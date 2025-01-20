<?php


namespace App\Service;


use App\Entity\Collection;
use App\Entity\MarketSequence;
use App\Entity\Options;
use App\Entity\Price;
use App\Entity\PriceExportDynamic;
use App\Entity\Product;
use App\Entity\Specification;
use App\Repository\ProductRepository;
use App\Util\Constant;
use App\Util\Dto\CollectionCCRepoDTO;
use App\Util\Dto\MarketPriceDto;
use App\Util\Dto\MarketPriceObjectDto;
use App\Util\Dto\MarketYmlDtoCategory;
use App\Util\Dto\MarketYmlDtoCategoryParent;
use App\Util\Dto\MarketYmlDtoOfferSimple;
use App\Util\Dto\MarketYmlDtoParam;
use App\Util\Dto\TwistSizeDto;
use App\Util\HelperMarketMapGenRemainYml;
use App\Util\HelperMarketMapGenXlsx;
use App\Util\HelperMarketMapGenYml;
use App\Util\SystemServiceInterface;
use App\Util\Tools\MasterEntity;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Category as CategoryCarpet;
use App\Util\HelperMarketExport;

class MarketExport implements SystemServiceInterface
{
    const MIN_SIZE_TO_EXPORT = 2;
    const EXPORT_XLSX = false;
    /**
     * @var object
     */
    private $param;

    /**
     * @var EntityManagerInterface
     */
    private $entity;

    /**
     * @var Bot
     */
    private $bot;

    /**
     * @var ProductRepository
     */
    private $repo;

    /**
     * @var array<OfferSimple>
     */
    private $offersDto;

    /**
     * @var array<MarketYmlDtoCategoryParent>
     */
    private $categoriesParentDto;

    /**
     * @var array<MarketYmlDtoCategory>
     */
    private $categoriesDto;

    /**
     * @var HelperMarketExport
     */
    private $helper;

    /**
     * @var HelperMarketMapGenYml
     */
    private $helperYml;

    /**
     * @var HelperMarketMapGenXlsx
     */
    private $helperXlsx;

    /**
     * @var array
     */
    private $linked;

    /**
     * @var MarketPriceDto
     */
    private $price;

    /**
     * @var PriceExportDynamic
     */
    private $priceLimiter;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Bot $bot)
    {
        $this->entity = $entity;
        $this->bot = $bot;
        $this->bot->init(Constant::BOT_TELEGRAM);
        $this->repo = $this->entity->getRepository(Product::class);
        $this->helper = new HelperMarketExport();
        $this->helperYml = new HelperMarketMapGenYml();
        $this->helperXlsx = new HelperMarketMapGenXlsx();
        $this->param = (object) $param->get(Constant::CONFIG_NAME[__CLASS__]);
        $this->offersDto = [];
        $this->categoriesDto = [];
        $this->categoriesParentDto = [];
        $this->linked = [];
        $this->price = null;
        $this->priceLimiter = $this->entity->getRepository(PriceExportDynamic::class)->findAll();
    }

    private function setPrice()
    {
        $configPrice = (object) $this->param->price;
        $configPriceOld = (object) $this->param->priceOld;

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

        $options = $this->entity->getRepository(Options::class)->findAll();

        $this->price = $this->helper->marketPriceCreate($price, $priceOld, $options);
    }

    private function setCategories()
    {
        $indexParent = 1;
        $index = 10;
        $cat = (new MarketYmlDtoCategoryParent())
            ->setId($indexParent++)
            ->setName('Ковры');
        $this->categoriesParentDto[] = $cat;

        $categories = $this->entity->getRepository(CategoryCarpet::class)->getAllActive();
        /**
         * @var $category CategoryCarpet
         */
        foreach (Constant::SPECIFICATIONS as $specConst){
            if ($this->helper->isActualSpec($specConst[Constant::SPEC_VALUE])) {
                $cat = (new MarketYmlDtoCategoryParent())
                    ->setId($indexParent++)
                    ->setName($specConst[Constant::SPEC_PARSE_SYM])
                    ->setKey($specConst[Constant::SPEC_VALUE]);
                $this->categoriesParentDto[] = $cat;
            }
        }

        foreach ($categories as $category) {
            if ($this->helper->isActualSpec($category->getKey())) {
                $parent = $this->helper->getParentCategoryFind($category->getKey(), $this->categoriesParentDto);
                $cat = (new MarketYmlDtoCategory())
                    ->setId($index++)
                    ->setName($category->getValue())
                    ->setCategoryKey($category->getKey())
                    ->setCategoryValue($category->getValue())
                    ->setCategoryName($category->getName())
                    ->setCategoryType($category->getType());

                if ($parent) {
                    $cat->setParentId($parent);
                }
                $this->categoriesDto[] = $cat;
            }
        }
    }

    private function parse(MasterEntity $me)
    {
        $brand = $this->helper->findSpecificationParam(Constant::SPEC_FABRIC, $me->getSpecification());
        //----------
        /** @todo check methode pic array */
        $images = $this->helper->findPic($me->getImage(), $this->param->external_carpetti_web);
        //$images = $this->helper->findPicOrigin($me->getImage(), $this->param->external_carpetti_web);
        //----------
        $params = $this->helper->formatSpec($me->getSpecification());
        $color = $this->helper->findSpecificationParam(Constant::SPEC_COLOR, $me->getSpecification());
        $city = $this->helper->findSpecificationParam(Constant::SPEC_COUNTRY, $me->getSpecification());
        $weight = $this->helper->getWeight($this->helper->findSpecificationParam(Constant::SPEC_WEIGHT, $me->getSpecification()));

        $pile = $this->helper->reformatSpec($this->helper->findSpecificationParam(Constant::SPEC_PILE, $me->getSpecification()), ' мм');
        $category = $this->helper->getCategorySpecific($me->getSpecification(), $this->categoriesDto);
        $tags = $this->helper->getCategorySpecificName($me->getSpecification(), $this->categoriesDto);

        $specCollection = $this->helper->findSpecificationParam(Constant::SPEC_COLLECTION, $me->getSpecification());
        $collectionProductsId = [];
        $isCircle = $this->helper->findSpecificationParam(Constant::SPEC_FORM, $me->getSpecification()) === 'Круг';

        // TO NAME
        $collectionMarketName = $this->helper->findSpecificationParam(Constant::SPEC_COLLECTION, $me->getSpecification());
        $formMarketName = $this->helper->findSpecificationParam(Constant::SPEC_FORM, $me->getSpecification());
        $colorMarketName = $this->helper->findSpecificationParam(Constant::SPEC_COLOUR, $me->getSpecification());
        $styleMarketName = $this->helper->findSpecificationParam(Constant::SPEC_STYLE, $me->getSpecification());
        $countryMarketName = $this->helper->findSpecificationParam(Constant::SPEC_COUNTRY, $me->getSpecification());

        // view all changes with this product
        if (count($me->getCollection()) > 0) {
            /**
             * @var $collection Collection
             */
            foreach ($me->getCollection() as $collection) {
                $collectionProductsId[] = $collection->getOutProductId();
            }
        }

        $collectionList = [];
        // getPriceMidWithPrice - return getMid list
        $collectionList = \array_merge($collectionList, $this->helper->getPriceMidWithPrice($me->getPrice()));

        if (count($collectionProductsId) > 0) {
            $collectionList = \array_merge($collectionList, $this->helper->getPriceMidWithProduct($collectionProductsId, $this->entity->getRepository(Price::class)));
        }

        $collectionList = \array_unique($collectionList);

        if (count($collectionList) > 1) {
            $linked = $this->helper->changeParamMarket(
                $collectionList,
                $this->param->marketSkuidPrefix
            );
            $this->linked = $this->helper->appendLinked($linked, $this->linked);
        }

        /**
         * @var $collection CollectionCCRepoDTO
         */
        $collection = $this->entity->getRepository(Collection::class)->getLastCollection($me->getProduct()->getId());

        $prodParams = [];
        foreach ($me->getSpecification() as $specification) {
            if ($specification instanceof Specification) {
                $key = null;
                foreach (Constant::SPECIFICATIONS as $specConst) {
                    if ($specConst[Constant::SPEC_VALUE] === $specification->getName()) {
                        $key = $specConst[Constant::SPEC_PARSE_SYM];
                    }
                }
                $prodParams[] = (new MarketYmlDtoParam())
                    ->setKey($key)
                    ->setName($specification->getName())
                    ->setValue($specification->getValue())
                ;
            }
        }

        /**
         * @var $price Price
         */
        foreach ($me->getPrice() as $price) {
            if ($price->getPrice() < 100) {
                continue;
            }

            // if zero w,h item
            if ($price->getWidth() < 1 || $price->getHeight() < 1) {
                continue;
            }

            // clear duplicate to mid item
            if (false === $this->helper->isAppendAndAddOrSumCount($this->offersDto, $price)) {
                continue;
            }

            $priceRandSum = 0;
            if (true === $this->price->getPrice()->isRandom()) {
                $priceRandSum = \rand($this->price->getPrice()->getRndMin(), $this->price->getPrice()->getRndMax());
            }

            $priceOldRandSum = 0;
            if (true === $this->price->getPriceOld()->isRandom()) {
                $priceOldRandSum = \rand($this->price->getPriceOld()->getRndMin(), $this->price->getPriceOld()->getRndMax());
            }

            $carpetPrice = $price->getPrice() * $this->price->getPrice()->getFactor() + $this->price->getPrice()->getMarkup() + $priceRandSum;
            $carpetOldPrice = $price->getPrice() * $this->price->getPriceOld()->getFactor() + $this->price->getPriceOld()->getMarkup() + $priceOldRandSum;

            if (!$pile) {
                $pile = 10;
            }

            $package = $this->helper->calculateSizePackage($price->getHeight(), $price->getWidth(), $pile);
            $packingHeight = $package->getHeight();
            $packingWidth = $package->getWidth();
            $packingSize = $package->getLength();

            $isAvailable = $me->getProduct()->isActive();

            // TODO: COUNT MIN MARKET
            if($price->getCount() <= self::MIN_SIZE_TO_EXPORT) {
                $isAvailable = false;
            }

            $quantity = 1;
            $marketSequence = $this->entity->getRepository(MarketSequence::class)->getMid($price->getMid());
            if (null !== $marketSequence && $marketSequence instanceof MarketSequence) {
                if ($marketSequence->isActive()) {
                    if ($marketSequence->isCounter()) {
                        $quantity = $marketSequence->getCounterPkg();
                    }
                }
            }

            //todo: constant alg
            $quantity = $this->helper->getCountToQuantityWithCountryAndSize($price->getWidth(), $price->getHeight(), $city, $quantity);

            // weight
            $weightValue = (int) ceil(str_replace(" гр./м2", "", $weight));
            if ($weight === HelperMarketExport::NONE_NAME) {
                $weightValue = 1200;
            }

            /**
             * @var $offer MarketYmlDtoOfferSimple;
             */
            $offer = (new MarketYmlDtoOfferSimple())
                ->setProductId($me->getProduct()->getId())
                ->setProductArticle($me->getProduct()->getArticle())
                ->setProductUuid($me->getProduct()->getUuid())
                ->setPriceUuid($price->getUuid())
                ->setPriceMid($price->getMid())
                ->setAvailable($isAvailable)
                ->setId($this->param->marketSkuidPrefix . "" . $price->getMid())
                ->setName($this->helper->getNameFormat(
                    $collectionMarketName,
                    $formMarketName,
                    $colorMarketName,
                    $styleMarketName,
                    $countryMarketName,
                    $price->getWidth(),
                    $price->getHeight()
                ))
                ->setVendor($brand)
                ->setVendorCode($me->getProduct()->getArticle())
                ->setUrl($this->param->carpetti_web . '/carpet?article=' . $me->getProduct()->getArticle())
                ->setPrice($carpetPrice)
                ->setOldPrice($carpetOldPrice)
                ->setAutoDiscount(true)
                ->setCurrencyId('RUR')
                //->setCategoryId(1)
                ->setCategoriesId($category)
                ->setPictures($images)
                ->setDelivery(true)
                ->setPickup(true)
                ->setDescription(\sprintf('Коллекция %s предлагает ковер %s формы. '.
                    'Этот ковер изготовлен в %s с качеством %s и имеет %s цвет с %s дизайн. '.
                    'Это отличная находка для дома и дачи, которая обеспечит тепло и комфорт. '.
                    'Такой интерьерный аксессуар разных цветов очень приятен для ног, ходить по нему одно удовольствие. '.
                    'Интерьерное украшение очень легко чистить, предназначено как для влажной, так и для сухой уборки, для робота пылесоса. '.
                    'Товар полностью безопасен для здоровья и имеет необходимые сертификаты соответствия качества.',
                    null !== $collectionMarketName ? $collectionMarketName : 'CARPETTI.COLLECTION',
                    null !== $formMarketName ? $formMarketName : 'Приятной',
                    null !== $countryMarketName ? $countryMarketName : 'Россия',
                    null !== $this->helper->findSpecificationParam(Constant::SPEC_QUALITY, $me->getSpecification())
                        ? $this->helper->findSpecificationParam(Constant::SPEC_QUALITY, $me->getSpecification())
                        : 'FRIZE',
                    null !== $colorMarketName ? $colorMarketName : 'Приятный',
                    null !== $styleMarketName ? $styleMarketName : 'Актуальным'
                ))
                ->setSalesNotes('Наличные, Visa/Mastercard, б/н расчет. Бесплатная доставка по Мосве и Московской области.')
                ->setStore(true)
                ->setManufacturerWarranty(true)
                ->setCountryOfOrigin($city)
                ->setWeight($this->helper->calculateWeight($price->getHeight(), $price->getWidth(), $weightValue))
                ->setStatDimension($this->helper->calculateSizePackage($price->getHeight(), $price->getWidth(), $pile))
                //->setDimensions($packingHeight, $packingWidth, $packingSize)
                ->setPackingHeight($packingHeight)
                ->setPackingWidth($packingWidth)
                ->setPackingSize($packingSize)
                ->setStore(true)
                ->setProductW($price->getWidth())
                ->setProductH($price->getHeight())
                ->setCircle($isCircle)
                ->setPriceCount($price->getCount())
                ->setProductParams($prodParams)
                ->setProductEan($this->helper->genEan($price->getId()))
                ->setQuantity($quantity)
                //->addDeliveryOption(new Delivery())
            ;

            $offer->setCollectionCode($collection->getCode());
            $offer->setCollectionId($collection->getId());
            $offer->setCollectionName($collection->getName());
            $offer->setTags($tags);

            foreach ($params as $param) {
                $offer->addParam($param);
            }

            $this->offersDto[] = $offer;
        }
    }

    private function read(int $limit = 0, int $offset = 0)
    {
        $list = $this->repo->getArrayList($limit, $offset);
        /**
         * @var $val Product
         */
        foreach ($list as $val) {
            if ($val instanceof Product && $val->getId()) {
                $this->parse($this->repo->masterToProductWithCollection($val));
            }
        }
        $this->entity->clear();
    }

    public function import(array $list = [])
    {
        return;
    }

    private function generate()
    {
        // Assortment
        $this->helperYml->import(
            $this->param->kernel_dir,
            $this->param->marketExportFileName,
            $this->categoriesParentDto,
            $this->categoriesDto,
            $this->offersDto,
            $this->linked,
            $this->param->isMini,
            $this->bot,
            $this->priceLimiter,
            $this->helper
        );

        $this->helperYml->generate();
        $this->helperYml->clear();

        // Cards
        if (true === self::EXPORT_XLSX) {
            $this->helperXlsx->import(
                $this->param->kernel_dir,
                $this->param->marketExportXlsFileName,
                $this->categoriesParentDto,
                $this->categoriesDto,
                $this->offersDto,
                $this->linked,
                $this->param->isMini,
                $this->bot,
                $this->priceLimiter,
                $this->helper
            );

            $this->helperXlsx->generate();
            $this->helperXlsx->clear();
        }
    }

    public function run()
    {
        $count = $this->repo->getCount();
        $this->setCategories();
        $this->setPrice();
        if ($count > $this->param->maxItemMarket) {
            $ic = round($count / $this->param->maxItemMarket, 0 ,PHP_ROUND_HALF_UP);
            for ($i = 0; $i <= $ic; $i++) {
                $this->read($this->param->maxItemMarket, $i * $this->param->maxItemMarket);
            }
            $this->generate();
            return;
        }

        $this->read($this->param->maxItemCollection);
        $this->generate();
    }

}
