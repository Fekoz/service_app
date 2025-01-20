<?php


namespace App\Util\FirstIdentityFunc;


use App\Entity\Field;
use App\Service\Bot;
use App\Util\FirstIdentityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CreateFieldFunctionFirst implements FirstIdentityInterface
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
        if (count($entity->getRepository(Field::class)->findAll()) === 0) {
            $output->writeln('Создание Отчета выгрузки на маркет и авито');
            $field = new Field();
            $field->setType("full");
            $field->setParam(3000);
            $field->setMax(15000);
            $field->setMade(false);
            $field->setName("app:marketImport");
            $field->setDateUpdate(new \DateTime());
            $field->setDateCreate(new \DateTime());
            $entity->persist($field);
            $entity->flush();
            $entity->clear();
        }
    }
}
