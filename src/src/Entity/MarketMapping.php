<?php

namespace App\Entity;

use App\Entity\Trait\LifeTrait;
use App\Repository\MarketMappingRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity(repositoryClass=MarketMappingRepository::class)
 * @ORM\Table(name="`market_mapping`")
 */
class MarketMapping
{
    use LifeTrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $key;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DirectoryMarketplaceFormatImport", inversedBy="directory_marketplace_import")
     * @JoinColumn(name="directory_marketplace_import_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MarketMappingProperty", inversedBy="market_mapping")
     * @JoinColumn(name="market_mapping_id", referencedColumnName="id")
     */
    private $params;

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getType(): ?DirectoryMarketplaceFormatImport
    {
        return $this->type;
    }

    public function setType(DirectoryMarketplaceFormatImport $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getParams(): ?MarketMappingProperty
    {
        return $this->params;
    }

    public function setParams(MarketMappingProperty $params): self
    {
        $this->params = $params;

        return $this;
    }
}
