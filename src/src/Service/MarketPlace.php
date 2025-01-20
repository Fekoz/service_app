<?php


namespace App\Service;

use App\Entity\Category as CategoryCarpet;
use App\Entity\Collection;
use App\Entity\MarketMapping;
use App\Entity\MarketSequence;
use App\Entity\Options;
use App\Entity\Price;
use App\Entity\PriceExportDynamic;
use App\Entity\Product;
use App\Entity\Specification;
use App\Repository\ProductRepository;
use App\Util\Constant;
use App\Util\Dto\CategoryExpandDto;
use App\Util\Dto\CollectionCCRepoDTO;
use App\Util\Dto\MarketMappingDirectoryDto;
use App\Util\Dto\MarketMappingDto;
use App\Util\Dto\MarketPlaceCharacterDto;
use App\Util\Dto\MarketPlaceDto;
use App\Util\Dto\MarketPlaceLiveDateDto;
use App\Util\Dto\MarketPlacePriceMarketDataDto;
use App\Util\Dto\MarketPlacePriceToMarketDto;
use App\Util\Dto\MarketPriceDto;
use App\Util\Dto\MarketYmlDtoParam;
use App\Util\Dto\TwistSizeDto;
use App\Util\HelperMarketExport;
use App\Util\MarketPlaceUtil;
use App\Util\Tools\MasterEntity;
use App\Util\SystemRunnerInterface;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MarketPlace implements SystemRunnerInterface
{
    const MIN_SIZE_TO_EXPORT = 2;
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
     * @var CategoryExpandDto
     */
    private $category;

    /**
     * @var HelperMarketExport
     */
    private $helper;

    /**
     * @var MarketPriceDto
     */
    private $price;

    /**
     * @var PriceExportDynamic
     */
    private $priceLimiter;

    /**
     * @var LogService
     */
    private $log;

    /**
     * @var array<MarketMappingDto>
     */
    private $mappingMind;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Bot $bot, LogService $log)
    {
        $this->entity = $entity;
        $this->bot = $bot;
        $this->bot->init(Constant::BOT_TELEGRAM);
        $this->repo = $this->entity->getRepository(Product::class);
        $this->helper = new HelperMarketExport();
        $this->param = (object) $param->get(Constant::CONFIG_NAME[__CLASS__]);
        $this->offersDto = [];
        $this->price = null;
        $this->priceLimiter = $this->entity->getRepository(PriceExportDynamic::class)->findAll();
        $this->log = $log;
        $this->rebuildMap();
    }

    private function rebuildMap(): void
    {
        $this->offersDto = [];
        $this->mappingMind = [];
        $this->price = [];
        $this->category = [];
        $this->entity->clear();

        $categoryDto = new CategoryExpandDto();
        $this->category = MarketPlaceUtil::setCategories(
            $this->helper,
            $this->entity->getRepository(CategoryCarpet::class)->getAllActive(),
            $categoryDto
        );

        $this->price = MarketPlaceUtil::setPrice(
            $this->helper,
            $this->entity->getRepository(Options::class)->findAll(),
            (object) $this->param->price,
            (object) $this->param->priceOld
        );

        $this->mappingMind = MarketPlaceUtil::setMarketMapping(
            $this->entity->getRepository(MarketMapping::class)->findAll()
        );
    }

    private function parse(MasterEntity $me): void
    {
        $brand = $this->helper->findSpec($me->getSpecification());
        $images = $this->helper->findPic($me->getImage(), $this->param->external_carpetti_web);
        $weight = $this->helper->getWeight($this->helper->findSpecificationParam(Constant::SPEC_WEIGHT, $me->getSpecification()));
        $pile = $this->helper->reformatSpec($this->helper->findSpecificationParam(Constant::SPEC_PILE, $me->getSpecification()), ' мм');
        $category = $this->helper->getCategorySpecific($me->getSpecification(), $this->category->getCategoriesDto());
        $categoryNames = $this->helper->getCategorySpecificName($me->getSpecification(), $this->category->getCategoriesDto());
        $isCircle = $this->helper->findSpecificationParam(Constant::SPEC_FORM, $me->getSpecification()) === 'Круг';

        // field to params
        $collectionCarpet = $this->helper->findSpecificationParam(Constant::SPEC_COLLECTION, $me->getSpecification());
        $pileCarpet = $this->helper->findSpecificationParam(Constant::SPEC_PILE, $me->getSpecification());
        $formCarpet = $this->helper->findSpecificationParam(Constant::SPEC_FORM, $me->getSpecification());
        $styleCarpet = $this->helper->findSpecificationParam(Constant::SPEC_STYLE, $me->getSpecification());
        $typeCarpet = $this->helper->findSpecificationParam(Constant::SPEC_CARPET_TYPE, $me->getSpecification());
        $colorCarpet = $this->helper->findSpecificationParam(Constant::SPEC_COLOUR, $me->getSpecification());
        $countryCarpet = $this->helper->findSpecificationParam(Constant::SPEC_COUNTRY, $me->getSpecification());
        $fabricCarpet = $this->helper->findSpecificationParam(Constant::SPEC_FABRIC, $me->getSpecification());
        $qualityCarpet = $this->helper->findSpecificationParam(Constant::SPEC_QUALITY, $me->getSpecification());
        $warpCarpet = $this->helper->findSpecificationParam(Constant::SPEC_WARP, $me->getSpecification());
        $vendorCarpet = $this->helper->findSpecificationParam(Constant::SPEC_VENDOR_NAME, $me->getSpecification());

        /**
         * @var $collection CollectionCCRepoDTO
         */
        $collection = $this->entity->getRepository(Collection::class)->getLastCollection($me->getProduct()->getId());

        $collectionUidList = [];
        $collectionNumberList = [];
        if ($collection !== null && $collection->getCode() !== null) {
            $collectionUidList = $this->helper->addPrefixList($this->param->marketSkuidPrefix, $this->entity->getRepository(Collection::class)->getMidListWithCollectionCode($collection->getCode()));
            $collectionNumberList = $this->entity->getRepository(Collection::class)->getIdWithCollectionCode($collection->getCode());
        }

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
         * @var $spec MarketPlaceCharacterDto
         */
        $specEm = new MarketPlaceCharacterDto();

        try {
            $specEm->setFabric(
                $this->helper->findMatchingOzonDto(
                    $this->mappingMind,
                    Constant::MARKETPLACE_FORMAT[Constant::MARKETPLACE_OZON_BRAND][0],
                    Constant::SPECIFICATIONS[Constant::SPEC_VENDOR_NAME][Constant::SPEC_VALUE],
                    \mb_strtoupper($vendorCarpet)
                )
            );

            $specEm->setCountry(
                $this->helper->findMatchingOzonDto(
                    $this->mappingMind,
                    Constant::MARKETPLACE_FORMAT[Constant::MARKETPLACE_OZON_COUNTRY][0],
                    Constant::SPECIFICATIONS[Constant::SPEC_COUNTRY][Constant::SPEC_VALUE],
                    $countryCarpet
                )
            );

            $specEm->setStyles(
                $this->helper->findMatchingOzonDto(
                    $this->mappingMind,
                    Constant::MARKETPLACE_FORMAT[Constant::MARKETPLACE_OZON_STYLE][0],
                    Constant::SPECIFICATIONS[Constant::SPEC_STYLE][Constant::SPEC_VALUE],
                    $styleCarpet
                )
            );

            $specEm->setTypeBase(
                $this->helper->findMatchingOzonDto(
                    $this->mappingMind,
                    Constant::MARKETPLACE_FORMAT[Constant::MARKETPLACE_OZON_TYPE_BASE][0],
                    Constant::SPECIFICATIONS[Constant::SPEC_WARP][Constant::SPEC_VALUE],
                    $warpCarpet
                )
            );

            $specEm->setTypeCarpet(
                $this->helper->findMatchingOzonDto(
                    $this->mappingMind,
                    Constant::MARKETPLACE_FORMAT[Constant::MARKETPLACE_OZON_TYPE_CARPET][0],
                    Constant::SPECIFICATIONS[Constant::SPEC_CARPET_TYPE][Constant::SPEC_VALUE],
                    $typeCarpet
                )
            );
        } catch (\Exception $exception) {
            $this->log->registerException(Logger::ERROR, Constant::LOG_PARSE_PAGE, null, ['error' => $exception->getMessage(), 'article' => $me->getProduct()->getArticle()]);
            return;
        }

        try {
            $specEm->setColor(
                $this->helper->findMatchingOzonDto(
                    $this->mappingMind,
                    Constant::MARKETPLACE_FORMAT[Constant::MARKETPLACE_OZON_COLOR][0],
                    Constant::SPECIFICATIONS[Constant::SPEC_COLOUR][Constant::SPEC_VALUE],
                    $colorCarpet
                )
            );
        } catch (\Exception $exception) {
            $specEm->setColor(
                $this->helper->findMatchingOzonDto(
                    $this->mappingMind,
                    Constant::MARKETPLACE_FORMAT[Constant::MARKETPLACE_OZON_COLOR][0],
                    Constant::SPECIFICATIONS[Constant::SPEC_COLOUR][Constant::SPEC_VALUE],
                    'default'
                )
            );
        }

        try {
            $specEm->setForm(
                $this->helper->findMatchingOzonDto(
                    $this->mappingMind,
                    Constant::MARKETPLACE_FORMAT[Constant::MARKETPLACE_OZON_FORM][0],
                    Constant::SPECIFICATIONS[Constant::SPEC_FORM][Constant::SPEC_VALUE],
                    $formCarpet
                )
            );
        } catch (\Exception $exception) {
            $specEm->setForm(
                $this->helper->findMatchingOzonDto(
                    $this->mappingMind,
                    Constant::MARKETPLACE_FORMAT[Constant::MARKETPLACE_OZON_FORM][0],
                    Constant::SPECIFICATIONS[Constant::SPEC_FORM][Constant::SPEC_VALUE],
                    'Прямоугольник'
                )
            );
        }

        try {
            $specEm->setOther(
                $this->helper->findMatchingOzonDto(
                    $this->mappingMind,
                    Constant::MARKETPLACE_FORMAT[Constant::MARKETPLACE_OZON_OTHER][0],
                    Constant::SPECIFICATIONS[Constant::SPEC_WARP][Constant::SPEC_VALUE],
                    $warpCarpet
                )
            );
        } catch (\Exception $exception) {
            $specEm->setOther(
                $this->helper->findMatchingOzonDto(
                    $this->mappingMind,
                    Constant::MARKETPLACE_FORMAT[Constant::MARKETPLACE_OZON_OTHER][0],
                    Constant::SPECIFICATIONS[Constant::SPEC_WARP][Constant::SPEC_VALUE],
                    'Не указано'
                )
            );
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
                $pile = '10';
            }

            $pile = intval($pile);

            $isAvailable = $me->getProduct()->isActive();

            if (!$specEm->getForm()) {
                $nForm = new MarketMappingDirectoryDto();
                if ($price->getHeight() === $price->getHeight() && $isCircle) {
                    $nForm->setId(43080)->setValue('Круг');
                } else if ($price->getHeight() === $price->getHeight() && !$isCircle) {
                    $nForm->setId(43078)->setValue('Квадрат');
                } else if ($price->getHeight() !== $price->getHeight() && $isCircle) {
                    $nForm->setId(43082)->setValue('Овал');
                } else if ($price->getHeight() !== $price->getHeight() && !$isCircle) {
                    $nForm->setId(43084)->setValue('Прямоугольник');
                } else {
                    $nForm->setId(43081)->setValue('Нестандартная');
                }
                $specEm->setForm($nForm);
            }

            $priceScheme = (new MarketPlacePriceMarketDataDto)
                ->setOriginal(
                    (new MarketPlacePriceToMarketDto)
                        ->setCurrent($price->getPrice())
                        ->setOld($price->getPrice())
                )
                ->setReformat(
                    (new MarketPlacePriceToMarketDto)
                        ->setCurrent($carpetPrice)
                        ->setOld($carpetOldPrice)
                )
                ->setOzon(
                    (new MarketPlacePriceToMarketDto)
                        ->setCurrent(\round($carpetPrice * $this->price->getPrice()->getOzonFactor(), 2))
                        ->setOld(\round($carpetOldPrice * $this->price->getPriceOld()->getOzonFactor(), 2))
                )
                ->setYandex(
                    (new MarketPlacePriceToMarketDto)
                        ->setCurrent(\round($carpetPrice * $this->price->getPrice()->getYandexFactor(), 2))
                        ->setOld(\round($carpetOldPrice * $this->price->getPriceOld()->getYandexFactor(), 2))
                )
                ->setWildberries(
                    (new MarketPlacePriceToMarketDto)
                        ->setCurrent(\round($carpetPrice * $this->price->getPrice()->getWildberriesFactor(), 2))
                        ->setOld(\round($carpetOldPrice * $this->price->getPriceOld()->getWildberriesFactor(), 2))
                )
            ;

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
            $quantity = $this->helper->getCountToQuantityWithCountryAndSize($price->getWidth(), $price->getHeight(), $countryCarpet, $quantity);

            $pileValue = (float) str_replace(" мм", "", $pileCarpet);
            if ($pileCarpet === HelperMarketExport::NONE_NAME) {
                $pileValue = HelperMarketExport::DEFAULT_CARPET_LENGTH;
            }

            $statCarpet = (new TwistSizeDto())
                ->setWidth($price->getWidth())
                ->setHeight($price->getHeight())
                ->setLength(ceil(HelperMarketExport::DEFAULT_CARPET_LENGTH + $pileValue)) // в см
            ;

            $liveDate = (new MarketPlaceLiveDateDto())
                ->setAt(
                    (new \DateTime())
                        ->format('Y-m-d H:i:s')
                )
                ->setTo(
                    (new \DateTime())
                        ->modify('+16 hours')
                        ->format('Y-m-d H:i:s')
                )
            ;

            $designCode = $this->helper->findSpecificationParam(Constant::SPEC_DESIGN, $me->getSpecification());
            if (!$designCode || $designCode === HelperMarketExport::NONE_NAME) {
                $designCode = 'Базовый';
            }

            // weight
            $weightValue = (int) ceil(str_replace(" гр./м2", "", $weight));
            if ($weight === HelperMarketExport::NONE_NAME) {
                $weightValue = 1200;
            }

            /**
             * @var $offer MarketPlaceDto
             */
            $offer = (new MarketPlaceDto())
                ->setLiveDate($liveDate)
                ->setAvailable($isAvailable)
                ->setId($this->param->marketSkuidPrefix . "" . $price->getMid())
                ->setProductId($me->getProduct()->getId())
                ->setProductArticle($me->getProduct()->getArticle())
                ->setProductUuid($me->getProduct()->getUuid())
                ->setPriceUuid($price->getUuid())
                ->setPriceMid($price->getMid())
                ->setNumber($price->getId())
                ->setName($this->helper->getNameFormat(
                    $collectionCarpet,
                    $formCarpet,
                    $colorCarpet,
                    $styleCarpet,
                    $countryCarpet,
                    $price->getWidth(),
                    $price->getHeight()
                ) . ', ' . $designCode)
                ->setVendor($brand)
                ->setVendorCode($me->getProduct()->getArticle())
                ->setUrl($this->param->carpetti_web . '/carpet?article=' . $me->getProduct()->getArticle())
                ->setPriceToMarket($priceScheme)
                ->setCurrencyId('RUR')
                ->setProductEan($this->helper->genEan($price->getId()))
                ->setQuantity($quantity)
                ->setCategoriesId($category)
                ->setCategoriesName($categoryNames)
                ->setLinkedItemNumbers($collectionNumberList)
                ->setLinkedItemIds($collectionUidList)
                ->setLinkedName(null !== $collection->getName() && '' !== $collection->getName()
                    ? \mb_substr($typeCarpet . ' ' . $collection->getName() . ', '. $countryCarpet, 0, 148)
                    : '')
                ->setPictures($images)
                ->setDescription(\sprintf('Коллекция %s предлагает ковер %s формы. '.
                    'Этот ковер изготовлен в %s с качеством %s и имеет %s цвет с %s дизайн. '.
                    'Это отличная находка для дома и дачи, которая обеспечит тепло и комфорт. '.
                    'Такой интерьерный аксессуар разных цветов очень приятен для ног, ходить по нему одно удовольствие. '.
                    'Интерьерное украшение очень легко чистить, предназначено как для влажной, так и для сухой уборки, для робота пылесоса. '.
                    'Товар полностью безопасен для здоровья и имеет необходимые сертификаты соответствия качества.',
                    null !== $collectionCarpet
                        ? $collectionCarpet
                        : 'CARPETTI.COLLECTION',
                    null !== $formCarpet
                        ? $formCarpet
                        : 'Приятной',
                    null !== $countryCarpet
                        ? $countryCarpet
                        : 'Россия',
                    null !== $qualityCarpet
                        ? $qualityCarpet
                        : 'FRIZE',
                    null !== $colorCarpet
                        ? $colorCarpet
                        : 'Приятный',
                    null !== $styleCarpet
                        ? $styleCarpet
                        : 'Актуальным'
                ))
                ->setCountryOfOrigin($countryCarpet)
                ->setWeight($this->helper->calculateWeight($price->getHeight(), $price->getWidth(), $weightValue))
                ->setStatCarpet($statCarpet)
                ->setStatDimension($this->helper->calculateSizePackage($price->getHeight(), $price->getWidth(), ceil($pile/10)))
                ->setCircle($isCircle)
                ->setPriceCount($price->getCount())
                ->setProductParams($prodParams)
                ->setCharacter($specEm)
            ;
            $offer->setCollectionCode($collection->getCode());
            $offer->setCollectionId($collection->getId());
            $offer->setCollectionName($collection->getName());
            $offer->setTags([]);

            /**
             * @description Применит текущие лимиты к пакету
             */
            $dimensionRebuild = $this->helper->getDimensions($this->priceLimiter, $price->getWidth(), $price->getHeight());
            if ($dimensionRebuild !== null && $dimensionRebuild instanceof TwistSizeDto) {
                $offer->getStatDimension()->setHeight($dimensionRebuild->getHeight());
                $offer->getStatDimension()->setWidth($dimensionRebuild->getWidth());
                $offer->getStatDimension()->setLength($dimensionRebuild->getLength());
            }

            $this->offersDto[] = $offer;
            $this->log->registerException(
                Logger::INFO,
                Constant::LOG_APPEND_MP,
                null,
                [
                    'append' => true,
                    'article' => $me->getProduct()->getArticle(),
                    'mid' => $this->param->marketSkuidPrefix . "" . $price->getMid(),
                    'count' => $price->getCount(),
                    'price' => $price->getPrice(),
                ]
            );
        }
    }

    public function run(string $article) {
        $product = $this->repo->findByArticle($article);

        if (!$product || !$product instanceof Product) {
            $this->log->registerException(Logger::ERROR, Constant::LOG_PREPARE_CARPET_TO_MARKET_FIND, null, ['article' => $article]);
            return;
        }

        $me = $this->repo->masterToProductWithCollection($product);

        if (!$me || !$me instanceof MasterEntity) {
            $this->log->registerException(Logger::ERROR, Constant::LOG_PREPARE_CARPET_TO_MARKET_FIND, null, ['article' => $article]);
            return;
        }

        try {
            $this->parse($me);
        }catch (\Exception $e) {
            $this->log->registerException(Logger::ERROR, Constant::LOG_AUTH_SESSION_ENDED, $e, ['article' => $me->getProduct()->getArticle()]);
        }
    }

    public function process(callable $fnc)
    {
        foreach ($this->offersDto as $offer) {
            $fnc($offer);
        }

        $this->rebuildMap();
    }

}
