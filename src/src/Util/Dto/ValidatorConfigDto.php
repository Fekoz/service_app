<?php


namespace App\Util\Dto;


use App\Util\Dto\ParserDto\ParseItemPriceDto;

class ValidatorConfigDto
{
    /**
     * @var float
     */
    private $factor;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $originalUrl;

    /**
     * @var string
     */
    private $dir;

    /**
     * @var bool
     */
    private $isDownloadImage;

    /**
     * @var int
     */
    private $price;

    /**
     * @return float
     */
    public function getFactor(): float
    {
        return $this->factor;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getOriginalUrl(): string
    {
        return $this->originalUrl;
    }

    /**
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * @return bool
     */
    public function isDownloadImage(): bool
    {
        return $this->isDownloadImage;
    }

    /**
     * @param float $factor
     * @return ValidatorConfigDto
     */
    public function setFactor(float $factor): ValidatorConfigDto
    {
        $this->factor = $factor;
        return $this;
    }

    /**
     * @param string $uuid
     * @return ValidatorConfigDto
     */
    public function setUuid(string $uuid): ValidatorConfigDto
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @param string $originalUrl
     * @return ValidatorConfigDto
     */
    public function setOriginalUrl(string $originalUrl): ValidatorConfigDto
    {
        $this->originalUrl = $originalUrl;
        return $this;
    }

    /**
     * @param string $dir
     * @return ValidatorConfigDto
     */
    public function setDir(string $dir): ValidatorConfigDto
    {
        $this->dir = $dir;
        return $this;
    }

    /**
     * @param bool $isDownloadImage
     * @return ValidatorConfigDto
     */
    public function setIsDownloadImage(bool $isDownloadImage): ValidatorConfigDto
    {
        $this->isDownloadImage = $isDownloadImage;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     * @return ValidatorConfigDto
     */
    public function setPrice(int $price): ValidatorConfigDto
    {
        $this->price = $price;
        return $this;
    }

    public function clear(): void
    {
        $this->uuid = '';
        $this->originalUrl = '';
        $this->price = 0;
        $this->factor = 0;
        $this->dir = '';
        $this->isDownloadImage = true;
    }
}
