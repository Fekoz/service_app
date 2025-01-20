<?php

namespace App\Util\Dto;

class ListingCreateDto
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $uuid;

    public function __construct(string $url, string $uuid)
    {
        $this->url = $url;
        $this->uuid = $uuid;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
