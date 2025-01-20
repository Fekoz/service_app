<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\SituationTrait;

/**
 * @ORM\Entity(repositoryClass=ImagesRepository::class)
 */
class Images
{
    use SituationTrait;

    const IMAGE_BIG = true;
    const IMAGE_MINI = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dir;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=255, name="original_url")
     */
    private $originalUrl;

    /**
     * @ORM\Column(type="boolean")
     * TODO (true = large)
     */
    private $type;

    public function __construct() {
    }

    /**
     * @return string|null
     */
    public function getDir(): ?string
    {
        return $this->dir;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function setDir(string $dir): self
    {
        $this->dir = $dir;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return $this
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOriginalUrl(): ?string
    {
        return $this->originalUrl;
    }

    /**
     * @param string $originalUrl
     * @return $this
     */
    public function setOriginalUrl(string $originalUrl): self
    {
        $this->originalUrl = $originalUrl;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getType(): ?bool
    {
        return $this->type;
    }

    /**
     * @param bool $type
     * @return $this
     */
    public function setType(bool $type): self
    {
        $this->type = $type;

        return $this;
    }
}
