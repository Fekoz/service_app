<?php


namespace App\Util\Dto\ParserDto;


class ParseItemPriceDto
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $price;

    /**
     * @var int
     */
    private $storage;

    /**
     * @var bool
     */
    private $isPackage;

    /**
     * @var int
     */
    private $package;

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getStorage(): int
    {
        return $this->storage;
    }

    /**
     * @param int $width
     * @return ParseItemPriceDto
     */
    public function setWidth(int $width): ParseItemPriceDto
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @param int $height
     * @return ParseItemPriceDto
     */
    public function setHeight(int $height): ParseItemPriceDto
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @param int $count
     * @return ParseItemPriceDto
     */
    public function setCount(int $count): ParseItemPriceDto
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @param int $price
     * @return ParseItemPriceDto
     */
    public function setPrice(int $price): ParseItemPriceDto
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @param int $storage
     * @return ParseItemPriceDto
     */
    public function setStorage(int $storage): ParseItemPriceDto
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @param bool $isPackage
     * @return ParseItemPriceDto
     */
    public function setIsPackage(bool $isPackage): ParseItemPriceDto
    {
        $this->isPackage = $isPackage;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPackage(): bool
    {
        return $this->isPackage;
    }

    /**
     * @return int
     */
    public function getPackage(): int
    {
        return $this->package;
    }

    /**
     * @param int $package
     * @return ParseItemPriceDto
     */
    public function setPackage(int $package): ParseItemPriceDto
    {
        $this->package = $package;
        return $this;
    }

}
