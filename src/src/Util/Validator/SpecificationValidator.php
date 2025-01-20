<?php

namespace App\Util\Validator;

use App\Entity\Specification;
use App\Util\Constant;
use App\Util\Dto\ParserDto\ParseItemSpecDto;
use App\Util\Dto\ValidatorConfigDto;
use App\Util\Tools\MasterEntity;
use App\Util\Validator\Util\UtilSpecificationValidator;
use App\Util\ValidatorInterface;
use Symfony\Component\DomCrawler\Crawler;

class SpecificationValidator implements ValidatorInterface
{
    /**
     * @var MasterEntity
     */
    private $master;

    public function import(MasterEntity $master)
    {
        $this->master = $master;
    }

    /**
     * @param ValidatorConfigDto $config
     */
    public function setConfig(ValidatorConfigDto $config)
    {

    }

    /**
     * @param Crawler $item
     * @return array
     */
    public function prepare(Crawler $item): array
    {
        return $item->filter('.h-ip-char')->each(function ($title, $i) {
            $key = $title->filter('.h-ip-char__title')->text();
            $value = $title->filter('.h-ip-char__value')->text();
            return (new ParseItemSpecDto())
                ->setName(\trim(\str_replace(['.', '\'', '"', ':', "\n", "\t", "\r"], '', $key)))
                ->setValue(\trim(\str_replace(['\'', '"', ':', "\n", "\t", "\r"], '', $value)))
                ;
        }) ?: [];
    }

    /**
     * @param array $item
     */
    public function formalize(array $item)
    {
        foreach ($item as $specItem) {
            if(!$specItem || !$specItem instanceof ParseItemSpecDto) {
                continue;
            }
            $itemWrite = false;

            foreach (Constant::SPECIFICATIONS as $specConst) {
                if ($specConst[Constant::SPEC_PARSE_SYM] === $specItem->getName()) {
                    foreach (Constant::RENAME_SPECIFICATIONS as $value) {
                        if($value["name"] === $specItem->getValue()) {
                            $specItem->setValue($value["value"]);
                        }
                    }

                    $this->master->setSpecificationOne(
                        (new Specification())
                            ->setName($specConst[Constant::SPEC_VALUE])
                            ->setValue(\ucfirst($specItem->getValue()))
                    );

                    $itemWrite = true;
                }
            }

            if (false === $itemWrite) {
                foreach (Constant::RENAME_SPECIFICATIONS as $value) {
                    if($value["name"] === $specItem->getValue()) {
                        $specItem->setValue($value["value"]);
                    }
                }

                $this->master->setSpecificationOne(
                    (new Specification())
                        ->setName(Constant::SPECIFICATIONS[Constant::SPEC_OTHER][Constant::SPEC_VALUE])
                        ->setValue(\ucfirst($specItem->getValue()))
                );
            }

        }

        /**
         * @description Добавляет дополнительные типы для спецификаций
         */
        $this->master = UtilSpecificationValidator::append($this->master);
    }

    public function export(): MasterEntity
    {
        return $this->master;
    }
}
