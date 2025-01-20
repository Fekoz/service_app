<?php

namespace App\Entity;

use App\Entity\Trait\LifeMiniTrait;
use App\Repository\CollectionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass=CollectionRepository::class)
 */
class Collection
{
    use LifeMiniTrait;

    /**
     * @ORM\Column(type="integer", unique=false, nullable=false)
     */
    private $inProductId;

    /**
     * @ORM\Column(type="integer", unique=false, nullable=false)
     */
    private $outProductId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $code;

    public function __construct(int $in, int $out, ?string $code)
    {
        $this->inProductId = $in;
        $this->outProductId = $out;
        $this->code = $code;
    }

    public function getInProductId(): ?int
    {
        return $this->inProductId;
    }

    public function setInProductId(int $inProductId): self
    {
        $this->inProductId = $inProductId;

        return $this;
    }

    public function getOutProductId(): ?int
    {
        return $this->outProductId;
    }

    public function setOutProductId(int $outProductId): self
    {
        $this->outProductId = $outProductId;

        return $this;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

}
