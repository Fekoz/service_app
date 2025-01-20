<?php

namespace App\Util\Dto;

use Bukashk0zzz\YmlGenerator\Model\Category;

class MarketYmlDtoCategory extends Category
{
    /**
     * @var string
     */
    private $categoryKey;

    /**
     * @var string
     */
    private $categoryType;

    /**
     * @var string
     */
    private $categoryValue;

    /**
     * @var string
     */
    private $categoryName;

    /**
     * @var bool
     */
    private $isParent;

    public function getCategoryKey(): ?string
    {
        return $this->categoryKey;
    }

    public function setCategoryKey(?string $categoryKey): self
    {
        $this->categoryKey = $categoryKey;

        return $this;
    }

    public function getCategoryType(): ?string
    {
        return $this->categoryType;
    }

    public function setCategoryType(?string $categoryType): self
    {
        $this->categoryType = $categoryType;

        return $this;
    }

    public function getCategoryValue(): ?string
    {
        return $this->categoryValue;
    }

    public function setCategoryValue(?string $categoryValue): self
    {
        $this->categoryValue = $categoryValue;

        return $this;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(?string $categoryName): self
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    public function isParent(): ?bool
    {
        return $this->isParent;
    }

    public function setParent(?bool $isParent): self
    {
        $this->isParent = $isParent;

        return $this;
    }

}
