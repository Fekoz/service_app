<?php


namespace App\Util\Dto;


use Bukashk0zzz\YmlGenerator\Model\Category;

class MarketYmlDtoCategoryParent extends Category
{
    /**
     * @var string
     */
    private $key;

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): self
    {
        $this->key = $key;

        return $this;
    }
}
