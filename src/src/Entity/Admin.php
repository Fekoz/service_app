<?php

namespace App\Entity;

use App\Entity\Trait\LifeTrait;
use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 */
class Admin
{
    use LifeTrait;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $pass;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $protection;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     */
    private $role;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function __construct(
        string $login,
        string $pass,
        string $email,
        int $protection,
        int $role,
        bool $isActive)
    {
        $this->login = $login;
        $this->pass = $pass;
        $this->protection = $protection;
        $this->role = $role;
        $this->isActive = $isActive;
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return $this
     */
    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPass(): ?string
    {
        return $this->pass;
    }

    /**
     * @param string $pass
     * @return $this
     */
    public function setPass(string $pass): self
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getProtection(): ?int
    {
        return $this->protection;
    }

    /**
     * @param int $protection
     * @return $this
     */
    public function setProtection(int $protection): self
    {
        $this->protection = $protection;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRole(): ?int
    {
        return $this->role;
    }

    /**
     * @param int $role
     * @return $this
     */
    public function setRole(int $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
