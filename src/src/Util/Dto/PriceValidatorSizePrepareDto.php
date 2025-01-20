<?php


namespace App\Util\Dto;


class PriceValidatorSizePrepareDto
{
    /**
     * @var float
     */
    private $w;

    /**
     * @var float
     */
    private $h;

    /**
     * @var int
     */
    private $count;

    /**
     * @param float $w
     * @return PriceValidatorSizePrepareDto
     */
    public function setW(float $w): PriceValidatorSizePrepareDto
    {
        $this->w = $w;
        return $this;
    }

    /**
     * @param float $h
     * @return PriceValidatorSizePrepareDto
     */
    public function setH(float $h): PriceValidatorSizePrepareDto
    {
        $this->h = $h;
        return $this;
    }

    /**
     * @param int $count
     * @return PriceValidatorSizePrepareDto
     */
    public function setCount(int $count): PriceValidatorSizePrepareDto
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return float
     */
    public function getW(): float
    {
        return $this->w;
    }

    /**
     * @return float
     */
    public function getH(): float
    {
        return $this->h;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }


}
