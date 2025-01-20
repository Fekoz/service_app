<?php


namespace App\Util\Dto;


class MarketPlaceCharacterDto
{
    /**
     * @var MarketMappingDirectoryDto
     */
    private $other;

    /**
     * @var MarketMappingDirectoryDto
     */
    private $styles;

    /**
     * @var MarketMappingDirectoryDto
     */
    private $country;

    /**
     * @var MarketMappingDirectoryDto
     */
    private $typeCarpet;

    /**
     * @var MarketMappingDirectoryDto
     */
    private $typeBase;

    /**
     * @var MarketMappingDirectoryDto
     */
    private $fabric;

    /**
     * @var MarketMappingDirectoryDto
     */
    private $form;

    /**
     * @var MarketMappingDirectoryDto
     */
    private $color;

    /**
     * @return MarketMappingDirectoryDto
     */
    public function getOther(): MarketMappingDirectoryDto
    {
        return $this->other;
    }

    /**
     * @return MarketMappingDirectoryDto
     */
    public function getStyles(): MarketMappingDirectoryDto
    {
        return $this->styles;
    }

    /**
     * @return MarketMappingDirectoryDto
     */
    public function getCountry(): MarketMappingDirectoryDto
    {
        return $this->country;
    }

    /**
     * @return MarketMappingDirectoryDto
     */
    public function getTypeCarpet(): MarketMappingDirectoryDto
    {
        return $this->typeCarpet;
    }

    /**
     * @return MarketMappingDirectoryDto
     */
    public function getTypeBase(): MarketMappingDirectoryDto
    {
        return $this->typeBase;
    }

    /**
     * @return MarketMappingDirectoryDto
     */
    public function getFabric(): MarketMappingDirectoryDto
    {
        return $this->fabric;
    }

    /**
     * @return MarketMappingDirectoryDto
     */
    public function getForm(): MarketMappingDirectoryDto
    {
        return $this->form;
    }

    /**
     * @return MarketMappingDirectoryDto
     */
    public function getColor(): MarketMappingDirectoryDto
    {
        return $this->color;
    }

    /**
     * @param MarketMappingDirectoryDto $other
     * @return MarketPlaceCharacterDto
     */
    public function setOther(MarketMappingDirectoryDto $other): MarketPlaceCharacterDto
    {
        $this->other = $other;
        return $this;
    }

    /**
     * @param MarketMappingDirectoryDto $styles
     * @return MarketPlaceCharacterDto
     */
    public function setStyles(MarketMappingDirectoryDto $styles): MarketPlaceCharacterDto
    {
        $this->styles = $styles;
        return $this;
    }

    /**
     * @param MarketMappingDirectoryDto $country
     * @return MarketPlaceCharacterDto
     */
    public function setCountry(MarketMappingDirectoryDto $country): MarketPlaceCharacterDto
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @param MarketMappingDirectoryDto $typeCarpet
     * @return MarketPlaceCharacterDto
     */
    public function setTypeCarpet(MarketMappingDirectoryDto $typeCarpet): MarketPlaceCharacterDto
    {
        $this->typeCarpet = $typeCarpet;
        return $this;
    }

    /**
     * @param MarketMappingDirectoryDto $typeBase
     * @return MarketPlaceCharacterDto
     */
    public function setTypeBase(MarketMappingDirectoryDto $typeBase): MarketPlaceCharacterDto
    {
        $this->typeBase = $typeBase;
        return $this;
    }

    /**
     * @param MarketMappingDirectoryDto $fabric
     * @return MarketPlaceCharacterDto
     */
    public function setFabric(MarketMappingDirectoryDto $fabric): MarketPlaceCharacterDto
    {
        $this->fabric = $fabric;
        return $this;
    }

    /**
     * @param MarketMappingDirectoryDto $form
     * @return MarketPlaceCharacterDto
     */
    public function setForm(MarketMappingDirectoryDto $form): MarketPlaceCharacterDto
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @param MarketMappingDirectoryDto $color
     * @return MarketPlaceCharacterDto
     */
    public function setColor(MarketMappingDirectoryDto $color): MarketPlaceCharacterDto
    {
        $this->color = $color;
        return $this;
    }
}
