<?php


namespace App\Util;


use App\Service\Bot;
use App\Service\Validator;
use App\Service\VeneraParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

interface SystemServiceRunCreatorInterface
{
    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Validator $validator, VeneraParser $parser, Bot $bot);

    public function run();
}
