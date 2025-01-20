<?php

namespace App\Service;

use App\Entity\Images;
use App\Util\Constant;
use App\Util\SystemServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GarbageProduct implements SystemServiceInterface
{
    /**
     * @var object
     */
    private $param;

    /**
     * @var EntityManagerInterface
     */
    private $entity;

    /**
     * @var Bot
     */
    private $bot;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Bot $bot)
    {
        $this->entity = $entity;
        $this->bot = $bot;
        $this->bot->init(Constant::BOT_TELEGRAM);
        $this->param = (object) $param->get(Constant::CONFIG_NAME[__CLASS__]);
        $this->dropCount = 0;
    }

    private function scanDir(array $array): array
    {
        $dir = [];
        foreach ($array as $value) {
            if ($value !== '.' && $value !== '..') {
                $dir[$value] = false;
            }
        }
        return $dir;
    }

    private function scanList(array $array, array $dir): array
    {
        foreach ($array as $value) {
            /**
             * @var $value Images
             */
            if ($value->getFilename()) {
                $dir[$value->getFilename()] = true;
            }
        }
        return $dir;
    }

    public function import(array $list = [])
    {
        return;
    }

    public function run()
    {
        $dropXlsxCount = 0;
        if (is_dir($this->param->kernel_dir . Constant::EXPORT_DIR . Constant::XLSX_DIR)) {
            $dropXlsxList = $this->scanDir(\scandir($this->param->kernel_dir . Constant::EXPORT_DIR . Constant::XLSX_DIR));
            foreach ($dropXlsxList as $key => $value) {
                if (false === $value) {
                    \unlink($this->param->kernel_dir . Constant::EXPORT_DIR . Constant::XLSX_DIR . $key);
                    $dropXlsxCount++;
                }
            }
        }

        $dropCount = 0;
        $list = $this->entity->getRepository(Images::class)->findAllImages();
        $fileList = $this->scanDir(\scandir($this->param->kernel_dir . Constant::FILE_DIR));
        $dropList = $this->scanList($list, $fileList);
        foreach ($dropList as $key => $value) {
            if (false === $value) {
                \unlink($this->param->kernel_dir . Constant::FILE_DIR . $key);
                $dropCount++;
            }
        }
        file_put_contents($this->param->kernel_dir . Constant::LOG_FILE, null);
        $this->bot->message(null, \sprintf('Закончил чистку файлов. Удалено Изображений:[%d], Файлов:[%d] неиспользуемых файлов.', $dropCount, $dropXlsxCount));
    }
}
