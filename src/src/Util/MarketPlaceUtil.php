<?php


namespace App\Util;


use App\Entity\Category as CategoryCarpet;
use App\Entity\MarketMapping;
use App\Util\Dto\CategoryExpandDto;
use App\Util\Dto\MarketMappingDirectoryDto;
use App\Util\Dto\MarketMappingDto;
use App\Util\Dto\MarketPriceObjectDto;
use App\Util\Dto\MarketYmlDtoCategory;
use App\Util\Dto\MarketYmlDtoCategoryParent;

class MarketPlaceUtil
{
    public static function setPrice(HelperMarketExport $helper, ?array $options, object $configPrice, object $configPriceOld)
    {
        $price = (new MarketPriceObjectDto())
            ->setFactor($configPrice->factor)
            ->setMarkup($configPrice->markup)
            ->setRandom($configPrice->isRandom)
            ->setRndMin($configPrice->minRnd)
            ->setRndMax($configPrice->maxRnd);

        $priceOld = (new MarketPriceObjectDto())
            ->setFactor($configPriceOld->factor)
            ->setMarkup($configPriceOld->markup)
            ->setRandom($configPriceOld->isRandom)
            ->setRndMin($configPriceOld->minRnd)
            ->setRndMax($configPriceOld->maxRnd);

        return $helper->marketPriceCreate($price, $priceOld, $options);
    }

    public static function setCategories(HelperMarketExport $helper, ?array $categories, CategoryExpandDto $category)
    {
        $indexParent = 1;
        $index = 10;
        $cat = (new MarketYmlDtoCategoryParent())
            ->setId($indexParent++)
            ->setName('Ковры');
        $categoryVl = $category->addCategoriesParentDto($cat);

        /**
         * @var $category CategoryCarpet
         */
        foreach (Constant::SPECIFICATIONS as $specConst){
            if ($helper->isActualSpec($specConst[Constant::SPEC_VALUE])) {
                $cat = (new MarketYmlDtoCategoryParent())
                    ->setId($indexParent++)
                    ->setName($specConst[Constant::SPEC_PARSE_SYM])
                    ->setKey($specConst[Constant::SPEC_VALUE]);
                $categoryVl = $category->addCategoriesParentDto($cat);
            }
        }

        foreach ($categories as $ct) {
            if ($helper->isActualSpec($ct->getKey())) {
                $parent = $helper->getParentCategoryFind($ct->getKey(), $categoryVl->getCategoriesParentDto());
                $cat = (new MarketYmlDtoCategory())
                    ->setId($index++)
                    ->setName($ct->getValue())
                    ->setCategoryKey($ct->getKey())
                    ->setCategoryValue($ct->getValue())
                    ->setCategoryName($ct->getName())
                    ->setCategoryType($ct->getType());

                if ($parent) {
                    $cat->setParentId($parent);
                }
                $categoryVl = $category->addCategoriesDto($cat);
            }
        }

        return $categoryVl;
    }

    public static function setMarketMapping(array $mm): array
    {
        $model = [];
        foreach ($mm as $val) {
            if (!$val instanceof MarketMapping) {
                continue;
            }

            $values = \explode(",", $val->getName());

            if (!$val->getKey() || empty($val->getKey()) || !$values || empty($values)){
                continue;
            }

            $model[] = (new MarketMappingDto())
                ->setKey($val->getKey())
                ->setValues(\explode(",", $val->getName()))
                ->setOzon(
                    (new MarketMappingDirectoryDto())
                    ->setId($val->getParams()->getId())
                    ->setValue($val->getParams()->getValue())
                )
                ->setDirect(
                    (new MarketMappingDirectoryDto())
                        ->setId($val->getType()->getId())
                        ->setValue($val->getType()->getName())
                )
            ;
        }

        return $model;
    }

}
