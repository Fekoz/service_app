<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\LifeTrait;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    use LifeTrait;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $article;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255, name="full_price")
     */
    private $fullPrice;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $factor;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $factorFull;

    /**
     * @ORM\Column(type="string", length=255, name="original_url")
     */
    private $originalUrl;

    // @TODO: Отключение авто-лока при создании.
    /**
     * @ORM\Column(type="boolean", name="is_product_lock", nullable=true)
     */
    private $isProductLock;

    /**
     * @ORM\Column(type="boolean", name="is_price_lock", nullable=true)
     */
    private $isPriceLock;

    /**
     * @ORM\Column(type="boolean", name="is_specification_lock", nullable=true)
     */
    private $isSpecificationLock;

    /**
     * @ORM\Column(type="boolean", name="is_images_lock", nullable=true)
     */
    private $isImagesLock;

    /**
     * @ORM\Column(type="boolean", name="is_attribute_lock", nullable=true)
     */
    private $isAttributeLock;

    /**
     * @ORM\Column(type="boolean", name="is_global_update_lock", nullable=true)
     */
    private $isGlobalUpdateLock;

    public function __construct() {
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }


    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function setArticle(string $article): self
    {
        $this->article = $article;

        return $this;
    }


    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }


    public function getFullPrice(): ?string
    {
        return $this->fullPrice;
    }

    public function setFullPrice(string $fullPrice): self
    {
        $this->fullPrice = $fullPrice;

        return $this;
    }


    public function getFactor(): ?int
    {
        return $this->factor;
    }

    public function setFactor(int $factor): self
    {
        $this->factor = $factor;

        return $this;
    }


    public function getFactorFull(): ?float
    {
        return $this->factorFull;
    }

    public function setFactorFull(float $factorFull): self
    {
        $this->factorFull = $factorFull;

        return $this;
    }


    public function getOriginalUrl(): ?string
    {
        return $this->originalUrl;
    }

    public function setOriginalUrl(string $originalUrl): self
    {
        $this->originalUrl = $originalUrl;

        return $this;
    }

    // Locker's trigger

    public function isProductLock(): ?bool
    {
        return $this->isProductLock;
    }

    public function setProductLock(bool $isProductLock): self
    {
        $this->isProductLock = $isProductLock;

        return $this;
    }

    public function isPriceLock(): ?bool
    {
        return $this->isPriceLock;
    }

    public function setPriceLock(bool $isPriceLock): self
    {
        $this->isPriceLock = $isPriceLock;

        return $this;
    }

    public function isSpecificationLock(): ?bool
    {
        return $this->isSpecificationLock;
    }

    public function setSpecificationLock(bool $isSpecificationLock): self
    {
        $this->isSpecificationLock = $isSpecificationLock;

        return $this;
    }

    public function isImagesLock(): ?bool
    {
        return $this->isImagesLock;
    }

    public function setImagesLock(bool $isImagesLock): self
    {
        $this->isImagesLock = $isImagesLock;

        return $this;
    }

    public function isAttributeLock(): ?bool
    {
        return $this->isAttributeLock;
    }

    public function setAttributeLock(bool $isAttributeLock): self
    {
        $this->isAttributeLock = $isAttributeLock;

        return $this;
    }

    public function isGlobalUpdateLock(): ?bool
    {
        return $this->isGlobalUpdateLock;
    }

    public function setGlobalUpdateLock(bool $isGlobalUpdateLock): self
    {
        $this->isGlobalUpdateLock = $isGlobalUpdateLock;

        return $this;
    }

}
