<?php


namespace App\Util;

use App\Service\Bot;
use App\Util\Dto\MarketYmlDtoOfferSimple;
use App\Util\Dto\MarketYmlDtoParam;
use PhpOffice\PhpSpreadsheet\IOFactory;

class HelperMarketMapGenXlsx implements HelperMarketMapGenInterface
{
    const PREFIX_NAME = 'key';
    const POINT_PARTNER_FILE = 7;

    const FORM_MAPPING = [
        "Стенд" => "квадратная",
        "Круг" => "круглая",
        "Дорожка" => "прямоугольная",
        "Овал" => "овальная",
        "Нитка" => "квадратная",
        "Картина" => "квадратная",
        "Прямоугольник" => "прямоугольная",
        null => "неприменимо",
    ];

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

    const NONE_NAME = 'Не указано';

    private function getKeyWithLinkedNum(string $uid): ?int
    {
        $key = null;
        foreach ($this->linked as $k => $val) {
            foreach ($val as $value) {
                if ($uid === $value) {
                    $key = 1000000 + $k;
                }
            }
        }

        return $key;
    }

    private function getKeyWithLinked(string $uid, string $collection, string $form, string $style): ?string
    {
        $key = null;
        foreach ($this->linked as $k => $val) {
            foreach ($val as $value) {
                if ($uid === $value) {
                    $key = \sprintf("%s.%s_%s_%s.%s", self::PREFIX_NAME, \mb_strtolower($collection), \mb_strtolower($form), \mb_strtolower($style), $k);
                }
            }
        }

        return $key;
    }

    private function getSplitParam(int $val, array $list = []): string
    {
        $item = '';
        foreach ($list as $param) {
            if ($param instanceof MarketYmlDtoParam) {
                if ($param->getName() === Constant::SPECIFICATIONS[$val][Constant::SPEC_VALUE]) {
                    $item = $param->getValue();
                }
            }
        }

        return $item;
    }

    private function reformatParamString(string $string): string
    {
        $name = \explode(" ", $string);
        if (is_array($name)) {
            return $name[0];
        }
        return $name;
    }

    private function getName(string $name, string $country = ''): string
    {
        // TODO: IS 150 SYMBOL
        return \mb_substr('Ковер ' . $name . ', '. $country, 0, 148);
    }

    private function arrayWith(MarketYmlDtoOfferSimple $val): array
    {
        $getLinked = $this->getKeyWithLinked(
            $val->getId(),
            $this->getSplitParam(Constant::SPEC_COLLECTION, $val->getProductParams()),
            $this->getSplitParam(Constant::SPEC_FORM, $val->getProductParams()),
            $this->getSplitParam(Constant::SPEC_STYLE, $val->getProductParams())
        );

        //$getLinkedNum = $this->getKeyWithLinkedNum($val->getId());
        $getLinkedNum = $val->getCollectionId();
        $getNameCategory = null !== $val->getCollectionName() && '' !== $val->getCollectionName()
            ? $this->getName($val->getCollectionName(), $this->getSplitParam(Constant::SPEC_COUNTRY, $val->getProductParams()))
            : '';

        $imageOne = Constant::NULL_IMAGE;
        $images = [];
        foreach($val->getPictures() as $k => $img) {
            if ($k === 0) {
                $imageOne = $img;
            }
            $images[] = $img;
        }

        $typeCreate = 'машинный';
        if ('Ручная работа' === $this->getSplitParam(Constant::SPEC_PREPARATION, $val->getProductParams())) {
            $typeCreate = 'ручной';
        }

        $pileDensity = $this->reformatParamString($this->getSplitParam(Constant::SPEC_DENSITY, $val->getProductParams()));
        if ($pileDensity < 100) {
            $pileDensity = 100;
        }

        if ($pileDensity > 2100000) {
            $pileDensity = 2100000;
        }

        return [
            /**
             * @Market:Ваш SKU
             */
            'A' => $val->getId(),

            /**
             * @Market:Рейтинг карточки
             */
            'B' => '',

            /**
             * @Market:Рекомендации по заполнению
             */
            'C' => '',

            /**
             * @Market:Номер карточки*
             */
            'D' => null !== $getLinkedNum ? $getLinkedNum : '',

            /**
             * @Market:Название товара
             */
            'E' => $val->getName(),

            /**
             * @Market:Ссылки на изображения *
             */
            'F' => \implode(",", $images),

            /**
             * @Market:Изображение для миниатюры
             */
            'G' => $imageOne,

            /**
             * @Market:Описание товара
             */
            'H' => $val->getDescription(),

            /**
             * @Market:Бренд
             */
            'I' => $val->getVendor(),

            /**
             * @Market:Штрихкод
             */
            'J' => $val->getProductEan(),

            /**
             * @Market:Теги
             */
            'K' => $this->getSplitParam(Constant::SPEC_CARPET_TYPE, $val->getProductParams()) . "," . \implode(",", $val->getTags()),

            /**
             * @Market:Ссылка на видео
             */
            'L' => '',

            /**
             * @Market:Страна производства
             */
            'M' => $this->getSplitParam(Constant::SPEC_COUNTRY, $val->getProductParams()),

            /**
             * @Market:Артикул производителя
             */
            'N' => $val->getVendorCode(),

            /**
             * @Market:Вес с упаковкой, кг
             */
            'O' => \round(floatval($val->getWeight()) + 0.6, 2),

            /**
             * @Market:Габариты с упаковкой, см
             */
            'P' => $val->getStatDimension()->getHeight() . "/" . $val->getStatDimension()->getWidth() . "/" . $val->getStatDimension()->getLength(),

            /**
             * @Market:Товар занимает больше одного места
             */
            'Q' => '',

            /**
             * @Market:Основная цена *
             */
            'R' => $val->getPrice(),

            /**
             * @Market:Цена до скидки
             */
            'S' => $val->getOldPrice(),

            /**
             * @Market:Себестоимость
             */
            'T' => \ceil(\floatval($val->getPrice()) * (70 / 100)),

            /**
             * @Market:Дополнительные расходы
             */
            'U' => \ceil(\floatval($val->getPrice()) * (15 / 100)),

            /**
             * @Market:Срок годности
             */
            'V' => '10 лет',

            /**
             * @Market:Комментарий к сроку годности
             */
            'W' => '',

            /**
             * @Market:Срок службы
             */
            'X' => '30 лет',

            /**
             * @Market:Комментарий к сроку службы
             */
            'Y' => '',

            /**
             * @Market:Гарантийный срок
             */
            'Z' => '1 год',

            /**
             * @Market:Комментарий к гарантийному сроку
             */
            'AA' => '',

            /**
             * @Market:Номер документа на товар
             */
            'AB' => '',

            /**
             * @Market:Код ТН ВЭД
             */
            'AC' => '',

            /**
             * @Market:Тип уценки
             */
            'AD' => '',

            /**
             * @Market:Внешний вид товара
             */
            'AE' => '',

            /**
             * @Market:Описание состояния товара
             */
            'AF' => '',

            /**
             * @Market:SKU на Маркете
             */
            'AG' => '',

            /**
             * @Market:Не использовать общий медиаконтент
             */
            'AH' => '',

            /**
             * @Market:В архиве
             */
            'AI' => '',

            /**
             * @Market:Длина, м
             */
            'AJ' => ($val->getProductH() / 100) > 0
                ? $val->getProductH() / 100
                : 1
            ,

            /**
             * @Market:Количество в наборе, шт.
             */
            'AK' => '1',

            /**
             * @Market:Форма
             */
            'AL' => self::FORM_MAPPING[$this->getSplitParam(Constant::SPEC_FORM, $val->getProductParams())] ?? self::FORM_MAPPING[null],

            /**
             * @Market:Цвет товара для карточки
             */
            'AM' => '' !== $this->getSplitParam(Constant::SPEC_COLOUR, $val->getProductParams())
                ? $this->getSplitParam(Constant::SPEC_COLOUR, $val->getProductParams())
                : $this->getSplitParam(Constant::SPEC_COLOR, $val->getProductParams())
            ,

            /**
             * @Market:Ширина, м
             */
            'AN' => ($val->getProductW() / 100) > 0
                ? $val->getProductW() / 100
                : 1
            ,

            /**
             * @Market:Вес, кг
             */
            'AO' => $val->getWeight() > 0
                ? $val->getWeight()
                : 1
            ,

            /**
             * @Market:Диаметр, м
             */
            'AP' => true === $val->isCircle()
                ? ($val->getProductW() / 100) > 0
                    ? $val->getProductW() / 100
                    : 1
                : '',

            /**
             * @Market:Толщина, мм
             */
            'AQ' => '',

            /**
             * @Market:Цвет товара для фильтра
             */
            'AR' => '' !== $this->getSplitParam(Constant::SPEC_COLOUR, $val->getProductParams())
                ? $this->getSplitParam(Constant::SPEC_COLOUR, $val->getProductParams())
                : $this->getSplitParam(Constant::SPEC_COLOR, $val->getProductParams())
            ,

            /**
             * @Market:Тип
             */
            'AS' => \mb_strtolower($this->getSplitParam(Constant::SPEC_CARPET_TYPE, $val->getProductParams())),

            /**
             * @Market:Тип рисунка
             */
            'AT' => \mb_strtolower($this->getSplitParam(Constant::SPEC_STYLE, $val->getProductParams())),

            /**
             * @Market:Материал верха
             */
            'AU' => $this->getSplitParam(Constant::SPEC_MATERIAL, $val->getProductParams()),

            /**
             * @Market:Материал основы
             */
            'AV' => $this->getSplitParam(Constant::SPEC_QUALITY, $val->getProductParams()),

            /**
             * @Market:Способ производства
             */
            'AW' => $typeCreate,

            /**
             * @Market:Противоскользящая основа
             */
            'AX' => '',

            /**
             * @Market:Без ворса
             */
            'AY' => '',

            /**
             * @Market:Плотность ворса на квадратный метр, точек
             */
            'AZ' => $pileDensity,

            /**
             * @Market:Вес ворса на квадратный метр, г/м²
             */
            'BA' => '',

            /**
             * @Market:Высота ворса, мм
             */
            'BB' => $this->reformatParamString($this->getSplitParam(Constant::SPEC_PILE, $val->getProductParams())),

            /**
             * @Market:Вес на квадратный метр, г/м²
             */
            'BC' => $this->reformatParamString($this->getSplitParam(Constant::SPEC_WEIGHT, $val->getProductParams())),

            /**
             * @Market:Набор
             */
            'BD' => 'Нет',

            /**
             * @Market:Страна производства
             */
            'BE' => $this->getSplitParam(Constant::SPEC_COUNTRY, $val->getProductParams()),

            /**
             * @Market:Подробная комплектация
             */
            'BF' => '',

            /**
             * @Market:Дополнительная информация
             */
            'BG' => \sprintf("качество: %s; код цвета: %s; код дизайна: %s; коллекция: %s; состав: %s;",
                \mb_strtolower($this->getSplitParam(Constant::SPEC_QUALITY, $val->getProductParams())),
                \mb_strtolower($this->getSplitParam(Constant::SPEC_COLOR, $val->getProductParams())),
                \mb_strtolower($this->getSplitParam(Constant::SPEC_DESIGN, $val->getProductParams())),
                \mb_strtolower($this->getSplitParam(Constant::SPEC_COLLECTION, $val->getProductParams())),
                \mb_strtolower($this->getSplitParam(Constant::SPEC_COMPOUND, $val->getProductParams()))
            ),
        ];
    }

    public function generate()
    {
        $array = [];
        foreach ($this->offersDto as $value) {
            if ($value instanceof MarketYmlDtoOfferSimple) {
                $array[] = $this->arrayWith($value);
            }
        }
        try {
            $this->importData($array);
        } catch (\Exception $e) {
            return;
        }
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

    /**
     * @param array $item
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function importData(array $item): void
    {
        if (!file_exists($this->fileDir . '/export/xlsx/')) {
            mkdir($this->fileDir . '/export/xlsx/', 0777, true);
        }

        $date = \md5((new \DateTime())->format(DATE_ATOM));
        try {
            $reader = IOFactory::createReader('Xlsx');
        }catch (\Exception $e) {
            return;
        }
        $spreadsheet = $reader->load($this->fileDir . '/import/xlsx/' . $this->fileName);
        $worksheet = $spreadsheet->getSheetByName('Данные о товарах');
        $index = self::POINT_PARTNER_FILE;
        foreach ($item as $val) {
            foreach ($val as $key => $value) {
                $worksheet->setCellValue($key.$index, $value);
            }
            $index++;
        }
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = $this->fileDir . '/export/xlsx/' . $date .  '_' . $this->fileName;
        $writer->save($fileName);
        $this->bot->message(null, \sprintf('Обновил файл с карточками товаров. Доступен под названием [%s].', $date .  '_' . $this->fileName));
    }
}
