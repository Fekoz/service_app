<?php


namespace App\Util\Dto;


class SenderTelegramDto
{
    /**
     * @var string
     */
    private $message;

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return SenderTelegramDto
     */
    public function setMessage(string $message): SenderTelegramDto
    {
        $this->message = $message;
        return $this;
    }
}
