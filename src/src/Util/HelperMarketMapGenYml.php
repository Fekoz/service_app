<?php

namespace App\Util;

use App\Entity\PriceExportDynamic;
use App\Service\Bot;
use App\Util\Dto\MarketYmlDtoCategory;
use App\Util\Dto\MarketYmlDtoCategoryParent;
use App\Util\Dto\MarketYmlDtoOfferSimple;
use App\Util\Dto\TwistSizeDto;
use Bukashk0zzz\YmlGenerator\Generator;
use Bukashk0zzz\YmlGenerator\Model\Category as CategoryModel;
use Bukashk0zzz\YmlGenerator\Model\Currency;
use Bukashk0zzz\YmlGenerator\Model\Delivery;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferParam;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;
use Bukashk0zzz\YmlGenerator\Model\ShopInfo;
use Bukashk0zzz\YmlGenerator\Settings;

class HelperMarketMapGenYml implements HelperMarketMapGenInterface
{
    /**
     * @var string
     */
    private $fileDir;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var array
     */
    private $categoriesParentDto;

    /**
     * @var array
     */
    private $categoriesDto;

    /**
     * @var array
     */
    private $offersDto;

    /**
     * @var array
     */
    private $linked;

    /**
     * @var bool
     */
    private $isMini;

    /**
     * @var Bot
     */
    private $bot;

    /**
     * @var array
     */
    private $dimensions;

    /**
     * @var HelperMarketExport
     */
    private $helper;

    public function __construct() {

    }

    private function remappingCategory(array $listParent, array $list) :array
    {
        $arr = [];

        foreach ($listParent as $val) {
            if ($val instanceof MarketYmlDtoCategoryParent) {
                $cat = (new CategoryModel())
                    ->setId($val->getId())
                    ->setName($val->getName());

                if ($val->getId() !== 1) {
                    $cat->setParentId(1);
                }

                $arr[] = $cat;
            }
        }

        foreach ($list as $val) {
            if ($val instanceof MarketYmlDtoCategory) {
                $arr[] = (new CategoryModel())
                    ->setId($val->getId())
                    ->setName($val->getName())
                    ->setParentId($val->getParentId());
            }
        }

        return $arr;
    }

    private function remappingOffer(array $list) :array
    {
        $arr = [];
        foreach ($list as $val) {
            if (!$val instanceof MarketYmlDtoOfferSimple) {
                continue;
            }
            /**
             * @description начало ограничений
             */

            // Ширина
            $weight = $val->getWeight();

            if ($weight > 140) {
                $weight = 139;
            }

            if ($weight < 1) {
                $weight = 1;
            }

            // Длина, ширина, высота в упаковке.
            $dimension = $val->getStatDimension();

            if ($dimension->getWidth() > 300) {
                $dimension->setWidth(299);
            }

            if ($dimension->getWidth() < 1) {
                $dimension->setWidth(5);
            }

            if ($dimension->getHeight() > 300) {
                $dimension->setHeight(299);
            }

            if ($dimension->getHeight() < 10) {
                $dimension->setHeight(10);
            }

            if ($dimension->getLength() > 300) {
                $dimension->setLength(299);
            }

            if ($dimension->getLength() < 1) {
                $dimension->setLength(4);
            }

            $length = $dimension->getLength();

            $width = $dimension->getWidth();
            if ($width > 42) {
                $width = 42;
            }

            $height = $dimension->getHeight();
            if ($height > 42) {
                $height = 42;
            }

            /**
             * @description Применит текущие лимиты к пакету
             */
            $dimensionRebuild = $this->helper->getDimensions($this->dimensions, $val->getProductW(), $val->getProductH());
            if ($dimensionRebuild !== null && $dimensionRebuild instanceof TwistSizeDto) {
                $height = $dimensionRebuild->getHeight();
                $width = $dimensionRebuild->getWidth();
                $length = $dimensionRebuild->getLength();
            }

            /**
             * @description окончание ограничений
             */
            $item = (new OfferSimple())
                ->setAvailable($val->isAvailable())
                ->setId($val->getId())
                ->setName($val->getName())
                ->setVendor($val->getVendor())
                ->setVendorCode($val->getVendorCode())
                ->setUrl($val->getUrl())
                ->setPrice($val->getPrice())
                ->setOldPrice($val->getOldPrice())
                ->setAutoDiscount($val->getAutoDiscount())
                ->setCurrencyId($val->getCurrencyId())
                ->setCategoryId($val->getCategoryId())
                ->setCategoriesId($val->getCategoriesId())
                ->setPictures($val->getPictures())
                ->setDelivery($val->isDelivery())
                ->setPickup($val->isPickup())
                ->setDescription($val->getDescription())
                ->setSalesNotes($val->getSalesNotes())
                ->setStore($val->isStore())
                ->setManufacturerWarranty($val->isManufacturerWarranty())
                ->setCountryOfOrigin($val->getCountryOfOrigin())
                ->setWeight($weight)
                ->setDimensions(
                    $length,
                    $width,
                    $height
                )
                ->setStore($val->isStore())
                ->addCustomElement('count', $val->getPriceCount())
                ->addCustomElement('disabled', $val->isAvailable() ? 'false' : 'true')
                ->addCustomElement('available', $val->isAvailable() ? 'true' : 'false')
                ->addCustomElement('min-quantity', $val->getQuantity())
                ->addCustomElement('step-quantity', $val->getQuantity())
                ->addBarcode($val->getProductEan())
            ;

            if ($val->isCircle()) {
                $r = (new OfferParam)
                    ->setName('Диаметр')
                    ->setValue($val->getProductW())
                    ->setUnit('сантиметры');
                $item->addParam($r);
            } else {
                $w = (new OfferParam)
                    ->setName('Ширина')
                    ->setValue($val->getProductW())
                    ->setUnit('сантиметры');
                $item->addParam($w);

                $h = (new OfferParam)
                    ->setName('Высота')
                    ->setValue($val->getProductH())
                    ->setUnit('сантиметры');
                $item->addParam($h);
            }

            foreach ($val->getParams() as $param) {
                $item->addParam($param);
            }

            $r = (new OfferParam)
                ->setName('Общий вес ковра')
                ->setValue($val->getWeight() - Constant::WEIGHT_OF_PACKING)
                ->setUnit('килограмм');
            $item->addParam($r);

            $arr[] = $item;
        }

        return $arr;
    }

    public function import(
        string $fileDir,
        string $fileName,
        array $categoriesParentDto,
        array $categoriesDto,
        array $offersDto,
        array $linked,
        bool $isMini,
        Bot $bot,
        array $dimensions,
        HelperMarketExport $helper
    ) {
        $this->fileDir = $fileDir;
        $this->fileName = $fileName;
        $this->categoriesParentDto = $categoriesParentDto;
        $this->categoriesDto = $categoriesDto;
        $this->offersDto = $offersDto;
        $this->linked = $linked;
        $this->isMini = $isMini;
        $this->bot = $bot;
        $this->dimensions = $dimensions;
        $this->helper = $helper;
    }

    public function generate()
    {
        $file = $this->fileDir . '/export/'. $this->fileName;
        $categories = $this->remappingCategory($this->categoriesParentDto, $this->categoriesDto);
        $offers = $this->remappingOffer($this->offersDto);

        if(\file_exists($file))
        {
            unlink($file);
        }

        $settings = (new Settings())
            ->setOutputFile($file)
            ->setEncoding('UTF-8');

        $shopInfo = (new ShopInfo())
            ->setName('CARPETTI')
            ->setCompany('В интернет-магазине «CARPETTI» вы найдете широкий выбор ковров и ковровых покрытий крупнейших фабрик-производителей России и ведущих мировых брендов.')
            ->setUrl('https://carpetti.vip/')
            ->setPlatform('OCCWO')
            ->setAgency('OCCWO')
            ->setPlatform(1.0);

        $currencies = [];
        $currencies[] = (new Currency())
            ->setId('RUR')
            ->setRate(1);

        $deliveries = [];
        $deliveries[] = (new Delivery())
            ->setCost(2)
            ->setDays(1)
            ->setOrderBefore(14);

        (new Generator($settings))->generate(
            $shopInfo,
            $currencies,
            $categories,
            $offers,
            $deliveries
        );

        $fileAssortment = $this->fileDir . '/export/'. 'remains_' . $this->fileName;
        $settings->setOutputFile($fileAssortment);

        (new Generator($settings))->generate(
            $shopInfo,
            $currencies,
            $categories,
            $offers,
            $deliveries
        );
    }

    public function clear()
    {
        $this->fileDir = '';
        $this->fileName = '';
        $this->categoriesParentDto = [];
        $this->categoriesDto = [];
        $this->offersDto = [];
        $this->linked = [];
    }

}
