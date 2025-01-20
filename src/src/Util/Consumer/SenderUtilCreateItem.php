<?php


namespace App\Util\Consumer;


use App\Entity\Product;
use App\Util\Dto\ParserDto\ParsePageItemDto;
use App\Util\Dto\SenderCreateItemDto;
use App\Util\Dto\SenderUtilOptionDto;
use PhpAmqpLib\Message\AMQPMessage;

class SenderUtilCreateItem implements SenderUtilInterface
{
    public static function read(SenderUtilOptionDto $option): \Closure
    {
        return function (AMQPMessage $message) use ($option) {
            $option->getParser()->clear();
            $option->getEntityManager()->clear();
            $param = null;
            try {
                $param = $option->getSerializer()->deserialize($message->body, SenderCreateItemDto::class, SenderUtilInterface::FORMAT);
            } catch (\Exception $e) { }

            if (!$param instanceof SenderCreateItemDto) {
                $option->getQueue()->acknowledge($message);
                return;
            }

            if (!$param->getUrl() || !$param->getUid()) {
                $option->getQueue()->acknowledge($message);
                return;
            }

            /**
             * @var $product Product
             */
            $product = $option->getEntityManager()->getRepository(Product::class)->findOneBy(['originalUrl' => $param->getUrl()]);
            if (!$product || !$product->getOriginalUrl()) {
                try {
                    $item = $option->getParser()->itemParse(
                        (new ParsePageItemDto)
                            ->setUuid($param->getUid())
                            ->setUrl($param->getUrl())
                    );
                } catch (\OAuthException $e) {
                    return;
                }

                if (!$item) {
                    $option->getQueue()->acknowledge($message);
                    return;
                }

                if ($param->getFactor()) {
                    $option->getValidator()->setDefaultFactor($param->getFactor());
                }

                $date = new \DateTime();
                $option->getValidator()->veneraImport($item, $date);

            }

            $option->getQueue()->acknowledge($message);
        };
    }
}
