<?php

namespace App\Entity;

use App\Entity\Trait\LifeTrait;
use App\Repository\AuthRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Cache;

/**
 * @ORM\Entity(repositoryClass=AuthRepository::class)
 * @Cache(usage="READ_WRITE")
 */
class Auth
{
    use LifeTrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $session;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prefix;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $object;

    public function __construct(
        string $name,
        string $session,
        string $prefix,
        \DateTimeInterface $createAt,
        \DateTimeInterface $updateAt,
        string $object = null
    ) {
        $this->name = $name;
        $this->session = $session;
        $this->prefix = $prefix;
        $this->createAt = $createAt;
        $this->updateAt = $updateAt;
        $this->object = $object;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(string $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(?string $object): self
    {
        $this->object = $object;

        return $this;
    }
}
