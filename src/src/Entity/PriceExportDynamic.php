<?php

namespace App\Entity;

use App\Entity\Trait\LifeMiniTrait;
use App\Repository\PriceExportDynamicRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PriceExportDynamicRepository::class)
 */
class PriceExportDynamic
{
    use LifeMiniTrait;

    /**
     * @ORM\Column(type="integer")
     */
    private $min_width;

    /**
     * @ORM\Column(type="integer")
     */
    private $max_width;

    /**
     * @ORM\Column(type="integer")
     */
    private $min_height;

    /**
     * @ORM\Column(type="integer")
     */
    private $max_height;

    /**
     * @ORM\Column(type="integer")
     */
    private $package_width;

    /**
     * @ORM\Column(type="integer")
     */
    private $package_height;

    /**
     * @ORM\Column(type="integer")
     */
    private $package_depth;

    public function getMinWidth(): ?int
    {
        return $this->min_width;
    }

    public function setMinWidth(int $min_width): self
    {
        $this->min_width = $min_width;

        return $this;
    }

    public function getMaxWidth(): ?int
    {
        return $this->max_width;
    }

    public function setMaxWidth(int $max_width): self
    {
        $this->max_width = $max_width;

        return $this;
    }

    public function getMinHeight(): ?int
    {
        return $this->min_height;
    }

    public function setMinHeight(int $min_height): self
    {
        $this->min_height = $min_height;

        return $this;
    }

    public function getMaxHeight(): ?int
    {
        return $this->max_height;
    }

    public function setMaxHeight(int $max_height): self
    {
        $this->max_height = $max_height;

        return $this;
    }

    public function getPackageWidth(): ?int
    {
        return $this->package_width;
    }

    public function setPackageWidth(int $package_width): self
    {
        $this->package_width = $package_width;

        return $this;
    }

    public function getPackageHeight(): ?int
    {
        return $this->package_height;
    }

    public function setPackageHeight(int $package_height): self
    {
        $this->package_height = $package_height;

        return $this;
    }

    public function getPackageDepth(): ?int
    {
        return $this->package_depth;
    }

    public function setPackageDepth(int $package_depth): self
    {
        $this->package_depth = $package_depth;

        return $this;
    }
}
