<?php

namespace App\Entity;

use App\Entity\Trait\LifeTrait;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;


/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    use LifeTrait;

    /**
     * @ManyToOne(targetEntity="App\Entity\Client")
     * @JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @OneToMany(targetEntity="App\Entity\OrderField", mappedBy="order")
     */
    private $fields;

    /**
     * @ORM\Column(type="integer", length=4)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", length=2)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $uuid;

    /**
     * @ManyToOne(targetEntity="App\Entity\Presents")
     * @JoinColumn(name="presents_id", referencedColumnName="id")
     */
    private $presents;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): ?self
    {
        $this->status = $status;

        return $this;
    }

    public function getPresent(): ?Presents
    {
        return $this->presents;
    }

    public function setPresent(Presents $presents): self
    {
        $this->presents = $presents;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

}
