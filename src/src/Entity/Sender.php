<?php

namespace App\Entity;

use App\Entity\Trait\LifeMiniTrait;
use App\Repository\SenderRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass=SenderRepository::class)
 */
class Sender
{
    use LifeMiniTrait;

    /**
     * @ManyToOne(targetEntity="App\Entity\Client")
     * @JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @Cache("NONSTRICT_READ_WRITE")
     * @ManyToOne(targetEntity="App\Entity\MailTemplate")
     * @JoinColumn(name="mail_template_id", referencedColumnName="id")
     */
    private $mailTemplate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSend;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    /**
     * @ORM\Column(type="integer", length=4)
     */
    private $type;

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getMailTemplate(): ?MailTemplate
    {
        return $this->mailTemplate;
    }

    public function setMailTemplate(MailTemplate $mailTemplate): self
    {
        $this->mailTemplate = $mailTemplate;

        return $this;
    }

    public function isSend(): ?bool
    {
        return $this->isSend;
    }

    public function setSend(bool $isSend): self
    {
        $this->isSend = $isSend;

        return $this;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }
}
