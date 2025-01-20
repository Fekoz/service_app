<?php


namespace App\Util\Dto;


class MarketMappingDirectoryDto
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $value;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param int $id
     * @return MarketMappingDirectoryDto
     */
    public function setId(int $id): MarketMappingDirectoryDto
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $value
     * @return MarketMappingDirectoryDto
     */
    public function setValue(string $value): MarketMappingDirectoryDto
    {
        $this->value = $value;
        return $this;
    }
}
