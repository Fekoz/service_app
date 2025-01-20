<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Util\Constant;
use App\Util\Tools\MasterEntity;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Explode\PersistExplode;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Persist extends PersistExplode
{
    /**
     * @var array
     */
    private $param;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var LogService
     */
    private $log;

    /**
     * Persist constructor.
     * @param EntityManagerInterface $entity
     * @param LogService $log
     * @param ContainerInterface $container
     * @param ParameterBagInterface $param
     */
    public function __construct(EntityManagerInterface $entity, LogService $log, ContainerInterface $container, ParameterBagInterface $param)
    {
        $this->entity = $entity;
        $this->log = $log;
        $this->container = $container;
        $param = $param->get(Constant::CONFIG_NAME[Constant::_DEFAULT]);
        $this->param = $param;
        parent::__construct();
    }

    public function getUpdateItem(\DateTime $time, \DateInterval $away): ?Product
    {
        $time->sub($away);
        /**
         * @var $repo ProductRepository
         */
        $repo = $this->entity->getRepository(Product::class);
        return $repo->findByUpdate($time);
    }

    public function touch(Product $item, \DateTime $date)
    {
        $item->setActive(false);
        $item->setDateUpdate($date);
        $this->entity->persist($item);
        $this->entity->flush();
    }

    public function write(MasterEntity $item, \DateTime $date): bool
    {
        if ($item->getProduct()->getArticle() === "" || $item->getProduct()->getName() === "") {
            return false;
        }
        //@todo to log
        //dump($item->getProduct()->getOriginalUrl());
        $this->product = $item->getProduct();
        $this->specification = $item->getSpecification();
        $this->price = $item->getPrice();
        $this->image = $item->getImage();

        $this->product->setActive(count($item->getPrice()) > 0);

        /**
         * @var $repo ProductRepository
         */
        $repo = $this->entity->getRepository(Product::class);
        $currentItem = $repo->findByUuid($item->getProduct()->getUuid());

        $this->dateNow = $date;
        $this->directory = $this->param['kernel_dir'];

        try {
            $currentItem
                ? $this->update($currentItem)
                : $this->create()
            ;
        } catch (\Exception $e) {
            $this->log->registerException(Logger::CRITICAL, Constant::LOG_DB_EM_ERROR, $e, ['url' => $item->getProduct()->getOriginalUrl(), 'uuid' => $item->getProduct()->getUuid()]);
            if (!$this->entity->isOpen()) {
                $this->container->get('doctrine')->resetManager();
                $this->entity = $this->container->get('doctrine.orm.default_entity_manager');
            }
            return false;
        }

        return true;
    }

    public function clear()
    {
        $this->entity->clear();
    }

}
