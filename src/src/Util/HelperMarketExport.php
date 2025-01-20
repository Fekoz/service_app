<?php


namespace App\Util;

use App\Entity\Images;
use App\Entity\Options;
use App\Entity\Price;
use App\Entity\PriceExportDynamic;
use App\Entity\Specification;
use App\Repository\PriceRepository;
use App\Service\MarketExport;
use App\Util\Dto\MarketMappingDirectoryDto;
use App\Util\Dto\MarketMappingDto;
use App\Util\Dto\MarketPriceDto;
use App\Util\Dto\MarketPriceObjectDto;
use App\Util\Dto\MarketYmlDtoCategoryParent;
use App\Util\Dto\MarketYmlDtoOfferSimple;
use App\Util\Dto\TwistSizeDto;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferParam;
use Doctrine\ORM\Query\Expr\Math;

class HelperMarketExport
{
    const USAGE_SPEC_KEY = [
        Constant::SPEC_COUNTRY,
        Constant::SPEC_MATERIAL,
        Constant::SPEC_FORM,
        Constant::SPEC_STYLE,
        Constant::SPEC_COLLECTION,
    ];

    const NONE_NAME = 'Не указано';
    const DEFAULT_CARPET_LENGTH = 1.4;

    public function __construct()
    {

    }

    public function getActualSpecConst() : array
    {
        return self::USAGE_SPEC_KEY;
    }

    public function isActualSpec(string $key): bool
    {
        $isActual = false;
        foreach (self::USAGE_SPEC_KEY as $value) {
            if($key === Constant::SPECIFICATIONS[$value][Constant::SPEC_VALUE]) {
                $isActual = true;
            }
        }
        return $isActual;
    }

    public function findSpec(array $arr): ?string
    {
        $result = self::NONE_NAME;
        /**
         * @var $value Specification
         */
        foreach ($arr as $value) {
            if(Constant::SPECIFICATIONS[Constant::SPEC_COLLECTION][Constant::SPEC_VALUE] === $value->getName()) {
                $result = $value->getValue();
            }
        }
        return $result;
    }

    public function findPic(array $arr, string $param = ''): ?array
    {
        $result = [];
        /**
         * @var $value Images
         */
        foreach ($arr as $value) {
            if ($value->getType() === true) {
                $result[] = $value->getOriginalUrl();
            }
        }
        return $result;
    }

    public function findPicOrigin(array $arr, string $param = ''): ?array
    {
        $result = [];
        /**
         * @var $value Images
         */
        foreach ($arr as $value) {
            if ($value->getType() === true) {
                $result[] = $param . $value->getDir() . $value->getFilename();
            }
        }
        return $result;
    }

    public function getParentCategory(string $key, array $categoriesDto): ?int
    {
        $id = null;
        foreach ($categoriesDto as $cat) {
            if ($cat->getCategoryKey() === $key && $cat->isParent() === true) {
                $id = $cat->getId();
            }
        }

        return $id;
    }

    public function getParentCategoryFind(string $key, array $categoriesParentDto): ?int
    {
        $id = null;
        foreach ($categoriesParentDto as $cat) {
            if ($cat instanceof MarketYmlDtoCategoryParent && $cat->getKey() === $key) {
                $id = $cat->getId();
            }
        }

        return $id;
    }

    public function getCategory(string $value, array $categoriesDto): ?int
    {
        $id = null;
        foreach ($categoriesDto as $cat) {
            if ($cat->getCategoryValue() === $value) {
                $id = $cat->getId();
            }
        }

        return $id;
    }

    public function getCategoryName(string $value, array $categoriesDto): ?string
    {
        $id = null;
        foreach ($categoriesDto as $cat) {
            if ($cat->getCategoryValue() === $value) {
                $id = $cat->getName();
            }
        }

        return $id;
    }

    public function formatSpec(array $arr): array
    {
        $list = [];
        /**
         * @var $value Specification
         */
        foreach ($arr as $value) {
            foreach (Constant::SPECIFICATIONS as $specConst) {
                if($specConst[Constant::SPEC_VALUE] === $value->getName()) {
                    $list[] = (new OfferParam())
                        ->setName($specConst[Constant::SPEC_PARSE_SYM])
                        ->setValue($value->getValue());
                }
            }
        }
        return $list;
    }

    public function getNameFormat(string $collection, string $form, string $color, string $style, string $country, string $with, string $height): string
    {
        $name = \str_replace(
            [self::NONE_NAME, '  ', ', ,', '  ', '  ', ' , '],
            ' ',
            \sprintf('Ковер %s - %s %s, %s, Ковер на пол, в гостиную, спальню, в ассортименте, Турция, Бельгия, %s (%s см. на %s см.)', $collection, $form, $color, $style, $country, $with, $height)
        );

        if (\mb_strlen($name) >= 148) {
            return \mb_substr(
                \str_replace(
                    [self::NONE_NAME, '  ', ', ,', '  ', '  '],
                    ' ',
                    \sprintf('Ковер %s - %s %s, %s, %s (%s см. на %s см.)', $collection, $form, $color, $style, $country, $with, $height)
                ), 0, 148);
        }
        return $name;
    }

    public function findSpecificationParam(string $name, array $specification): string
    {
        $value = self::NONE_NAME;
        foreach ($specification as $spec) {
            /**
             * @var $spec Specification
             */
            if (Constant::SPECIFICATIONS[$name][Constant::SPEC_VALUE] === $spec->getName()) {
                $value = $spec->getValue();
            }
        }
        return $value;
    }

    /**
     * @throws \Exception
     */
    public function findMatchingOzonDto(array $mappingMarket, string $directoryName, string $key, string $value): ?MarketMappingDirectoryDto
    {
        foreach ($mappingMarket as $marketMappingDto) {
            if(!$marketMappingDto instanceof MarketMappingDto) {
                continue;
            }

            if($marketMappingDto->getDirect()->getValue() !== $directoryName) {
                continue;
            }

            if ($marketMappingDto->getKey() === $key && \in_array($value, $marketMappingDto->getValues())) {
                return $marketMappingDto->getOzon();
            }
        }

        throw new \Exception('In directory not found key:' . $key . ', value:' . $value);
    }

    public function getPriceUuidWithProduct(array $list, $repo): array
    {
        $uuids = [];
        foreach ($list as $productId) {
            /**
             * @var $repo PriceRepository
             */
            $item = $repo->getPriceUuid($productId);
            foreach ($item as $val) {
                if (isset($val['uuid'])) {
                    $uuids[] = $val['uuid'];
                }
            }
        }

        return $uuids;
    }

    public function getPriceMidWithProduct(array $list, $repo): array
    {
        $uuids = [];
        foreach ($list as $productId) {
            /**
             * @var $repo PriceRepository
             */
            $item = $repo->getPriceUuid($productId);
            foreach ($item as $val) {
                if (isset($val['mid'])) {
                    $uuids[] = $val['mid'];
                }
            }
        }

        return $uuids;
    }

    public function getPriceUuidWithProductTst(array $list, $repo): array
    {
        $uuids = [];
        foreach ($list as $productId) {
            /**
             * @var $repo PriceRepository
             */
            $item = $repo->getPriceUuid($productId);
            foreach ($item as $val) {
                if (isset($val['uuid'])) {
                    $uuids[] = $val['uuid'];
                }
            }
        }

        return $uuids;
    }

    public function getPriceUuidWithPrice(array $list): array
    {
        $uuids = [];
        /**
         * @var $item Price
         */
        foreach ($list as $item) {
            if($item instanceof Price) {
                $uuids[] = $item->getUuid();
            }
        }

        return $uuids;
    }

    public function getPriceMidWithPrice(array $list): array
    {
        $uuids = [];
        /**
         * @var $item Price
         */
        foreach ($list as $item) {
            if($item instanceof Price) {
                $uuids[] = $item->getMid();
            }
        }

        return $uuids;
    }

    public function addPrefixList(string $prefix, array $inputArray): array {
        return \array_map(function($item) use ($prefix) {
            return $prefix . $item;
        }, $inputArray);
    }

    public function getCategorySpecific(array $spec, array $categoriesDto): array
    {
        $arrayWith = [];
        foreach ($this->getActualSpecConst() as $key) {
            $item = $this->getCategory(
                $this->findSpecificationParam($key, $spec),
                $categoriesDto
            );
            if (null !== $item) {
                $arrayWith[] = $item;
            }
        }
        return $arrayWith;
    }

    public function getCategorySpecificName(array $spec, array $categoriesDto): array
    {
        $arrayWith = [];
        foreach ($this->getActualSpecConst() as $key) {
            $item = $this->getCategoryName(
                $this->findSpecificationParam($key, $spec),
                $categoriesDto
            );
            if (null !== $item) {
                $arrayWith[] = $item;
            }
        }
        return $arrayWith;
    }

    public function exactWeight(int $height, int $width, float $weight, bool $isKg): float
    {
        if (true === $isKg) {
            return \round($height * $width / 10000 * $weight / 1000, 2);
        }

        return \round($height * $width / 10000 * $weight, 2);
    }

    public function getWeight(string $value): float
    {
        return floatval(\preg_replace('/\W/', '', \stristr($value, './', true))) ?: 1000;
    }

    public function reformatSpec(string $value, string $sim): string
    {
        return \preg_replace('/\W/', '', \stristr($value, $sim, true));
    }

    public function getWeightOfPacking(float $weight): float
    {
        return $weight + Constant::WEIGHT_OF_PACKING;
    }

    public function calculateWeight(int $length, int $width, int $weight): float
    {
        $lengthInMeters = $length / 100;
        $widthInMeters = $width / 100;
        $area = $lengthInMeters * $widthInMeters;
        $weightInKg = $area * $weight / 1000;
        return \round($weightInKg, 2);
    }

    /**
     * @param int $wight Ширина
     * @param int $height Длина
     * @param int $pile Высота ворса
     * @return TwistSizeDto
     */
    public function calculateSizePackage(int $wight, int $height, int $pile): TwistSizeDto
    {
        $w = $wight;
        $h = $height;
        $p = $pile;

        if ($height > $wight) {
            $w = $height;
            $h = $wight;
        }

        $wh = \round(sqrt($p * $w / M_PI) / 2, 0, PHP_ROUND_HALF_UP) + TwistSizeDto::DEFAULT_PACKAGE;
        return (new TwistSizeDto())
            ->setLength($h + TwistSizeDto::DEFAULT_PACKAGE)
            ->setWidth($wh)
            ->setHeight($wh);
    }

    public function changeParamMarket(array $list, string $param): array
    {
        $arr = [];
        foreach ($list as $val) {
            $arr[] = $param . "" . $val;
        }

        return $arr;
    }

    public function appendLinked(array $list, array $linked = []): array
    {
        $isAppend = true;
        foreach ($linked as $key => $itemHead) {
            if ($itemHead) {
                foreach ($itemHead as $itemBody) {
                    foreach ($list as $value) {
                        if ($itemBody === $value) {
                            $linked[$key][] = $value;
                            $isAppend = false;
                        }
                    }
                }
            }
        }

        if ($isAppend === true) {
            $linked[] = $list;
        }

        foreach ($linked as $key => $itemHead) {
            $linked[$key] = \array_unique($itemHead);
        }

        return $linked;
    }

    public function getKeyWithLinked(array $linked, string $uid): ?int
    {
        $key = null;
        foreach ($linked as $k => $val) {
            foreach ($val as $value) {
                if ($uid === $value) {
                    $key = $k;
                }
            }
        }

        return $key;
    }

    public function genEan($number): string
    {
        $code = '200' . str_pad($number, 9, '0', STR_PAD_LEFT);
        $weightFlag = true;
        $sum = 0;
        for ($i = strlen($code) - 1; $i >= 0; $i--)
        {
            $sum += (int)$code[$i] * ($weightFlag?3:1);
            $weightFlag = !$weightFlag;
        }
        $code .= (10 - ($sum % 10)) % 10;
        return $code;
    }

    public function isAppendAndAddOrSumCount(array $offersDto, Price $price): bool
    {
        $isAppend = true;
        foreach ($offersDto as $offerItemToCount) {
            if ($offerItemToCount instanceof MarketYmlDtoOfferSimple && $offerItemToCount->getPriceMid() === $price->getMid()) {
                $countItem = $offerItemToCount->getPriceCount() + $price->getCount();
                $offerItemToCount->setPriceCount($countItem);

                $offerItemToCount->setAvailable(true);

                // TODO: COUNT MIN MARKET
                if ($countItem <= MarketExport::MIN_SIZE_TO_EXPORT) {
                    $offerItemToCount->setAvailable(false);
                }

                if ($price->getPrice() > $offerItemToCount->getPrice()) {
                    $offerItemToCount->setPrice($price->getPrice());
                    $offerItemToCount->setOldPrice($price->getPrice());
                }

                $isAppend = false;
            }
        }

        return $isAppend;
    }

    public function getCountToQuantityWithCountryAndSize(int $w, int $h, string $c, int $i): int
    {
        if ($w <= 60 && $h <= 110) {
            return 4;
        }

        if ($w <= 80 && $h <= 150 && $c === 'Россия') {
            return 4;
        }

        return $i;
    }

    public function marketPriceCreate(MarketPriceObjectDto $price, MarketPriceObjectDto $priceOld, ?array $options): MarketPriceDto
    {
        /**
         * @var $option Options
         */
        foreach ($options as $option) {
            switch ($option->getName()) {
                // Price
                case Constant::DEFAULT_PRICE_FACTOR:
                    $price->setFactor($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_MARKUP:
                    $price->setMarkup($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_RANDOM:
                    $isRandom = 'true' === $option->getValue();
                    $price->setRandom($isRandom);
                    break;
                case Constant::DEFAULT_PRICE_RNDMIN:
                    $price->setRndMin($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_RNDMAX:
                    $price->setRndMax($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_FACTOR_OZON:
                    $price->setOzonFactor($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_FACTOR_YANDEX:
                    $price->setYandexFactor($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_FACTOR_WILDBERRIES:
                    $price->setWildberriesFactor($option->getValue());
                    break;
                // Price Old
                case Constant::DEFAULT_PRICE_OLD_FACTOR:
                    $priceOld->setFactor($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_OLD_MARKUP:
                    $priceOld->setMarkup($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_OLD_RANDOM:
                    $isRandom = 'true' === $option->getValue();
                    $priceOld->setRandom($isRandom);
                    break;
                case Constant::DEFAULT_PRICE_OLD_RNDMIN:
                    $priceOld->setRndMin($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_OLD_RNDMAX:
                    $priceOld->setRndMax($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_OLD_FACTOR_OZON:
                    $priceOld->setOzonFactor($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_OLD_FACTOR_YANDEX:
                    $priceOld->setYandexFactor($option->getValue());
                    break;
                case Constant::DEFAULT_PRICE_OLD_FACTOR_WILDBERRIES:
                    $priceOld->setWildberriesFactor($option->getValue());
                    break;
            }
        }

        return (new MarketPriceDto())
            ->setPrice($price)
            ->setPriceOld($priceOld);
    }

    public function getDimensions(array $arr, int $w, int $h): ?TwistSizeDto
    {
        $size = null;
        foreach ($arr as $value) {
            if ($value instanceof PriceExportDynamic) {
                if (
                    $w >= $value->getMinWidth() &&
                    $w <= $value->getMaxWidth() &&
                    $h >= $value->getMinHeight() &&
                    $h <= $value->getMaxHeight()) {
                    $size = (new TwistSizeDto())
                        ->setHeight($value->getPackageHeight())
                        ->setWidth($value->getPackageWidth())
                        ->setLength($value->getPackageDepth());
                }
            }
        }
        return $size;
    }

}
