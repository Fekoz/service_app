<?php


namespace App\Util\Dto;


class MarketPlacePriceToMarketDto
{
    /**
     * @var float
     */
    private $old;

    /**
     * @var float
     */
    private $current;

    /**
     * @param float $old
     * @return MarketPlacePriceToMarketDto
     */
    public function setOld(float $old): MarketPlacePriceToMarketDto
    {
        $this->old = $old;
        return $this;
    }

    /**
     * @return float
     */
    public function getOld(): float
    {
        return $this->old;
    }

    /**
     * @param float $current
     * @return MarketPlacePriceToMarketDto
     */
    public function setCurrent(float $current): MarketPlacePriceToMarketDto
    {
        $this->current = $current;
        return $this;
    }

    /**
     * @return float
     */
    public function getCurrent(): float
    {
        return $this->current;
    }


}
