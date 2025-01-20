<?php


namespace App\Util\Consumer;

use App\Util\Dto\SenderEmailDto;
use App\Util\Dto\SenderUtilOptionDto;
use PhpAmqpLib\Message\AMQPMessage;

class SenderUtilEmail implements SenderUtilInterface
{
    public static function read(SenderUtilOptionDto $option): \Closure
    {
        return function (AMQPMessage $message) use ($option) {
            $param = $option->getSerializer()->deserialize($message->body, SenderEmailDto::class, SenderUtilInterface::FORMAT);
            if ($param instanceof SenderEmailDto) {
                try {
                    $option->getSenderService()->message(
                        $param->getTo(),
                        $param->getTitle(),
                        $param->getMessage()
                    );
                } catch (\Exception $e) { }
            }
            $option->getQueue()->acknowledge($message);
        };
    }
}
