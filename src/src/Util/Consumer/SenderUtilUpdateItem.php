<?php


namespace App\Util\Consumer;


use App\Entity\Product;
use App\Util\Dto\ParserDto\ParseItemDto;
use App\Util\Dto\ParserDto\ParsePageItemDto;
use App\Util\Dto\SenderUpdateItemDto;
use App\Util\Dto\SenderUtilOptionDto;
use PhpAmqpLib\Message\AMQPMessage;

class SenderUtilUpdateItem implements SenderUtilInterface
{
    public static function read(SenderUtilOptionDto $option): \Closure
    {
        return function (AMQPMessage $message) use ($option) {
            $option->getParser()->clear();
            $option->getEntityManager()->clear();
            $param = null;
            try {
                $param = $option->getSerializer()->deserialize($message->body, SenderUpdateItemDto::class, SenderUtilInterface::FORMAT);
            } catch (\Exception $e) { }

            if (!$param instanceof SenderUpdateItemDto) {
                $option->getQueue()->acknowledge($message);
                return;
            }

            if (!$param->getId() || !$param->getStart() || !$param->getEnd()) {
                $option->getQueue()->acknowledge($message);
                return;
            }

            /**
             * @var Product $product
             */
            $product = $option->getEntityManager()->getRepository(Product::class)->find($param->getId());
            if (!$product) {
                $option->getQueue()->acknowledge($message);
                return;
            }

            $startTime = \DateTime::createFromFormat('Y-m-d H:i:s', $param->getStart());
            $endTime = \DateTime::createFromFormat('Y-m-d H:i:s', $param->getEnd());

            if (
                $product->getDateUpdate()->getTimestamp() >= $startTime->getTimestamp() &&
                $product->getDateUpdate()->getTimestamp() <= $endTime->getTimestamp()
            ) {
                $option->getQueue()->acknowledge($message);
                return;
            }

            $attempts = 0;
            $authFailed = false;

            $itemParse = null;
            while ($attempts < SenderUtilInterface::MAX_ITEM_UPDATE_ATTEMPTS) {
                try {
                    $itemParse = $option->getParser()->itemParse(
                        (new ParsePageItemDto)
                            ->setUrl($product->getOriginalUrl())
                            ->setUuid($product->getUuid())
                    );
                    break; // Если удалось выполнить itemParse успешно, выходим из цикла
                } catch (\OAuthException $exception) {
                    $option->getParser()->auth();
                    $attempts++;

                    if ($attempts >= SenderUtilInterface::MAX_ITEM_UPDATE_ATTEMPTS) {
                        $authFailed = true;
                        break; // Если исчерпаны все попытки авторизации, выходим из цикла
                    }
                }
            }

            if ($authFailed) {
                $option->getQueue()->acknowledge($message);
                return;
            }

            $time = new \DateTime();
            if ($itemParse instanceof ParseItemDto) {
                if ($param->getFactor()) {
                    $option->getValidator()->setDefaultFactor($param->getFactor());
                }

                $option->getValidator()->veneraImport($itemParse, $time);
                $option->getQueue()->acknowledge($message);
                return;
            }

            $product->setActive(false);
            $product->setDateUpdate($time);

            $option->getEntityManager()->persist($product);
            $option->getEntityManager()->flush();

            $option->getQueue()->acknowledge($message);
        };
    }
}
