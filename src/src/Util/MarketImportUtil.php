<?php

namespace App\Util;

use App\Entity\Images;
use App\Entity\Price;
use App\Entity\Specification;
use App\Util\Tools\MasterEntity;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class MarketImportUtil
{
    const DEFAULT_CARD_NUMBER = 10000000;
    const DEFAULT_ARTICLE_PREFIX = 'cr';
    /**
     * @var array
     */
    private $incId;

    const NONE_NAME = 'Не указано';
    const PRICE_KF = 1.3;
    const PRICE_KF_ARRAY = [
        1.4,
        1.6,
        1.8,
        2.0,
        2.2,
        2.4,
        2.6,
        2.8,
    ];
    const PRODUCT_SURCHARGE = 4000;

    const PLE_MIN = 100;

    const RAND_INFO = [
        'Стильные ковры любых размеров.',
        'Цвет ковра очень универсален!',
        'Огромное количество цветовых комбинаций!',
        'Примерка ковров в интерьере - удобная услуга.',
        'Выбор наиболее подходящего ковра уже в интерьере!',
        'Не скучная и стильная деталь вашего интерьера!',
        'Ковры в любые города за наш счёт!',
        'Стильные и необычные ковры - наша фишка!',
        'Ковёр с самыми популярными интерьерными оттенками',
        'Новая стильная модель в коллекции!',
        'Стильный ковёр, невероятная игра оттенков!',
        'Ковёр точно станет важной деталью в интерьере!',
        'Ковёр очень красиво бликует под разным углом!',
        'Тот случай, когда ничего лишнего. Стильный ковёр!',
    ];

    const NULL_IMAGE = 'http://external.carpetti.vip/export/image/carpetti-logo.png';

    /**
     * @return \DateTime
     */
    public function getCurrentDate(): \DateTime
    {
        return new \DateTime();
    }

    private function translit(string $article): string
    {
        return \strtoupper(\strtr($article, Constant::ARTICLE_FORMATTER));
    }

    private function reformatSpec(string $value, string $sim): string
    {
        return \preg_replace('/\W/', '', \stristr($value, $sim, true));
    }

    public function generateInfo(): string
    {
        return self::RAND_INFO[rand(0,count(self::RAND_INFO)-1)];
    }

    private function findSpecificationParam(string $name, array $specification): string
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

    private function getWeight(string $height, string $width, array $item): string
    {
        return $this->reformatSpec($this->findSpecificationParam(Constant::SPEC_WEIGHT, $item), './') ?: 1000;
    }

    private function makeArticle(string $id, string $name): string
    {
        return $id . '-' . $this->translit($name);
    }

    private function getImageToDo(MasterEntity $item): string
    {
        $image = [];
        foreach ($item->getImage() as $value) {
            /**
             * @var $value Images
             */
            if (true === $value->getType()) {
                $image[] = $value->getOriginalUrl();
            }
        }
        return \implode(' | ', $image);
    }

    private function getImage(MasterEntity $item, bool $isPreview): string
    {
        $image = [];
        if ($isPreview) {
            foreach ($item->getImage() as $value) {
                /**
                 * @var $value Images
                 */
                if (true === $value->getType()) {
                    $image[] = $value->getOriginalUrl();
                }
            }
            return \implode(',', $image);
        }

        foreach ($item->getImage() as $value) {
            /**
             * @var $value Images
             */
            if (true === $value->getType()) {
                $image = $value->getOriginalUrl();
                break;
            }
        }
        $setImage = !$image ? '' : $image;

        return count($item->getImage()) > 1 ? $setImage : '';
    }

    private function getIdPrice(int $oldPrice, string $width, string $height): string
    {
        return \strtoupper(\substr(\md5($oldPrice . '_' . $width . '_' . $height), 0, 6));
    }

    private function getNameFormat(string $name, string $color, string $width, string $height): string
    {
        return \sprintf('%s, %s (%s на %s)', $name, $color, $width, $height);
    }

    private function exactWeight(int $height, int $width, float $weight): float
    {
        return \round($height * $width / 10000 * $weight, 2);
    }

    /**
     * @param MasterEntity $item
     * @param string $toPrefix
     * @return array|null
     */
    public function convertMarketAssortment(MasterEntity $item, string $toPrefix = self::DEFAULT_ARTICLE_PREFIX): ?array
    {
        /**
         * @var $price Price
         */
        $price = $item->getPrice()[0];

        $theWeight = $this->getWeight($price->getHeight(), $price->getWidth(), $item->getSpecification());
        $mid = $toPrefix . '-' . $price->getMid();
        $city = $this->findSpecificationParam(Constant::SPEC_COUNTRY, $item->getSpecification());
        $info = $price->getUuid();
        $count = $item->getProduct()->isActive() ? $price->getCount() : 0;
        $status = $item->getProduct()->isActive() ? 'В наличии' : 'На заказ';
        $pile = $this->reformatSpec($this->findSpecificationParam(Constant::SPEC_PILE, $item->getSpecification()), ' мм');

        if (!$pile) {
            $pile = '10';
        }

        if ($theWeight < 1) {
            $theWeight = 1;
        }

        $packingHeight = \round($price->getWidth());
        $packingWidth = \round($price->getHeight() / 2) >= $packingHeight ? $packingHeight : \round($price->getHeight() / 2);
        $packingSize = \round($price->getWidth() / $price->getHeight() / $pile + 1.5);

        $priceKf = self::PRICE_KF_ARRAY[rand(0, count(self::PRICE_KF_ARRAY)-1)];
        return [
            'A' => '',
            'B' => '',
            'C' => $mid,
            'D' => $this->getNameFormat(
                $item->getProduct()->getName(),
                $this->findSpecificationParam(Constant::SPEC_COLOR, $item->getSpecification()),
                $price->getWidth(),
                $price->getHeight()
            ),
            'E' => $this->getImage($item, true) ?: self::NULL_IMAGE,
            'F' => $info,
            'G' => 'Ковер ' . $this->findSpecificationParam(Constant::SPEC_COLLECTION, $item->getSpecification()),
            'H' => $this->findSpecificationParam(Constant::SPEC_COLLECTION, $item->getSpecification()),
            'I' => '',
            'J' => '', //$packingHeight . '/' . $packingWidth . '/' . $packingSize,
            'K' => \round(($this->exactWeight($price->getHeight(), $price->getWidth(), $theWeight) + 0.6) / 1000),
            'L' => 'Стенд' === $city ? 'Россия' : $city,
            'M' => '',
            'N' => '',
            'O' => '',
            'P' =>
                'Высота ворса|' .  $pile . 'мм;'.
                'Цвет|'. $this->findSpecificationParam(Constant::SPEC_COLOUR, $item->getSpecification()) . ';' .
                'Форма|' . $this->findSpecificationParam(Constant::SPEC_FORM, $item->getSpecification()) . ';' .
                'Тип рисунка|' . $this->findSpecificationParam(Constant::SPEC_STYLE, $item->getSpecification()) . ';' .
                'Материал|' . $this->findSpecificationParam(Constant::SPEC_TYPE, $item->getSpecification()) . ';' .
                'Материал производства|' . $this->findSpecificationParam(Constant::SPEC_MATERIAL, $item->getSpecification()) . ';' .
                'Способ производства|' . $this->findSpecificationParam(Constant::SPEC_PREPARATION, $item->getSpecification()) . ';' .
                'Идентификатор|' . $item->getProduct()->getUuid() . ';' .
                'Mid|' . $mid
            ,
            'Q' => '',
            'R' => (int) \round($price->getPrice()) + self::PRODUCT_SURCHARGE,
            'S' => (int) \round($price->getPrice() * $priceKf) + self::PRODUCT_SURCHARGE,
            'T' => 'RUR',
            'U' => 'NO_VAT',
            'V' => 'Персональная консультация и индивидуальный размер',
            'W' => 'Можно',
            'X' => '',
            'Y' => $status,
            'Z' => $count,
            'AA' => 'Есть',
            'AB' => '',
            'AC' => 'Есть',
            'AD' => '',
        ];
    }

    /**
     * @param string $form
     * @return string
     */
    private function getFormFormat(string $form): string
    {
        switch ($form) {
            case 'Прямоугольник':
            case 'Дорожка':
                $name = 'прямоугольная';
                break;
            case 'Круг':
                $name = 'круглая';
                break;
            case 'Овал':
                $name = 'овальная';
                break;
            default:
                $name = 'нестандартная';
        }
        return $name;
    }

    /**
     * @param string $form
     * @return string
     */
    private function getTypeFormat(string $form): string
    {
        switch ($form) {
            case 'Дорожка':
                $name = 'ковровая дорожка';
                break;
            default:
                $name = 'ковер';
        }
        return $name;
    }

    /**
     * @param MasterEntity $item
     * @param int $id
     * @param int $prefixId
     * @param string $toPrefix
     * @return array
     */
    public function convertMarketPartner(MasterEntity $item, int $id, int $prefixId = self::DEFAULT_CARD_NUMBER, string $toPrefix = self::DEFAULT_ARTICLE_PREFIX): array
    {
        /**
         * @var $price Price
         */
        $price = $item->getPriceOne(0);
        $mid = $toPrefix . '-' . $price->getMid();

        $diameter = null;
        if ('Круг' === $this->findSpecificationParam(Constant::SPEC_FORM, $item->getSpecification())) {
            if ($price->getWidth() !== $price->getHeight()) {
                $price->setHeight($price->getWidth());
            }
            $diameter = \round($price->getWidth() / 100 / M_PI, 2);
        }

        $theWeight = $this->getWeight($price->getHeight(), $price->getWidth(), $item->getSpecification());
        $article = $item->getProduct()->getArticle();
        $city = $this->findSpecificationParam(Constant::SPEC_COUNTRY, $item->getSpecification());
        $pile = $this->reformatSpec($this->findSpecificationParam(Constant::SPEC_PILE, $item->getSpecification()), ' мм');
        $info = $price->getUuid();

        if ($theWeight < 1) {
            $theWeight = 1;
        }

        $ple = ((int) $this->reformatSpec($this->findSpecificationParam(Constant::SPEC_DENSITY, $item->getSpecification()), '/')) / 100;

        if($ple <= self::PLE_MIN) {
            $ple += self::PLE_MIN;
        }

        $genId = $prefixId + $id;

        $pile = 'Не указано' === $pile ? 1 : (int) $pile;
        if (!empty($pile) && $pile >= 65) {
            $pile = 65;
        }

        if($pile < 1) {
            $pile = 1;
        }

        $typeCreate = 'машинный';
        if ('Ручная работа' === $this->findSpecificationParam(Constant::SPEC_PREPARATION, $item->getSpecification())) {
            $typeCreate = 'ручной';
        }

        return [
            'A' => $mid,
            'B' => '',//$genId,
            'C' => '',//$item->getProduct()->getName(),
            'D' => $this->getNameFormat(
                $item->getProduct()->getName(),
                $this->findSpecificationParam(Constant::SPEC_COLOR, $item->getSpecification()),
                $price->getWidth(),
                $price->getHeight()
            ),
            'E' => $this->findSpecificationParam(Constant::SPEC_COLLECTION, $item->getSpecification()),
            'F' => $this->getImage($item, false) ?: self::NULL_IMAGE,
            'G' => $info,
            'H' => $this->translit($item->getProduct()->getArticle()) . '-' . $id,
            'I' => '',
            'J' => '',
            'K' => !$diameter ? '' : $diameter,
            'L' => $this->getFormFormat($this->findSpecificationParam(Constant::SPEC_FORM, $item->getSpecification())),
            'M' => $this->findSpecificationParam(Constant::SPEC_COLOUR, $item->getSpecification()),
            'N' => $this->findSpecificationParam(Constant::SPEC_COLOUR, $item->getSpecification()),
            'O' => \round($price->getHeight() / 100, 2),
            'P' => '',
            'Q' => \round($this->exactWeight($price->getHeight(), $price->getWidth(), $theWeight) / 1000, 2),
            'R' => \round($price->getWidth() / 100, 2),
            'S' => $this->getTypeFormat($this->findSpecificationParam(Constant::SPEC_FORM, $item->getSpecification())),
            'T' => 'Не указано' === $this->findSpecificationParam(Constant::SPEC_STYLE, $item->getSpecification())
                ? 'классический'
                : \mb_strtolower($this->findSpecificationParam(Constant::SPEC_STYLE, $item->getSpecification()))
            ,
            'U' => $this->findSpecificationParam(Constant::SPEC_MATERIAL, $item->getSpecification()),
            'V' => '',
            'W' => $typeCreate,
            'X' => '',
            'Y' => 'Нет',
            'Z' => \round($ple),
            'AA' => $pile,
            'AB' => $theWeight,
            'AC' => 'Нет',
            'AD' => '',
            'AE' => 'Стенд' === $city ? 'Россия' : $city,
            'AF' => '',
            'AG' => 'качество ковра: ' .
                $this->findSpecificationParam(Constant::SPEC_QUALITY, $item->getSpecification()) .
                ';состав ковра: ' .
                $this->findSpecificationParam(Constant::SPEC_COMPOSITION, $item->getSpecification()) .
                ';',
        ];
    }

    public function convertAvito(MasterEntity $item, string $toPrefix = self::DEFAULT_ARTICLE_PREFIX, string $region = ''): ?array
    {
        /**
         * @var $price Price
         */
        $price = $item->getPrice()[0];

        $mid = $toPrefix . '-' . $price->getMid();
        $city = $this->findSpecificationParam(Constant::SPEC_COUNTRY, $item->getSpecification());
        $city = 'Стенд' === $city ? 'Россия' : $city;
        $info = $price->getUuid();

        $ple = ((int) $this->reformatSpec($this->findSpecificationParam(Constant::SPEC_DENSITY, $item->getSpecification()), '/'));

        if($ple <= self::PLE_MIN) {
            $ple += self::PLE_MIN;
        }

        $carpetInfo =
            "Ковер из коллекции " . $this->findSpecificationParam(Constant::SPEC_COLLECTION, $item->getSpecification()) .
            "\r\nРазмер: " . (int) $price->getWidth() . " х " . (int) $price->getHeight() . " см." .
            "\r\nЦвет: " . $this->findSpecificationParam(Constant::SPEC_COLOUR, $item->getSpecification()) .
            "\r\nСтрана-производитель: " . $city .
            "\r\nСостав: " . $this->findSpecificationParam(Constant::SPEC_TYPE, $item->getSpecification()) . ', ' . $this->findSpecificationParam(Constant::SPEC_MATERIAL, $item->getSpecification()) .
            "\r\nФорма: " . $this->findSpecificationParam(Constant::SPEC_FORM, $item->getSpecification()) .
            "\r\nСпособ производства: " . $this->findSpecificationParam(Constant::SPEC_PREPARATION, $item->getSpecification()) .
            "\r\nПлотность: " . $ple .
            "\r\nКод ковра: " . $mid .
            "\r\nПодробнее: " . $this->findSpecificationParam(Constant::SPEC_COMPOSITION, $item->getSpecification()) .
            "\r\n- " . $info .
            "\r\n" .
            "\r\n═════════════════════════════════════════════" .
            "\r\n" .
            "\r\nШирокий размерный ряд, уточняйте информацию в профиле по нужному для Вас размеру." .
            "\r\nБолее 10000 ковров в наличии" .
            "\r\nОпытные консультанты, продаем ковры уже больше 4 лет" .
            "\r\n" .
            "\r\n═════════════════════════════════════════════" .
            "\r\n" .
            "\r\n✔️ Бесплатная доставка по Москве" .
            "\r\n✔️ Бесплатная доставка по МО при сумме заказа от 10 тыс." .
            "\r\n✔️ Бесплатная примерка" .
            "\r\n✔️ Доставка по всей России" .
            "\r\n" .
            "\r\nНуждаетесь в подборе ковра?" .
            "\r\n- Тогда прямо СЕЙЧАС пишите нам в личные сообщения. Наша команда дизайнеров подберёт ковёр в Ваш интерьер!" .
            "\r\n" .
            "\r\n═════════════════════════════════════════════" .
            "\r\n" .
            "\r\nМожем подобрать не стандартный индивидуальный размер ковра под Ваши параметры." .
            "\r\n«««««📞 Позвоните нам! Ответим на все Ваши вопросы! 📞»»»»»" .
            "\r\nДобавьте объявление в избранные ❤, что бы не потерять!" .
            "\r\nС уважением магазин ковров CARPETTI!"
        ;

        return [
            'A' => $mid,
            'B' => '',
            'C' => 'Free',
            'D' => 'Мебель и интерьер',
            'E' => 'Текстиль и ковры',
            'F' => $region,
            'G' => 'Ковер из коллекции ' . $this->findSpecificationParam(Constant::SPEC_COLLECTION, $item->getSpecification()),
            'H' => $carpetInfo,
            'I' => 'Новое',
            'J' => (int) \round($price->getPrice()),
            'K' => '',
            'L' => 'Вячеслав Ерёменко',
            'M' => '+7 977 126-04-18',
            'N' => 'Товар от производителя',
            'O' => $this->getImageToDo($item) ?: self::NULL_IMAGE,
        ];
    }

    public function calculatePrice(array $priceItem, string $mid, string $info, int $marketFactor = 0): ?Price
    {
        $isError = false;
        $iterate = \count($priceItem);
        $width = 0;
        $height = 0;
        $count = 0;
        $price = 0;
        $productId = 0;
        foreach ($priceItem as $val) {
            if (null === $val) {
                $isError = true;
                continue;
            }
            /**
             * @var $val Price
             */
            $width += $val->getWidth();
            $height += $val->getHeight();
            $count += $val->getCount();
            $price += $val->getPrice();
            $productId = $val->getProductId();
        }

        if ($isError) {
            return null;
        }

        $newPrice = $marketFactor + ($price / $iterate);

        $generate = new Price();
//        $generate = new Price(
//            $width / $iterate,
//            $height / $iterate,
//            $count,
//            $newPrice,
//            Constant::PRICES_CM,
//            $newPrice,
//            $info,
//            $mid,
//            Constant::STORAGE_0
//        );

        $generate->setProductId($productId);
        return $generate;
    }

}
