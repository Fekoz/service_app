<?php

namespace App\Util;

use App\Util\Dto\ValidatorConfigDto;
use App\Util\Tools\MasterEntity;
use Symfony\Component\DomCrawler\Crawler;

interface ValidatorInterface
{
    public function import(MasterEntity $master);

    public function setConfig(ValidatorConfigDto $config);

    public function prepare(Crawler $item);

    public function formalize(array $item);

    public function export(): MasterEntity;
}
