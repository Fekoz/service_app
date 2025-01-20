<?php


namespace App\Util;


use App\Service\Bot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

interface FirstIdentityInterface
{
    public static function run(
        OutputInterface $output,
        EntityManagerInterface $entity,
        object $param,
        object $paramParse,
        object $parserAuth,
        SerializerInterface $serializer,
        Bot $bot
    ): void;
}
