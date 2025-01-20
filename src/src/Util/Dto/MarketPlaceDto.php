<?php


namespace App\Util\Dto;


class MarketPlaceDto extends MarketYmlDtoOfferSimple
{
    /**
     * @var string
     */
    private $linkedName;

    /**
     * @var MarketPlaceCharacterDto
     */
    private $character;

    /**
     * @var TwistSizeDto
     */
    private $statCarpet;

    /**
     * @var array<string>
     */
    private $categoriesName;

    /**
     * @var array<string>
     */
    private $linkedItemIds;

    /**
     * @var MarketPlacePriceMarketDataDto
     */
    private $priceToMarket;

    /**
     * @var int
     */
    private $number;

    /**
     * @var array<int>
     */
    private $linkedItemNumbers;

    /**
     * @var MarketPlaceLiveDateDto
     */
    private $liveDate;

    /**
     * @param MarketPlaceCharacterDto $character
     * @return MarketPlaceDto
     */
    public function setCharacter(MarketPlaceCharacterDto $character): MarketPlaceDto
    {
        $this->character = $character;
        return $this;
    }

    /**
     * @return MarketPlaceCharacterDto
     */
    public function getCharacter(): MarketPlaceCharacterDto
    {
        return $this->character;
    }

    /**
     * @param string $linkedName
     * @return MarketPlaceDto
     */
    public function setLinkedName(string $linkedName): MarketPlaceDto
    {
        $this->linkedName = $linkedName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLinkedName(): string
    {
        return $this->linkedName;
    }

    /**
     * @param TwistSizeDto $statCarpet
     * @return MarketPlaceDto
     */
    public function setStatCarpet(TwistSizeDto $statCarpet): MarketPlaceDto
    {
        $this->statCarpet = $statCarpet;
        return $this;
    }

    /**
     * @return TwistSizeDto
     */
    public function getStatCarpet(): TwistSizeDto
    {
        return $this->statCarpet;
    }

    /**
     * @param string[] $categoriesName
     * @return MarketPlaceDto
     */
    public function setCategoriesName(array $categoriesName): MarketPlaceDto
    {
        $this->categoriesName = $categoriesName;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getCategoriesName(): array
    {
        return $this->categoriesName;
    }

    /**
     * @param string[] $linkedItemIds
     * @return MarketPlaceDto
     */
    public function setLinkedItemIds(array $linkedItemIds): MarketPlaceDto
    {
        $this->linkedItemIds = $linkedItemIds;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getLinkedItemIds(): array
    {
        return $this->linkedItemIds;
    }

    /**
     * @param MarketPlacePriceMarketDataDto $priceToMarket
     * @return MarketPlaceDto
     */
    public function setPriceToMarket(MarketPlacePriceMarketDataDto $priceToMarket): MarketPlaceDto
    {
        $this->priceToMarket = $priceToMarket;
        return $this;
    }

    /**
     * @return MarketPlacePriceMarketDataDto
     */
    public function getPriceToMarket(): MarketPlacePriceMarketDataDto
    {
        return $this->priceToMarket;
    }

    /**
     * @param int $number
     * @return MarketPlaceDto
     */
    public function setNumber(int $number): MarketPlaceDto
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @return int[]
     */
    public function getLinkedItemNumbers(): array
    {
        return $this->linkedItemNumbers;
    }

    /**
     * @param int[] $linkedItemNumbers
     * @return MarketPlaceDto
     */
    public function setLinkedItemNumbers(array $linkedItemNumbers): MarketPlaceDto
    {
        $this->linkedItemNumbers = $linkedItemNumbers;
        return $this;
    }

    /**
     * @return MarketPlaceLiveDateDto
     */
    public function getLiveDate(): MarketPlaceLiveDateDto
    {
        return $this->liveDate;
    }

    /**
     * @param MarketPlaceLiveDateDto $liveDate
     * @return MarketPlaceDto
     */
    public function setLiveDate(MarketPlaceLiveDateDto $liveDate): MarketPlaceDto
    {
        $this->liveDate = $liveDate;
        return $this;
    }

}
