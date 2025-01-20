<?php

namespace App\Service;

use App\Util\Constant;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class Bot
{
    /**
     * @var $param array
     */
    private $param;

    /**
     * @var BotApi
     */
    private $bot;

    /**
     * @var array
     */
    private $botConfig;

    public function __construct(ParameterBagInterface $param)
    {
        $this->param = $param->get(Constant::CONFIG_NAME[__CLASS__]);
    }

    public function init(string $botName)
    {
        $botConfig = $this->param[$botName] ?? null;
        if (!$botConfig) {
            return;
        }

        $this->botConfig = $botConfig;
        $this->bot = new BotApi($botConfig['token']);
    }

    /**
     * @param string|null $chat
     * @param string $message
     */
    public function message(?string $chat, string $message)
    {
        if (!$chat) {
            $chat = $this->botConfig['group_telegram_id'];
        }
        try {
            $this->bot->sendMessage($chat, $message);
        } catch (InvalidArgumentException | \Exception $e) {
            return;
        }
    }


    /**
     * @param string|null $chat
     * @param string $document
     */
    public function document(?string $chat, string $document)
    {
        if (!$chat) {
            $chat = $this->botConfig['group_telegram_id'];
        }
        try {
            $document = new \CURLFile($document);
            $this->bot->sendDocument($chat, $document);
        } catch (InvalidArgumentException | \Exception $e) {
            return;
        }
    }

}
