<?php


namespace App\Util\Dto;


use App\Entity\MarketMappingProperty;

class MarketMappingDto
{
    /**
     * @var MarketMappingDirectoryDto
     */
    private $direct;

    /**
     * @var string
     */
    private $key;

    /**
     * @var array<string>
     */
    private $values;

    /**
     * @var MarketMappingDirectoryDto
     */
    private $ozon;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return MarketMappingDto
     */
    public function setKey(string $key): MarketMappingDto
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param string[] $values
     * @return MarketMappingDto
     */
    public function setValues(array $values): MarketMappingDto
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @return MarketMappingDirectoryDto
     */
    public function getOzon(): MarketMappingDirectoryDto
    {
        return $this->ozon;
    }

    /**
     * @param MarketMappingDirectoryDto $ozon
     * @return MarketMappingDto
     */
    public function setOzon(MarketMappingDirectoryDto $ozon): MarketMappingDto
    {
        $this->ozon = $ozon;
        return $this;
    }

    /**
     * @return MarketMappingDirectoryDto
     */
    public function getDirect(): MarketMappingDirectoryDto
    {
        return $this->direct;
    }

    /**
     * @param MarketMappingDirectoryDto $direct
     * @return MarketMappingDto
     */
    public function setDirect(MarketMappingDirectoryDto $direct): MarketMappingDto
    {
        $this->direct = $direct;
        return $this;
    }

}
