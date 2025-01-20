<?php


namespace App\Util\Dto\ParserDto;


class ParseItemOptionDto
{
    /**
     * @var int
     */
    private $price;

    /**
     * @var array
     */
    private $priceParseList;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $url;

    /**
     * @param int $price
     * @return ParseItemOptionDto
     */
    public function setPrice(int $price): ParseItemOptionDto
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param array $priceParseList
     * @return ParseItemOptionDto
     */
    public function setPriceParseList(array $priceParseList): ParseItemOptionDto
    {
        $this->priceParseList = $priceParseList;
        return $this;
    }

    /**
     * @return array
     */
    public function getPriceParseList(): array
    {
        return $this->priceParseList;
    }

    /**
     * @param string $uuid
     * @return ParseItemOptionDto
     */
    public function setUuid(string $uuid): ParseItemOptionDto
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return ParseItemOptionDto
     */
    public function setUrl(string $url): ParseItemOptionDto
    {
        $this->url = $url;
        return $this;
    }

    public function clear(): void
    {
        $this->price = 0;
        $this->url = '';
        $this->uuid = '';
        $this->priceParseList = [];
    }
}
