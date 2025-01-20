<?php

namespace App\Entity;

use App\Entity\Trait\LifeMiniTrait;
use App\Repository\MailTemplateRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Cache;

/**
 * @ORM\Entity(repositoryClass=MailTemplateRepository::class)
 * @Cache(usage="READ_ONLY")
 */
class MailTemplate
{
    use LifeMiniTrait;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
