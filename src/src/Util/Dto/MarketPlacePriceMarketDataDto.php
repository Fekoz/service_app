<?php


namespace App\Util\Dto;


class MarketPlacePriceMarketDataDto
{
    /**
     * @var MarketPlacePriceToMarketDto
     */
    private $ozon;

    /**
     * @var MarketPlacePriceToMarketDto
     */
    private $yandex;

    /**
     * @var MarketPlacePriceToMarketDto
     */
    private $wildberries;

    /**
     * @var MarketPlacePriceToMarketDto
     */
    private $original;

    /**
     * @var MarketPlacePriceToMarketDto
     */
    private $reformat;

    /**
     * @param MarketPlacePriceToMarketDto $ozon
     * @return MarketPlacePriceMarketDataDto
     */
    public function setOzon(MarketPlacePriceToMarketDto $ozon): MarketPlacePriceMarketDataDto
    {
        $this->ozon = $ozon;
        return $this;
    }

    /**
     * @return MarketPlacePriceToMarketDto
     */
    public function getOzon(): MarketPlacePriceToMarketDto
    {
        return $this->ozon;
    }

    /**
     * @param MarketPlacePriceToMarketDto $yandex
     * @return MarketPlacePriceMarketDataDto
     */
    public function setYandex(MarketPlacePriceToMarketDto $yandex): MarketPlacePriceMarketDataDto
    {
        $this->yandex = $yandex;
        return $this;
    }

    /**
     * @return MarketPlacePriceToMarketDto
     */
    public function getYandex(): MarketPlacePriceToMarketDto
    {
        return $this->yandex;
    }

    /**
     * @param MarketPlacePriceToMarketDto $wildberries
     * @return MarketPlacePriceMarketDataDto
     */
    public function setWildberries(MarketPlacePriceToMarketDto $wildberries): MarketPlacePriceMarketDataDto
    {
        $this->wildberries = $wildberries;
        return $this;
    }

    /**
     * @return MarketPlacePriceToMarketDto
     */
    public function getWildberries(): MarketPlacePriceToMarketDto
    {
        return $this->wildberries;
    }

    /**
     * @param MarketPlacePriceToMarketDto $original
     * @return MarketPlacePriceMarketDataDto
     */
    public function setOriginal(MarketPlacePriceToMarketDto $original): MarketPlacePriceMarketDataDto
    {
        $this->original = $original;
        return $this;
    }

    /**
     * @return MarketPlacePriceToMarketDto
     */
    public function getOriginal(): MarketPlacePriceToMarketDto
    {
        return $this->original;
    }

    /**
     * @param MarketPlacePriceToMarketDto $reformat
     * @return MarketPlacePriceMarketDataDto
     */
    public function setReformat(MarketPlacePriceToMarketDto $reformat): MarketPlacePriceMarketDataDto
    {
        $this->reformat = $reformat;
        return $this;
    }

    /**
     * @return MarketPlacePriceToMarketDto
     */
    public function getReformat(): MarketPlacePriceToMarketDto
    {
        return $this->reformat;
    }
}
