<?php


namespace App\Util\Consumer;

use App\Util\Dto\SenderTelegramDto;
use App\Util\Dto\SenderUtilOptionDto;
use PhpAmqpLib\Message\AMQPMessage;

class SenderUtilTelegram implements SenderUtilInterface
{
    public static function read(SenderUtilOptionDto $option): \Closure
    {
        return function (AMQPMessage $message) use ($option) {
            $param = $option->getSerializer()->deserialize($message->body, SenderTelegramDto::class, SenderUtilInterface::FORMAT);
            if ($param instanceof SenderTelegramDto) {
                try {
                    $option->getBot()->message(null, $param->getMessage());
                } catch (\Exception $e) {

                }
            }
            $option->getQueue()->acknowledge($message);
        };
    }
}
