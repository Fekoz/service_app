<?php


namespace App\Util\Dto;


class CategoryExpandDto
{
    /**
     * @var array<MarketYmlDtoCategoryParent>
     */
    private $categoriesParentDto;

    /**
     * @var array<MarketYmlDtoCategory>
     */
    private $categoriesDto;

    public function setCategoriesParentDto(array $categoriesParentDto): CategoryExpandDto
    {
        $this->categoriesParentDto = $categoriesParentDto;
        return $this;
    }

    public function getCategoriesParentDto(): array
    {
        return $this->categoriesParentDto;
    }

    public function setCategoriesDto(array $categoriesDto): CategoryExpandDto
    {
        $this->categoriesDto = $categoriesDto;
        return $this;
    }

    public function getCategoriesDto(): array
    {
        return $this->categoriesDto;
    }

    public function addCategoriesDto(MarketYmlDtoCategory $c): CategoryExpandDto
    {
        $this->categoriesDto[] = $c;
        return $this;
    }

    public function addCategoriesParentDto(MarketYmlDtoCategoryParent $c): CategoryExpandDto
    {
        $this->categoriesParentDto[] = $c;
        return $this;
    }
}
