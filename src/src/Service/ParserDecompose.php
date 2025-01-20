<?php

namespace App\Service;

use App\Entity\Auth;
use App\Repository\AuthRepository;
use App\Util\Constant;
use App\Util\Tools\Config\ConfigParserModel;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ParserDecompose
{
    /**
     * @var EntityManagerInterface
     */
    public $entity;

    /**
     * @var array
     */
    private $param;

    /**
     * @var \DateTime
     */
    private $dateTime;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param)
    {
        $this->entity = $entity;
        $this->param = $param->get(Constant::CONFIG_NAME[__CLASS__]);
    }

    /**
     * @param string $name
     * @param \DateTime $date
     * @return ConfigParserModel
     */
    public function init(string $name, \DateTime $date): ?ConfigParserModel
    {
        if (!isset($this->param['auth'][$name])) {
            return null;
        }

        $configAuth = (object) $this->param['auth'][$name];
        $configPage = (object) $this->param['page'][$name];
        $configFake = (object) $this->param['fake'][$name];

        $this->dateTime = $date;

        try {
            return (new ConfigParserModel())
                ->setPassword($configAuth->password)
                ->setName($configPage->name)
                ->setUrl($configPage->url)
                ->setCount($configAuth->count)
                ->setEmail($configAuth->email)
                ->setAuthPage($configPage->auth)
                ->setCatalogPage($configPage->catalog)
                ->setErrorMessage($configPage->error)
                ->setFakeKey($configFake->key)
                ->setFakeName($configFake->name)
                ->setFakeValue($configFake->value)
                ->setFilterPage($configPage->filter)
                ->setIsFake($configPage->fake)
                ->setLimit($configPage->limit)
                ->setWarehouseCategories($configPage->warehouseCategories)
            ;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param CookieJar $cookieJar
     */
    public function context(CookieJar $cookieJar)
    {
        $array = $cookieJar->toArray();
        foreach ($array as $value) {
            if (!$value['Name'] || !$value['Value'] || !$value['Domain']) {
                continue;
            }

            $session = new Auth(
                $value['Domain'],
                $value['Value'],
                $value['Name'],
                $this->dateTime,
                $this->dateTime
            );
            $this->entity->persist($session);
        }

        $this->entity->flush();
    }

    /**
     * @param string $name
     * @return Auth|null
     */
    public function stream(string $name): ?Auth
    {
        /**
         * @var $em AuthRepository
         */
        $em = $this->entity->getRepository(Auth::class);
        return $em->getLastSession($name);
    }

}
