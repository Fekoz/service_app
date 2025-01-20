<?php


namespace App\Util\Dto;


class SenderCreateItemDto
{
    /**
     * @var string
     */
    private $uid;

    /**
     * @var string
     */
    private $url;

    /**
     * @var float|null
     */
    private $factor;

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     * @return SenderCreateItemDto
     */
    public function setUid(string $uid): SenderCreateItemDto
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return SenderCreateItemDto
     */
    public function setUrl(string $url): SenderCreateItemDto
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getFactor(): ?float
    {
        return $this->factor;
    }

    /**
     * @param float|null $factor
     * @return SenderCreateItemDto
     */
    public function setFactor(?float $factor): SenderCreateItemDto
    {
        $this->factor = $factor;
        return $this;
    }
}
