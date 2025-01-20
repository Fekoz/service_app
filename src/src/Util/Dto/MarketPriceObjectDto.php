<?php


namespace App\Util\Dto;


class MarketPriceObjectDto
{
    /**
     * @var float
     */
    private $factor;

    /**
     * @var float
     */
    private $markup;

    /**
     * @var bool
     */
    private $isRandom;

    /**
     * @var int
     */
    private $rndMin;

    /**
     * @var int
     */
    private $rndMax;

    /**
     * @var float
     */
    private $ozonFactor;

    /**
     * @var float
     */
    private $yandexFactor;

    /**
     * @var float
     */
    private $wildberriesFactor;

    public function getFactor(): float
    {
        return $this->factor;
    }

    public function setFactor(float $factor): self
    {
        $this->factor = $factor;

        return $this;
    }

    public function getMarkup(): float
    {
        return $this->markup;
    }

    public function setMarkup(float $markup): self
    {
        $this->markup = $markup;

        return $this;
    }

    public function isRandom(): bool
    {
        return $this->isRandom;
    }

    public function setRandom(bool $isRandom): self
    {
        $this->isRandom = $isRandom;

        return $this;
    }

    public function getRndMin(): int
    {
        return $this->rndMin;
    }

    public function setRndMin(int $rndMin): self
    {
        $this->rndMin = $rndMin;

        return $this;
    }

    public function getRndMax(): int
    {
        return $this->rndMax;
    }

    public function setRndMax(int $rndMax): self
    {
        $this->rndMax = $rndMax;

        return $this;
    }

    public function setOzonFactor(float $ozonFactor): self
    {
        $this->ozonFactor = $ozonFactor;

        return $this;
    }

    public function getOzonFactor(): float
    {
        return $this->ozonFactor;
    }

    public function setYandexFactor(float $yandexFactor): self
    {
        $this->yandexFactor = $yandexFactor;

        return $this;
    }

    public function getYandexFactor(): float
    {
        return $this->yandexFactor;
    }

    public function setWildberriesFactor(float $wildberriesFactor): self
    {
        $this->wildberriesFactor = $wildberriesFactor;
        return $this;
    }

    public function getWildberriesFactor(): float
    {
        return $this->wildberriesFactor;
    }
}
