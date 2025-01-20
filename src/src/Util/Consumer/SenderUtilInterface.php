<?php


namespace App\Util\Consumer;


use App\Util\Dto\SenderUtilOptionDto;

interface SenderUtilInterface
{
    const FORMAT = 'json';

    const MAX_ITEM_UPDATE_ATTEMPTS = 3;

    public static function read(SenderUtilOptionDto $option): \Closure;
}
