<?php


namespace App\Util\FirstIdentityFunc;


use App\Entity\DirectoryMarketplaceFormatImport;
use App\Entity\DirectoryMarketplaceMappingConst;
use App\Entity\MarketMapping;
use App\Entity\MarketMappingProperty;
use App\Service\Bot;
use App\Util\Constant;
use App\Util\Dto\MarketPlaceOutputDirectoryOzonDto;
use App\Util\FirstIdentityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

class CreateMarketMappingFunctionFirst implements FirstIdentityInterface
{
    // CreateMarketMappingFunctionFirst
    private static function constUpload(string $nameless, array $arr, EntityManagerInterface $entity, OutputInterface $output)
    {
        foreach ($arr as $val) {
            if (!$val instanceof DirectoryMarketplaceMappingConst) {
                continue;
            }
            /**
             * @var $mm MarketMapping
             */
            $mm = $entity->getRepository(MarketMapping::class)->findOneBy(['params' => $val->getIdMap()]);
            if(!$mm) {
                $output->writeln('Ошибка - Запись [id: '. $val->getIdMap() .' ] не найдена.');
                continue;
            }

            if (!$mm->getName()) {
                $mm->setName($val->getValue());
            } else {
                $newName = $mm->getName() . ',' . $val->getValue();
                $mm->setName($newName);
            }

            $mm->setKey($nameless);
            $date = new \DateTime();
            $mm->setDateUpdate($date);
            $entity->persist($mm);
        }
    }

    public static function run(
        OutputInterface $output,
        EntityManagerInterface $entity,
        object $param,
        object $paramParse,
        object $parserAuth,
        SerializerInterface $serializer,
        Bot $bot
    ): void
    {
        $directory = $entity->getRepository(DirectoryMarketplaceFormatImport::class)->findAll();

        /**
         * @var $path DirectoryMarketplaceFormatImport
         */
        foreach ($directory as $path) {
            $content = file_get_contents($path->getPath());
            if ($content === false || !$content) {
                $output->writeln("Ошибка чтения json файла " . $path->getPath());
                continue;
            }

            try {
                /**
                 * @var MarketPlaceOutputDirectoryOzonDto $map
                 */
                $map = $serializer->deserialize($content, MarketPlaceOutputDirectoryOzonDto::class, 'json');
                if (!$map->getResult()) {
                    continue;
                }

                foreach ($map->getResult() as $val) {
                    $val = (object) $val;
                    /**
                     * @var $mp MarketMappingProperty
                     */
                    $mp = $entity->getRepository(MarketMappingProperty::class)->find($val->id);
                    if (!$mp) {
                        $mp = new MarketMappingProperty();
                        $mp->setId($val->id);
                        $mp->setValue($val->value);
                        $mp->setInfo($val->info);
                        $mp->setPicture($val->picture);
                        $entity->persist($mp);
                    }

                    /**
                     * @var $mm MarketMapping
                     */
                    $mm = $entity->getRepository(MarketMapping::class)->findBy(['params' => $mp->getId()]);
                    if (!$mm) {
                        $date = new \DateTime();
                        $mm = new MarketMapping();
                        $mm->setParams($mp);
                        $mm->setDateUpdate($date);
                        $mm->setDateCreate($date);
                        $mm->setType($path);
                        $mm->setName('');
                        $mm->setKey('');
                        $entity->persist($mm);
                    }

                    $entity->flush();
                }
            } catch (\Exception $e) {
                $output->writeln('Ошибка: ' . $e->getMessage());
                continue;
            }
        }

        // constUpload
        self::constUpload(Constant::MAPPING_NAME_WARP, $entity->getRepository(DirectoryMarketplaceMappingConst::class)->findBy(['type' => Constant::MAPPING_NAME_WARP]), $entity, $output);
        self::constUpload(Constant::MAPPING_NAME_STYLE, $entity->getRepository(DirectoryMarketplaceMappingConst::class)->findBy(['type' => Constant::MAPPING_NAME_STYLE]), $entity, $output);
        self::constUpload(Constant::MAPPING_NAME_COUNTRY, $entity->getRepository(DirectoryMarketplaceMappingConst::class)->findBy(['type' => Constant::MAPPING_NAME_COUNTRY]), $entity, $output);
        self::constUpload(Constant::MAPPING_NAME_CARPET_TYPE, $entity->getRepository(DirectoryMarketplaceMappingConst::class)->findBy(['type' => Constant::MAPPING_NAME_CARPET_TYPE]), $entity, $output);
        self::constUpload(Constant::MAPPING_NAME_WARP, $entity->getRepository(DirectoryMarketplaceMappingConst::class)->findBy(['type' => Constant::MAPPING_NAME_WARP2]), $entity, $output);
        self::constUpload(Constant::MAPPING_NAME_FABRIC, $entity->getRepository(DirectoryMarketplaceMappingConst::class)->findBy(['type' => Constant::MAPPING_NAME_FABRIC]), $entity, $output);
        self::constUpload(Constant::MAPPING_NAME_FORM, $entity->getRepository(DirectoryMarketplaceMappingConst::class)->findBy(['type' => Constant::MAPPING_NAME_FORM]), $entity, $output);
        self::constUpload(Constant::MAPPING_NAME_COLOUR, $entity->getRepository(DirectoryMarketplaceMappingConst::class)->findBy(['type' => Constant::MAPPING_NAME_COLOUR]), $entity, $output);
        self::constUpload(Constant::MAPPING_NAME_BRAND, $entity->getRepository(DirectoryMarketplaceMappingConst::class)->findBy(['type' => Constant::MAPPING_NAME_BRAND]), $entity, $output);

        $entity->flush();
    }
}
