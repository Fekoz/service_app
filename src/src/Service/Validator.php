<?php

namespace App\Service;

use App\Util\Constant;
use App\Util\Dto\ParserDto\ParseItemDto;
use App\Util\Dto\WriteParamDto;
use App\Util\Tools\MasterEntity;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Persist;
use App\Service\Helper;
use DateTime;

class Validator
{
    /**
     * @var object
     */
    private $param;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var Persist
     */
    private $persist;

    /**
     * @var float
     */
    private $defaultFactor;

    /**
     * @param ParameterBagInterface $param
     * @param Helper $helper
     * @param Persist $persist
     */
    public function __construct(ParameterBagInterface $param, Helper $helper, Persist $persist)
    {
        $this->helper = $helper;
        $this->param = $this->helper->convertObject($param->get(Constant::CONFIG_NAME[__CLASS__]));
        $this->persist = $persist;
        $this->defaultFactor = $this->helper->getParam()->getFactor() !== null ? $this->helper->getParam()->getFactor() : $this->param->default_factor;
    }

    private function venera(ParseItemDto $item): MasterEntity
    {
        $master = new MasterEntity();
        $master = $this->helper->specificationValidate($master, $item->getSpec());
        $master = $this->helper->imageValidate($master, $item->getImg(), $this->param->kernel_dir, $item->getOption()->getUuid(), $this->param->is_download_image);
        $master = $this->helper->priceValidate($master, $item->getPrice(), $this->defaultFactor, $item->getOption()->getUuid());
        $master = $this->helper->productValidate($master, $item->getOption()->getPrice(), $this->defaultFactor, $item->getOption()->getUuid(), $item->getOption()->getUrl());
        unset($item);
        return $master;
    }

    public function setDefaultFactor(float $factor)
    {
        $this->defaultFactor = $factor;
    }

    /**
     * @param ParseItemDto $item
     * @param DateTime $date
     */
    public function veneraImport(ParseItemDto $item, \DateTime $date)
    {
        $this->persist->clear();
        $me = $this->venera($item);
        $this->persist->write($me, $date);
        unset($me);
    }

    /**
     * @param ParseItemDto $item
     * @return MasterEntity
     */
    public function veneraView(ParseItemDto $item): MasterEntity
    {
        return $this->venera($item);
    }

}
