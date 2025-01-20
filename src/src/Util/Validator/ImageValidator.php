<?php

namespace App\Util\Validator;

use App\Entity\Images;
use App\Util\Constant;
use App\Util\Dto\ParserDto\ParseItemImgDto;
use App\Util\Dto\ValidatorConfigDto;
use App\Util\Tools\MasterEntity;
use App\Util\ValidatorInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

class ImageValidator implements ValidatorInterface
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var bool
     */
    private $isDownloadImage;

    /**
     * @var int
     */
    private $trys;

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
        $this->dir  = $config->getDir();
        $this->uuid = $config->getUuid();
        $this->isDownloadImage = $config->isDownloadImage();
        $this->trys = 0;
    }

    private function listToParsed(string $class, bool $type, Crawler $item): array
    {
        $list = [];

        $item->filter($class)->each(function ($img, $i) use (&$list, $type) {
            try {
                $image = $img->attr('src');
            } catch (\Exception $e) {
                return;
            }

            if ($image && $image != "") {
                $src = preg_replace('/\?.*/', '', $image);
                $found = false;

                foreach ($list as $item) {
                    if ($item instanceof ParseItemImgDto && $item->getSrc() === $src && $item->isType() === $type) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $list[] = (new ParseItemImgDto())
                        ->setType($type)
                        ->setSrc($src);
                }
            }
        });

        return $list;
    }

    /**
     * @param Crawler $item
     * @return array
     */
    public function prepare(Crawler $item): array
    {
        return array_merge(
            $this->listToParsed('.h-ip__images-list .h-ip-image-item img', false, $item),
            $this->listToParsed('.h-ip-imageBody .swiper-slide a img', true, $item)
        );
    }

    /**
     * @param string $url
     * @param string $name
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fileDownload(string $url, string $name): ?string
    {
        $format = \substr($url, \strripos($url, '.'));
        $path = $this->dir . Constant::FILE_DIR . $name . $format;
        if (!file_exists($path)) {
            $resource = \fopen($path, 'w');
            $client = new Client();
            $client->request(Constant::GET, $url, ['sink' => $resource]);
        }
        return \file_exists($path) ? $name . $format : null;
    }

    /**
     * @param ParseItemImgDto $value
     * @return bool
     * @throws GuzzleException
     */
    private function arrayConverter(ParseItemImgDto $value): bool
    {
        $getDownload = true;
        if ($this->isDownloadImage) {
            $getDownload = $this->fileDownload($value->getSrc(), \md5($value->getSrc() . $this->uuid));
        }

        if (null === $getDownload) {
            if (Constant::IMAGE_TRY_DOWNLOAD >= $this->trys) {
                $this->trys++;
                return $this->arrayConverter($value);
            }
            return false;
        }

        $this->master->setImageOne(
            (new Images())
                ->setFilename($getDownload)
                ->setDir(Constant::FILE_DIR)
                ->setType($value->isType())
                ->setOriginalUrl($value->getSrc())
        );
        return true;
    }

    /**
     * @param array $item
     */
    public function formalize(array $item)
    {
        foreach ($item as $value) {
            if(!$value instanceof ParseItemImgDto) {
                continue;
            }
            try {
                $this->arrayConverter($value);
            } catch (GuzzleException $e) {
                continue;
            }
        }
    }

    public function export(): MasterEntity
    {
        return $this->master;
    }
}
