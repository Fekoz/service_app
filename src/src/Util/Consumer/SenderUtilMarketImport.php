<?php


namespace App\Util\Consumer;

use App\Entity\MarketSequence;
use App\Util\Dto\SenderMarketExportDto;
use App\Util\Dto\SenderUtilOptionDto;
use PhpAmqpLib\Message\AMQPMessage;

class SenderUtilMarketImport implements SenderUtilInterface
{
    public static function read(SenderUtilOptionDto $option): \Closure
    {
        return function (AMQPMessage $message) use ($option) {
            $param = null;

            try {
                $param = $option->getSerializer()->deserialize($message->body, SenderMarketExportDto::class, SenderUtilInterface::FORMAT);
            } catch (\Exception $e) {

            }

            if ($param instanceof SenderMarketExportDto) {
                try {
                    $ms = $option->getEntityManager()->getRepository(MarketSequence::class)->getMid($param->getMid());
                    if ($ms === null) {
                        $time = new \DateTime();
                        $ms = (new MarketSequence())
                            ->setActive(true)
                            ->setDisabled(false)
                            ->setCounter(true)
                            ->setName($param->getName())
                            ->setMid($param->getMid())
                            ->setCounterPkg($param->getCounterPkg())
                            ->setDateUpdate($time)
                            ->setDateCreate($time);

                        $option->getEntityManager()->persist($ms);
                        $option->getEntityManager()->flush();
                        $option->getEntityManager()->clear();
                    }
                } catch (\Exception $e) {
                    if(!$option->getEntityManager()->isOpen()) {
                        $option->getQueue()->acknowledge($message);
                        $option->getBot()->message(null, '[YML PCK]::Закрыто соединение с БД Процессом на авто-добавление записей YML пакетов. Ошибка: '. $e->getMessage());
                        die();
                    }
                }
            }

            $option->getQueue()->acknowledge($message);
        };
    }
}
