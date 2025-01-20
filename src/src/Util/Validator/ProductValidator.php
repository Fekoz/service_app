<?php

namespace App\Util\Validator;

use App\Entity\Product;
use App\Entity\Specification;
use App\Util\Constant;
use App\Util\Dto\ParserDto\ParseItemOptionDto;
use App\Util\Dto\ValidatorConfigDto;
use App\Util\Tools\MasterEntity;
use App\Util\ValidatorInterface;
use Symfony\Component\DomCrawler\Crawler;

class ProductValidator implements ValidatorInterface
{
    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var int
     */
    private $price;

    /**
     * @var float
     */
    private $factor;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var MasterEntity
     */
    private $master;

    /**
     * @var string
     */
    private $url;

    public function import(MasterEntity $master)
    {
        $this->master = $master;
    }

    /**
     * @param string $text
     * @return string
     */
    public function priceValidate(string $text) :string
    {
        return \preg_replace('/\W/', '', $text);
    }

    /**
     * @param ValidatorConfigDto $config
     */
    public function setConfig(ValidatorConfigDto $config)
    {
        $this->isActive = false;
        $this->price = null;
        $this->factor = $config->getFactor();
        $this->uuid = $config->getUuid();
        $this->url = $config->getOriginalUrl();
        $this->price = $config->getPrice();
    }

    /**
     * @param Crawler $item
     * @return ParseItemOptionDto
     */
    public function prepare(Crawler $item): ParseItemOptionDto
    {
        $fullPrice = 0;

        try {
            $fullPrice = $this->priceValidate(($item->filter('.h-ip-sizes .h-ip-size__price'))->text()) ?: 0;
            $fullPrice = \preg_replace('/[^0-9]/', '', $fullPrice);

        } catch (\Exception $e) {}

        $list = [];

        try {
            $item->filter('.h-ip-sizes .h-ip-size__list a')->each(function ($itm, $i) use(&$list) {
                $list[] = $itm->attr('data-url');
            });
        } catch (\Exception $e) {}

        return (new ParseItemOptionDto())
            ->setPrice((int) $fullPrice)
            ->setPriceParseList($list)
            ;
    }

    private function specificationArray(array $specifications, array $keysCode, array $symb): string
    {
        $symb = \array_reverse($symb);
        $mapping = [];
        $index = count($keysCode)-1;
        foreach ($specifications as $specification) {
            /**
             * @var $specification Specification
             */
            foreach ($keysCode as $keyCode) {
                $index = $index < 0 ? 0 : $index;

                if ($specification->getName() === Constant::SPECIFICATIONS[$keyCode][Constant::SPEC_VALUE]) {
                    $mapping[$index] = $symb[$index] . $specification->getValue();
                    $index--;
                }
            }
        }
        return \implode('', $mapping);
    }

    /**
     * @param array $specifications
     * @return string
     */
    private function createName(array $specifications) :string
    {
        return $this->specificationArray($specifications, [
            Constant::SPEC_STYLE,
            Constant::SPEC_COLLECTION,
            Constant::SPEC_FORM,
            Constant::SPEC_COLOUR,
        ], ['Ковер ', ' - ', ' ', ', ']);
    }

    /**
     * @param string $name
     * @return string
     */
    private function cutName(string $name) :string
    {
        return \substr(\md5($name), 0, 6);
    }

    /**
     * @param array $specifications
     * @param string $name
     * @return string
     */
    private function createArticle(array $specifications, string $name) :string
    {
        return strtoupper($this->specificationArray($specifications, [
            Constant::SPEC_COLLECTION,
            Constant::SPEC_COLOR,
        ], [$this->cutName($name) . '-', '-']));
    }

    /**
     * @param string $article
     * @return string
     */
    private function transferArticle(string $article): string
    {
        return \strtoupper(\strtr($article, Constant::ARTICLE_FORMATTER));
    }

    /**
     * @param array $item
     */
    public function formalize(array $item)
    {
        $name = $this->createName($this->master->getSpecification());
        $this->isActive = count($this->master->getPrice()) > 0;
        $this->master->setProduct(
            (new Product())
                ->setName($name)
                ->setArticle($this->transferArticle($this->createArticle($this->master->getSpecification(), $this->uuid . $name)))
                ->setActive($this->isActive)
                ->setUuid($this->uuid)
                ->setFullPrice($this->price)
                ->setFactor($this->factor)
                ->setFactorFull($this->factor)
                ->setOriginalUrl($this->url)
                ->setProductLock(false)
                ->setPriceLock(false)
                ->setSpecificationLock(false)
                ->setImagesLock(false)
                ->setAttributeLock(false)
                ->setGlobalUpdateLock(false)
        );
    }

    public function export(): MasterEntity
    {
        return $this->master;
    }
}
