<?php


namespace App\Util\Dto;


class OfferReadMarketOffer
{
    private $url;
    private $price;
    private $id;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return OfferReadMarketOffer
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return OfferReadMarketOffer
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return OfferReadMarketOffer
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
