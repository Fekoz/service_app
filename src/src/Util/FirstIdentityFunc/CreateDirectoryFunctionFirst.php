<?php


namespace App\Util\FirstIdentityFunc;


use App\Entity\DirectoryLog;
use App\Entity\DirectoryMarketplaceFormatImport;
use App\Entity\DirectoryMeter;
use App\Entity\DirectorySpecification;
use App\Entity\DirectoryStorage;
use App\Service\Bot;
use App\Util\Constant;
use App\Util\FirstIdentityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CreateDirectoryFunctionFirst implements FirstIdentityInterface
{
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
        if (count($entity->getRepository(DirectoryLog::class)->findAll()) === 0) {
            $output->writeln('Создание справочника Логов');
            foreach (Constant::LOG as $key => $val) {
                $directory = new DirectoryLog();
                $value = \is_array($val) ? $val['parse'] : $val;
                if (!$value) {
                    $value = 'Не указано';
                }
                $directory->setName($value);
                $entity->persist($directory);
                $entity->flush();
                $entity->clear();
            }
        }

        if (count($entity->getRepository(DirectoryMeter::class)->findAll()) === 0) {
            $output->writeln('Создание справочника Метража');
            foreach (Constant::METRES as $key => $val) {
                $directory = new DirectoryMeter();
                $value = \is_array($val) ? $val['parse'] : $val;
                if (!$value) {
                    $value = 'Не указано';
                }
                $directory->setName($value);
                $entity->persist($directory);
                $entity->flush();
                $entity->clear();
            }
        }

        if (count($entity->getRepository(DirectoryStorage::class)->findAll()) === 0) {
            $output->writeln('Создание справочника Складов');
            foreach (Constant::STORAGES as $key => $val) {
                $directory = new DirectoryStorage();
                $value = \is_array($val) ? $val['parse'] : $val;
                if (!$value) {
                    $value = 'Не указано';
                }
                $directory->setName($value);
                $entity->persist($directory);
                $entity->flush();
                $entity->clear();
            }
        }

        if (count($entity->getRepository(DirectorySpecification::class)->findAll()) === 0) {
            $output->writeln('Создание справочника Спецификаций');
            foreach (Constant::SPECIFICATIONS as $key => $val) {
                $directory = new DirectorySpecification();
                $value = \is_array($val) ? $val['parse'] : $val;
                if (!$value) {
                    $value = 'Не указано';
                }
                $directory->setName($value);
                $entity->persist($directory);
                $entity->flush();
                $entity->clear();
            }
        }

        if (count($entity->getRepository(DirectoryMarketplaceFormatImport::class)->findAll()) === 0) {
            $output->writeln('Создание справочника Маркетплейс-Импортов');
            foreach (Constant::MARKETPLACE_FORMAT as $key => $val) {
                $directory = new DirectoryMarketplaceFormatImport();
                $directory->setId($key);
                $directory->setName($val[0]);
                $directory->setPath($val[1]);
                $entity->persist($directory);
                $entity->flush();
                $entity->clear();
            }
        }
    }

}
