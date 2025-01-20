<?php


namespace App\Util\Dto\ParserDto;


class ParseItemDto
{
    /**
     * @var ParseItemOptionDto
     */
    private $option;

    /**
     * @var array<ParseItemSpecDto>
     */
    private $spec;

    /**
     * @var array<ParseItemImgDto>
     */
    private $img;

    /**
     * @var array<ParseItemPriceDto>
     */
    private $price;

    /**
     * @return ParseItemSpecDto[]
     */
    public function getSpec(): array
    {
        return $this->spec;
    }

    /**
     * @param ParseItemSpecDto[] $spec
     * @return ParseItemDto
     */
    public function setSpec(array $spec): ParseItemDto
    {
        $this->spec = $spec;
        return $this;
    }

    /**
     * @param ParseItemImgDto[] $img
     * @return ParseItemDto
     */
    public function setImg(array $img): ParseItemDto
    {
        $this->img = $img;
        return $this;
    }

    /**
     * @return ParseItemImgDto[]
     */
    public function getImg(): array
    {
        return $this->img;
    }

    /**
     * @return ParseItemPriceDto[]
     */
    public function getPrice(): array
    {
        return $this->price;
    }

    /**
     * @param ParseItemPriceDto[] $price
     * @return ParseItemDto
     */
    public function setPrice(array $price): ParseItemDto
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return ParseItemOptionDto
     */
    public function getOption(): ParseItemOptionDto
    {
        return $this->option;
    }

    /**
     * @param ParseItemOptionDto $option
     * @return ParseItemDto
     */
    public function setOption(ParseItemOptionDto $option): ParseItemDto
    {
        $this->option = $option;
        return $this;
    }

    public function clear(): void
    {
        $this->price = [];
        $this->spec = [];
        $this->img = [];
        if ($this->option) {
            $this->option->clear();
        }
    }
}
