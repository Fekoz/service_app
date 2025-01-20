<?php


namespace App\Util;

use App\Service\Bot;

interface HelperMarketMapGenInterface
{
    public function __construct();

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
    );

    public function generate();

    public function clear();

}
