<?php

namespace App\Entity;

use App\Entity\Trait\LifeTrait;
use App\Repository\ShowcasesRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass=ShowcasesRepository::class)
 */
class Showcases
{
    use LifeTrait;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $uuid;

    /**
     * @ManyToOne(targetEntity="App\Entity\Admin")
     * @JoinColumn(name="admin_id", referencedColumnName="id")
     */
    private $admin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @var array
     * @ORM\Column(type="json", nullable=true)
     */
    private $priceList;

    /**
     * @OneToOne(targetEntity="App\Entity\Order")
     * @JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    public function __construct()
    {
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(Admin $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive($isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPriceList(): ?array
    {
        return $this->priceList;
    }

    public function setPriceList(array $priceList): self
    {
        $this->priceList = $priceList;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }
}
