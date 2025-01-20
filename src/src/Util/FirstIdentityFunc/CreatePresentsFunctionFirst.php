<?php


namespace App\Util\FirstIdentityFunc;


use App\Entity\Presents;
use App\Service\Bot;
use App\Util\FirstIdentityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CreatePresentsFunctionFirst implements FirstIdentityInterface
{
    const PRESENTS = [
        ["key" => "SALE10", "value" => "10", "name" => "Скидка на товар 10%"],
        ["key" => "SALE20", "value" => "20", "name" => "Скидка на товар 20%"],
        ["key" => "SALE30", "value" => "30", "name" => "Скидка на товар 30%"],
        ["key" => "SALE40", "value" => "40", "name" => "Скидка на товар 40%"],
        ["key" => "CERT10", "value" => "10", "name" => "Сертификат на 10% скидку"],
        ["key" => "CERT20", "value" => "20", "name" => "Сертификат на 20% скидку"],
        ["key" => "CERT30", "value" => "30", "name" => "Сертификат на 30% скидку"],
        ["key" => "CERT40", "value" => "40", "name" => "Сертификат на 40% скидку"],
        ["key" => "NONE", "value" => "0", "name" => "Без скидки"]
    ];

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
        if (count($entity->getRepository(Presents::class)->findAll()) === 0) {
            $output->writeln('Создание Акций');
            foreach (self::PRESENTS as $value) {
                $presents = new Presents();
                $presents->setKey($value["key"]);
                $presents->setName($value["name"]);
                $presents->setValue($value["value"]);
                $presents->setDateCreate(new \DateTime());
                $presents->setDateUpdate(new \DateTime());
                $entity->persist($presents);
                $entity->flush();
                $entity->clear();
            }
        }
    }
}
