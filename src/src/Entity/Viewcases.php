<?php

namespace App\Entity;

use App\Entity\Trait\LifeTrait;
use App\Repository\ViewcasesRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass=ViewcasesRepository::class)
 */
class Viewcases
{
    use LifeTrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @var array
     * @ORM\Column(type="json", nullable=true)
     */
    private $list;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive($isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPriceList(): ?array
    {
        return $this->list;
    }

    public function setPriceList(array $list): self
    {
        $this->list = $list;

        return $this;
    }

}
