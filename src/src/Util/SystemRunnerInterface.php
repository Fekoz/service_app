<?php


namespace App\Util;


use App\Service\Bot;
use App\Service\LogService;
use App\Service\Queue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

interface SystemRunnerInterface
{
    public function run(string $article);

    public function process(callable $fnc);
}
