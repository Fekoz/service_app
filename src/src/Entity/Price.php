<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\SituationTrait;

/**
 * @ORM\Entity(repositoryClass=PriceRepository::class)
 */
class Price
{
    use SituationTrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $width;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $height;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $meter;

    /**
     * @ORM\Column(type="float", name="old_price")
     */
    private $oldPrice;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mid;

    /**
     * @ORM\Column(type="integer")
     */
    private $storage;

    public function __construct() {
    }

    /**
     * @return string|null
     */
    public function getWidth(): ?string
    {
        return $this->width;
    }

    /**
     * @param string $width
     * @return $this
     */
    public function setWidth(string $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHeight(): ?string
    {
        return $this->height;
    }

    /**
     * @param string $height
     * @return $this
     */
    public function setHeight(string $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return $this
     */
    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMeter(): ?int
    {
        return $this->meter;
    }

    /**
     * @param int|null $meter
     * @return $this
     */
    public function setMeter(int $meter): self
    {
        $this->meter = $meter;

        return $this;
    }

    /**
     * @param float|null $oldPrice
     * @return $this
     */
    public function setOldPrice(?float $oldPrice): self
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getOldPrice(): ?float
    {
        return $this->oldPrice;
    }

    /**
     * @param string $uuid
     * @return $this
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string $mid
     * @return $this
     */
    public function setMid(string $mid): self
    {
        $this->mid = $mid;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMid(): ?string
    {
        return $this->mid;
    }

    /**
     * @return int|null
     */
    public function getStorage(): ?int
    {
        return $this->storage;
    }

    /**
     * @param int $storage
     * @return $this
     */
    public function setStorage(int $storage): self
    {
        $this->storage = $storage;

        return $this;
    }
}
