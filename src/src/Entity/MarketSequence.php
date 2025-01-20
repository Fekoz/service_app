<?php

namespace App\Entity;

use App\Entity\Trait\LifeTrait;
use App\Repository\MarketSequenceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MarketSequenceRepository::class)
 */
class MarketSequence
{
    use LifeTrait;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $mid;

    /**
     * @ORM\Column(type="boolean", name="is_active", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDisabled;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isCounter;

    /**
     * @ORM\Column(type="integer", length=4)
     */
    private $counterPkg;

    public function getMid(): ?string
    {
        return $this->mid;
    }

    public function setMid(string $mid): self
    {
        $this->mid = $mid;

        return $this;
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

    public function isDisabled(): ?bool
    {
        return $this->isDisabled;
    }

    public function setDisabled(bool $isDisabled): self
    {
        $this->isDisabled = $isDisabled;

        return $this;
    }

    public function isCounter(): ?bool
    {
        return $this->isCounter;
    }

    public function setCounter(bool $isCounter): self
    {
        $this->isCounter = $isCounter;

        return $this;
    }

    public function getCounterPkg(): ?int
    {
        return $this->counterPkg;
    }

    public function setCounterPkg(int $counterPkg): self
    {
        $this->counterPkg = $counterPkg;

        return $this;
    }
}
