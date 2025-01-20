<?php


namespace App\Util;


use App\Service\Bot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

interface SystemServiceInterface
{
    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Bot $bot);

    public function import(array $list = []);

    public function run();
}
