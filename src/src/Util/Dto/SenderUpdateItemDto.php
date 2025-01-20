<?php


namespace App\Util\Dto;


class SenderUpdateItemDto
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $start;

    /**
     * @var string|null
     */
    private $end;

    /**
     * @var float|null
     */
    private $factor;

    /**
     * @param float|null $factor
     * @return SenderUpdateItemDto
     */
    public function setFactor(?float $factor): SenderUpdateItemDto
    {
        $this->factor = $factor;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getFactor(): ?float
    {
        return $this->factor;
    }

    /**
     * @return string|null
     */
    public function getStart(): ?string
    {
        return $this->start;
    }

    /**
     * @param string|null $start
     * @return SenderUpdateItemDto
     */
    public function setStart(?string $start): SenderUpdateItemDto
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEnd(): ?string
    {
        return $this->end;
    }

    /**
     * @param string|null $end
     * @return SenderUpdateItemDto
     */
    public function setEnd(?string $end): SenderUpdateItemDto
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @param int|null $id
     * @return SenderUpdateItemDto
     */
    public function setId(?int $id): SenderUpdateItemDto
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

}
