<?php


namespace App\Util\Dto;


class SenderEmailDto
{
    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $message;

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $to
     * @return SenderEmailDto
     */
    public function setTo(string $to): SenderEmailDto
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @param string $title
     * @return SenderEmailDto
     */
    public function setTitle(string $title): SenderEmailDto
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $message
     * @return SenderEmailDto
     */
    public function setMessage(string $message): SenderEmailDto
    {
        $this->message = $message;
        return $this;
    }

}
