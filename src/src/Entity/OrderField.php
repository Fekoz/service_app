<?php

namespace App\Entity;

use App\Repository\OrderFieldRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass=OrderFieldRepository::class)
 * @ORM\Table(name="`order_field`")
 */
class OrderField
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $key;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ManyToOne(targetEntity="App\Entity\Order", inversedBy="fields")
     * @JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey($key): self
    {
        $this->key = $key;

        return $this;
    }


    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue($value): ?self
    {
        $this->value = $value;

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
