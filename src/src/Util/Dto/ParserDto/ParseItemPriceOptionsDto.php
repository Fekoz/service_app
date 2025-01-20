<?php


namespace App\Util\Dto\ParserDto;


class ParseItemPriceOptionsDto
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
     * @var bool
     */
    private $isPackage;

    /**
     * @var int
     */
    private $package;

    /**
     * @param int $width
     * @return ParseItemPriceOptionsDto
     */
    public function setWidth(int $width): ParseItemPriceOptionsDto
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $height
     * @return ParseItemPriceOptionsDto
     */
    public function setHeight(int $height): ParseItemPriceOptionsDto
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param bool $isPackage
     * @return ParseItemPriceOptionsDto
     */
    public function setIsPackage(bool $isPackage): ParseItemPriceOptionsDto
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
     * @param int $package
     * @return ParseItemPriceOptionsDto
     */
    public function setPackage(int $package): ParseItemPriceOptionsDto
    {
        $this->package = $package;
        return $this;
    }

    /**
     * @return int
     */
    public function getPackage(): int
    {
        return $this->package;
    }
}
