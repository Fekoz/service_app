<?php


namespace App\Command;


use App\Entity\Product;
use App\Service\Cache;
use App\Service\Helper;
use App\Service\MarketPlace;
use App\Service\Queue;
use App\Util\Constant;
use App\Util\Dto\CacheDto;
use App\Util\Dto\MarketPlaceDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Wrep\Daemonizable\Command\EndlessContainerAwareCommand;

class RuntimeSendMarketPlace extends EndlessContainerAwareCommand
{
    const FILE_WRITE = '/srv/app/s.list';

    /**
     * @var EntityManagerInterface
     */
    private $entity;

    /**
     * @var object
     */
    private $param;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var MarketPlace
     */
    private $mp;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var int
     */
    private $point;

    /**
     * @var ?\Generator
     */
    private $list;

    /**
     * @var int
     */
    private $timeout;

    public function __construct(
        EntityManagerInterface $entity,
        ParameterBagInterface $param,
        Queue $queue,
        ContainerInterface $container,
        Helper $helper,
        SerializerInterface $serializer,
        MarketPlace $mp,
        Cache $cache
    ) {
        $this->entity = $entity;
        $this->param = $param;
        $this->queue = $queue;
        $this->container = $container;
        $this->helper = $helper;
        $this->serializer = $serializer;
        $this->mp = $mp;
        $this->cache = $cache;
        $this->param = $this->helper->convertObject($param->get(Constant::CONFIG_NAME[__CLASS__]));
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, ['SKIP_NULL_VALUES' => true])];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->list = null;
        $this->timeout = $this->param->iterate;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:run.send.mp')
            ->setDescription('runtime send to marketplace start');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->queue->init($this->param->name);

        if (!$this->entity->getConnection()) {
            $this->setReturnCode(Constant::RUNTIME_RETURN_CODE);
            $this->shutdown();
        }

        $pointInModel = $this->cache->get($this->param->name);

        if ($pointInModel instanceof CacheDto && $pointInModel->getType() === Constant::DECODE_CACHE_TYPE_INT) {
            $this->point = $pointInModel->getResult();
        } else {
            $this->point = $this->nullPoint();
        }

    }

    protected function startIteration(InputInterface $input, OutputInterface $output): void
    {
        if (!$this->list) {
            $this->injector();
        }

        if (!$this->entity->isOpen()) {
            $this->container->get('doctrine')->resetManager();
            $this->entity = $this->container->get('doctrine.orm.default_entity_manager');
        }

        parent::startIteration($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        gc_collect_cycles();
        gc_mem_caches();

        if ($this->list) {
            $this->iterate();
        }

        $this->mp->process(function ($a) {
            if ($a instanceof MarketPlaceDto) {
                //$this->exportToFile($this->encodeObject($a));
                $this->exportToQueue($this->encodeObject($a));
            }
        });

        $this->throwExceptionOnShutdown();
    }

    protected function finishIteration(InputInterface $input, OutputInterface $output): void
    {
        $this->entity->clear();
        $this->setTimeout($this->timeout);
        parent::finishIteration($input, $output);
    }

    protected function finalize(InputInterface $input, OutputInterface $output): void
    {
        parent::finalize($input, $output);
    }

    private function injector()
    {
        gc_collect_cycles();
        gc_mem_caches();

        if (memory_get_usage() >= $this->param->limit) {
            $this->shutdown();
            return;
        }

        $this->list = $this->entity->getRepository(Product::class)->getPointerItemList(
            rand($this->param->min, $this->param->max),
            $this->point
        );

        $this->timeout = $this->param->iterate;
        $max = $this->entity->getRepository(Product::class)->getMaxId();

        if ($this->point >= $max) {
            $this->point = $this->nullPoint();
            $this->timeout = $this->param->sleep;
        }

        $this->list->rewind();
    }

    private function iterate(): void
    {
        try {
            $data = $this->list->current();
        } catch (\InvalidArgumentException | \Exception $e) {
            $this->list = null;
            return;
        }

        if (!$data || !$data instanceof Product) {
            $this->list = null;
            return;
        }

        $this->mp->run($data->getArticle());
        $this->point++;
        $this->cache->set($this->param->name, $this->point);
        $this->list->next();
    }

    private function encodeObject(MarketPlaceDto $offer): string
    {
        // Get id echo
        echo $offer->getId() . "\r\n";
        return $this->serializer->serialize($offer, JsonEncoder::FORMAT,  [AbstractObjectNormalizer::SKIP_NULL_VALUES => true, 'json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    private function exportToFile(string $json)
    {
        \file_put_contents(self::FILE_WRITE, "\r\n" . $json, FILE_APPEND);
    }

    private function exportToQueue(string $json)
    {
        $this->queue->write($json);
    }

    private function nullPoint(): int
    {
        $point = 0;
        $this->cache->set($this->param->name, $point);
        return $point;
    }
}
