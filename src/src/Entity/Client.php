<?php

namespace App\Entity;

use App\Entity\Trait\LifeTrait;
use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client
{
    use LifeTrait;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $email;

    /**
     * @var int
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $city;

    public function __construct(
        string $name,
        string $city,
        string $email,
        string $phone)
    {
        $this->name = $name;
        $this->city = $city;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone($phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCity(): ?int
    {
        return $this->city;
    }

    public function setCity(int $city): self
    {
        $this->city = $city;

        return $this;
    }
}
