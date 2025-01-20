<?php


namespace App\Util\Dto;


class MarketPriceDto
{
    /**
     * @var MarketPriceObjectDto
     */
    private $price;

    /**
     * @var MarketPriceObjectDto
     */
    private $priceOld;

    public function getPrice(): MarketPriceObjectDto
    {
        return $this->price;
    }

    public function setPrice(MarketPriceObjectDto $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceOld(): MarketPriceObjectDto
    {
        return $this->priceOld;
    }

    public function setPriceOld(MarketPriceObjectDto $priceOld): self
    {
        $this->priceOld = $priceOld;

        return $this;
    }
}
