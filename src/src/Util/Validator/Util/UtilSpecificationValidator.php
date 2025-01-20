<?php


namespace App\Util\Validator\Util;


use App\Entity\Specification;
use App\Util\Constant;
use App\Util\Tools\MasterEntity;

class UtilSpecificationValidator
{
    public static function append(MasterEntity $me): MasterEntity
    {
        /**
         * Тип ворса ковра:
         * true - С ворсом
         * false - Без ворса
         */
        $isPileType = false;

        /**
         * Тип ковра:
         * true - Ковровая дорожка
         * false - Ковер
         */
        $isCarpetType = false;

        /**
         * Тип изготавления:
         * true - Ручной
         * false - Машинный
         */
        $isPrepareType = false;

        $collectionName = '';

        foreach ($me->getSpecification() as $value) {
            if ($value instanceof Specification) {
                if ($value->getName() === Constant::SPECIFICATIONS[Constant::SPEC_PILE][Constant::SPEC_VALUE]) {
                    $isPileType = true;
                }

                if ($value->getName() === Constant::SPECIFICATIONS[Constant::SPEC_FORM][Constant::SPEC_VALUE] && $value->getValue() === 'Дорожка') {
                    $isCarpetType = true;
                }

                if ($value->getName() === Constant::SPECIFICATIONS[Constant::SPEC_PREPARATION][Constant::SPEC_VALUE] && $value->getValue() === 'Ручная работа') {
                    $isPrepareType = true;
                }

                if ($value->getName() === Constant::SPECIFICATIONS[Constant::SPEC_FABRIC][Constant::SPEC_VALUE]) {
                    $value->setName(Constant::SPECIFICATIONS[Constant::SPEC_VENDOR_NAME][Constant::SPEC_VALUE]);
                }

                if ($value->getName() === Constant::SPECIFICATIONS[Constant::SPEC_COLLECTION][Constant::SPEC_VALUE]) {
                    $collectionName = $value->getValue();
                    $value->setName(Constant::SPECIFICATIONS[Constant::SPEC_VENDOR_COLLECTION][Constant::SPEC_VALUE]);
                }
            }
        }

        $me->setSpecificationOne(
            (new Specification())
                ->setName(Constant::SPECIFICATIONS[Constant::SPEC_PILE_TYPE][Constant::SPEC_VALUE])
                ->setValue(true === $isPileType
                    ? Constant::CARPET_SPEC_DYNAMIC_PARAM_TYPE_PILE_TRUE
                    : Constant::CARPET_SPEC_DYNAMIC_PARAM_TYPE_PILE_FALSE)
        );

        $me->setSpecificationOne(
            (new Specification())
                ->setName(Constant::SPECIFICATIONS[Constant::SPEC_CARPET_TYPE][Constant::SPEC_VALUE])
                ->setValue(true === $isCarpetType
                    ? Constant::CARPET_SPEC_DYNAMIC_PARAM_TYPE_CARPET_TRUE
                    : Constant::CARPET_SPEC_DYNAMIC_PARAM_TYPE_CARPET_FALSE)
        );

        $me->setSpecificationOne(
            (new Specification())
                ->setName(Constant::SPECIFICATIONS[Constant::SPEC_PREPARE_TYPE][Constant::SPEC_VALUE])
                ->setValue(true === $isPrepareType
                    ? Constant::CARPET_SPEC_DYNAMIC_PARAM_TYPE_PREPARE_TRUE
                    : Constant::CARPET_SPEC_DYNAMIC_PARAM_TYPE_PREPARE_FALSE)
        );

        $me->setSpecificationOne(
            (new Specification())
                ->setName(Constant::SPECIFICATIONS[Constant::SPEC_FABRIC][Constant::SPEC_VALUE])
                ->setValue(Constant::CARPETTI_FABRIC)
        );

        $hash = sha1($collectionName);
        $newName = substr($hash, 0, 5);

        $me->setSpecificationOne(
            (new Specification())
                ->setName(Constant::SPECIFICATIONS[Constant::SPEC_COLLECTION][Constant::SPEC_VALUE])
                ->setValue(
                    \strtoupper(\strtr($newName, Constant::ARTICLE_FORMATTER))
                )
        );

        return $me;
    }
}
