<?php


namespace App\Util\Dto;


class OfferReadMarketResponse
{
    /**
     * @var array<OfferReadMarketOffer>
     */
    private $offers;

    /**
     * @var integer
     */
    private $current;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var integer
     */
    private $total;

    /**
     * @return OfferReadMarketOffer[]
     */
    public function getOffers(): array
    {
        return $this->offers;
    }

    /**
     * @param OfferReadMarketOffer[] $offers
     * @return OfferReadMarketResponse
     */
    public function setOffers(array $offers): OfferReadMarketResponse
    {
        $this->offers = $offers;
        return $this;
    }

    /**
     * @param OfferReadMarketOffer $offer
     * @return OfferReadMarketResponse
     */
    public function addOffers(OfferReadMarketOffer $offer): OfferReadMarketResponse
    {
        $this->offers[] = $offer;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrent(): int
    {
        return $this->current;
    }

    /**
     * @param int $current
     * @return OfferReadMarketResponse
     */
    public function setCurrent(int $current): OfferReadMarketResponse
    {
        $this->current = $current;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return OfferReadMarketResponse
     */
    public function setSize(int $size): OfferReadMarketResponse
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     * @return OfferReadMarketResponse
     */
    public function setTotal(int $total): OfferReadMarketResponse
    {
        $this->total = $total;
        return $this;
    }
}
