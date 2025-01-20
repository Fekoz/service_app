<?php

namespace App\Entity;

use App\Entity\Trait\LifeTrait;
use App\Repository\FieldRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Cache;

/**
 * @ORM\Entity(repositoryClass=FieldRepository::class)
 * @Cache(usage="READ_WRITE")
 */
class Field
{
    use LifeTrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $param;

    /**
     * @ORM\Column(type="integer")
     */
    private $max;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMade;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $export;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getParam(): ?string
    {
        return $this->param;
    }

    public function setParam(?string $param): self
    {
        $this->param = $param;

        return $this;
    }

    public function isMade(): ?bool
    {
        return $this->isMade;
    }

    public function setMade(bool $isMade): self
    {
        $this->isMade = $isMade;

        return $this;
    }

    public function getExport(): ?string
    {
        return $this->export;
    }

    public function setExport(?string $export): self
    {
        $this->export = $export;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(int $max): self
    {
        $this->max = $max;

        return $this;
    }
}
