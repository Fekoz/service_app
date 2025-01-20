<?php


namespace App\Util\Dto;


class CollectionCCRepoDTO
{

    /**
     * @var int | null
     */
    private $id;

    /**
     * @var string | null
     */
    private $code;

    /**
     * @var string | null
     */
    private $name;

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

}
