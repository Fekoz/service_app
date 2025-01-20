<?php


namespace App\Util\FirstIdentityFunc;


use App\Entity\Admin;
use App\Service\Bot;
use App\Util\FirstIdentityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CreateAdminFunctionFirst implements FirstIdentityInterface
{
    public static function run(
        OutputInterface $output,
        EntityManagerInterface $entity,
        object $param,
        object $paramParse,
        object $parserAuth,
        SerializerInterface $serializer,
        Bot $bot
    ):void
    {
        if (count($entity->getRepository(Admin::class)->findAll()) === 0) {
            $output->writeln('Создание Админа');
            $admin = new Admin(
                "carpetti_admin",
                "sBD3nPD3",
                "admin@carpetti.vip",
                "0000",
                1,
                true
            );
            $admin->setName("carpetti_admin");
            $admin->setDateUpdate(new \DateTime());
            $admin->setDateCreate(new \DateTime());
            $entity->persist($admin);
            $entity->flush();
            $entity->clear();
        }
    }

}
