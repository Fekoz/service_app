<?php


namespace App\Util\Validator;


use App\Util\Constant;
use App\Util\Dto\ValidatorConfigDto;
use App\Util\Tools\MasterEntity;
use App\Util\ValidatorInterface;
use Symfony\Component\DomCrawler\Crawler;

class CollectionValidator implements ValidatorInterface
{
    /**
     * @var array | null
     */
    private $items;

    /**
     * @var MasterEntity
     */
    private $masterEntity;

    public function import(MasterEntity $master)
    {
        $this->masterEntity = $master;
    }

    /**
     * @param ValidatorConfigDto $config
     */
    public function setConfig(ValidatorConfigDto $config)
    {}

    /**
     * @param Crawler $item
     * @return array
     */
    public function prepare(Crawler $item): array
    {
        return $item->filter('a.h-product-image')->each(function ($cl, $i) {
            if ($cl->attr('href')) {
                return $cl->attr('href');
            }
        }) ?: [];
    }

    /**
     * @param array $item
     */
    public function formalize(array $item)
    {
        $this->items = $this->items && count($this->items) > 0 ? $item : null;
    }

    public function export(): MasterEntity
    {
        //if($this->items !== null) { }
        return $this->masterEntity;
    }
}
