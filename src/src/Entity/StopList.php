<?php

namespace App\Entity;

use App\Entity\Trait\LifeTrait;
use App\Repository\StopListRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StopListRepository::class)
 */
class StopList
{
    use LifeTrait;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $ip;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $session;

    /**
     * @ORM\Column(type="datetime", name="unlock_at")
     */
    private $unlockAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $try;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(?string $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getDateUnlock(): ?\DateTimeInterface
    {
        return $this->unlockAt;
    }

    public function setDateUnlock(\DateTimeInterface $unlockAt): self
    {
        $this->unlockAt = $unlockAt;

        return $this;
    }

    public function getTry(): ?int
    {
        return $this->try;
    }

    public function setTry(int $try): self
    {
        $this->try = $try;

        return $this;
    }


}
