<?php


namespace App\Util\Dto;


use App\Entity\MarketMappingProperty;

class MarketPlaceOutputDirectoryOzonDto
{
    /**
     * @var array<MarketMappingProperty>
     */
    private $result;

    /**
     * @var bool
     */
    private $has_next;

    public function getResult(): ?array
    {
        return $this->result;
    }

    public function setResult(array $result): MarketPlaceOutputDirectoryOzonDto
    {
        $this->result = $result;

        return $this;
    }

    public function isHasNext(): bool
    {
        return $this->has_next;
    }

    public function setHasNext(bool $has_next): MarketPlaceOutputDirectoryOzonDto
    {
        $this->has_next = $has_next;

        return $this;
    }
}
