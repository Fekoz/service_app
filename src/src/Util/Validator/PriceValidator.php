<?php

namespace App\Util\Validator;

use App\Entity\Price;
use App\Service\Queue;
use App\Util\Constant;
use App\Util\Dto\ParserDto\ParseItemPriceDto;
use App\Util\Dto\ParserDto\ParseItemPriceOptionsDto;
use App\Util\Dto\PriceValidatorSizePrepareDto;
use App\Util\Dto\SenderMarketExportDto;
use App\Util\Dto\ValidatorConfigDto;
use App\Util\Tools\MasterEntity;
use App\Util\ValidatorInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Serializer\SerializerInterface;

class PriceValidator implements ValidatorInterface
{
    const DROP_PRICE_BLOCK = '<del>';

    const PACKAGE_QUEUE_NAME = 'package';
    /**
     * @var float
     */
    private $factor;

    /**
     * @var MasterEntity
     */
    private $master;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(Queue $queue, SerializerInterface $serializer)
    {
        $this->queue = $queue;
        $this->serializer = $serializer;
        $this->queue->init(self::PACKAGE_QUEUE_NAME, 'price export limiter');
    }

    public function import(MasterEntity $master)
    {
        $this->master = $master;
    }

    /**
     * @param ValidatorConfigDto $config
     */
    public function setConfig(ValidatorConfigDto $config)
    {
        $this->factor = $config->getFactor();
    }

    /**
     * @param string $text
     * @return string
     */
    public function priceValidate(string $text) :string
    {
        return \preg_replace('/\W/', '', $text);
    }

    private function getPackage(string $text): ?int
    {
        $number = null;
        $startIndex = strpos($text, 'упаковка по');
        if ($startIndex !== false) {
            $substring = substr($text, $startIndex + strlen('упаковка по'));
            preg_match('/\d+/', $substring, $matches);
            if (!empty($matches)) {
                $number = (int) $matches[0];
            }
        }
        return $number;
    }

    /**
     * @param Crawler $item
     * @return array
     */
    public function prepare(Crawler $item): array
    {
        try {
            $name = $item->filter('.h-ip-size__list a.h-ip-size__item.h-active');
        } catch (\Exception $e) {
            return [];
        }

        $head = (new ParseItemPriceOptionsDto)
            ->setWidth(0)
            ->setHeight(0)
            ->setIsPackage(false)
            ->setPackage(0)
        ;

        if (preg_match('/([\d.]+)x([\d.]+)(?:\s+уп-ка по (\d+)\s*шт\.)?/', $name->html(), $matches)) {
            $head->setWidth((int)(floatval($matches[1]) * 100));
            $head->setHeight((int)(floatval($matches[2]) * 100));
            $head->setIsPackage(isset($matches[3]));
            $head->setPackage($head->isPackage() ? (int)$matches[3] : 0);
        }

        $items = [];

        $item->filter('.h-ip-table tbody tr')->each(function ($tr, $i) use (&$items, $head) {
            $tds = $tr->filter('td');
            if ($tds->count() >= 3) {
                $storageName = $tds->eq(0)->text();
                $count = (int) $tds->eq(1)->text();
                $priceText = $tds->eq(2)->text();

                if ($head->getWidth() < 1 || $head->getHeight() < 1) {
                    return;
                }

                $items[] = (new ParseItemPriceDto())
                    ->setWidth($head->getWidth())
                    ->setHeight($head->getHeight())
                    ->setCount($count)
                    ->setPrice((int) str_replace(['₽', ' ', 'Руб', 'RU', 'Рублей', 'руб.', 'р.', 'руб', 'р'], '', $priceText))
                    ->setStorage($this->getStorage($storageName))
                    ->setIsPackage($head->isPackage())
                    ->setPackage($head->getPackage())
                ;
            }
        });

        return $items;
    }

    /**
     * @param string $storage
     * @return int
     */
    private function getStorage(string $storage): int
    {
        $id = Constant::STORAGE_0;
        foreach (Constant::STORAGES as $key => $val) {
            // Точный поиск склада
            if ($val === $storage) {
                $id = (int) $key;
                break;
            }
            // Поиск склада like
            if (\stripos($storage, $storage)) {
                $id = (int) $key;
                break;
            }
        }

        return $id;
    }

    /**
     * @param string $article
     * @param int $width
     * @param int $height
     * @return string
     */
    private function generateMid(string $article, int $width, int $height): string
    {
        return \md5($article . $width . $height . Constant::PRICES_CM);
    }

    /**
     * @param array $item
     * @param string $uuid
     */
    public function formalize(array $item, string $uuid = '')
    {
        $pricePackageList = [];
        $uniqueCombinations = [];

        foreach ($item as $parseItem) {
            /**
             * @var $parseItem ParseItemPriceDto
             */
            if ($parseItem->isPackage()) {
                $combinationKey = $parseItem->getWidth() . '-' . $parseItem->getHeight();
                if (!isset($uniqueCombinations[$combinationKey])) {
                    $pricePackageList[] = (new PriceValidatorSizePrepareDto())
                        ->setW($parseItem->getWidth())
                        ->setH($parseItem->getHeight())
                        ->setCount($parseItem->getPackage())
                    ;
                    $uniqueCombinations[$combinationKey] = true;
                }
            }
        }

        unset($uniqueCombinations);

        foreach ($pricePackageList as $val) {
            $this->queue->writeQueue(
                $this->serializer->serialize((new SenderMarketExportDto())
                    ->setName('auto')
                    ->setMid($this->generateMid($uuid, $val->getW(), $val->getH()))
                    ->setCounterPkg($val->getCount()),
                    'json'
                ), self::PACKAGE_QUEUE_NAME
            );
        }

        foreach ($item as $parseItem) {
            $this->master->setPriceOne(
                (new Price())
                    ->setUuid(\md5($uuid . $parseItem->getWidth() . $parseItem->getHeight() . Constant::PRICES_CM . $parseItem->getStorage()))
                    ->setMid($this->generateMid($uuid, $parseItem->getWidth(), $parseItem->getHeight()))
                    ->setWidth($parseItem->getWidth())
                    ->setHeight($parseItem->getHeight())
                    ->setPrice(round($parseItem->getPrice() * $this->factor))
                    ->setCount($parseItem->getCount())
                    ->setOldPrice(round($parseItem->getPrice()))
                    ->setStorage($parseItem->getStorage())
                    ->setMeter(Constant::PRICES_CM)
            );
        }
    }

    public function export(): MasterEntity
    {
        return $this->master;
    }

}
