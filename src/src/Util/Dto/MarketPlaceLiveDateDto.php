<?php


namespace App\Util\Dto;


class MarketPlaceLiveDateDto
{
    /**
     * @var string
     */
    private $at;

    /**
     * @var string
     */
    private $to;

    /**
     * @return string
     */
    public function getAt(): string
    {
        return $this->at;
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @param string $at
     * @return MarketPlaceLiveDateDto
     */
    public function setAt(string $at): MarketPlaceLiveDateDto
    {
        $this->at = $at;
        return $this;
    }

    /**
     * @param string $to
     * @return MarketPlaceLiveDateDto
     */
    public function setTo(string $to): MarketPlaceLiveDateDto
    {
        $this->to = $to;
        return $this;
    }


}
