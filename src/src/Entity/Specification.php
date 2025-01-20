<?php

namespace App\Entity;

use App\Repository\SpecificationRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\SituationTrait;

/**
 * @ORM\Entity(repositoryClass=SpecificationRepository::class)
 */
class Specification
{
    use SituationTrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    public function __construct()
    {

    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
