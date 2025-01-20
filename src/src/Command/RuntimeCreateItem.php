<?php


namespace App\Command;


use App\Entity\Product;
use App\Service\Bot;
use App\Service\Helper;
use App\Service\Queue;
use App\Service\VeneraParser;
use App\Util\Constant;
use App\Util\Dto\ParserDto\ParsePageItemDto;
use App\Util\Dto\SenderCreateItemDto;
use App\Util\Dto\SenderUpdateItemDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Wrep\Daemonizable\Command\EndlessContainerAwareCommand;

class RuntimeCreateItem extends EndlessContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $entity;

    /**
     * @var object
     */
    private $param;

    /**
     * @var Bot
     */
    private $bot;

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
     * @var VeneraParser
     */
    private $parser;

    /**
     * @var float|null
     */
    private $defaultFactor;

    /**
     * @var ?\Generator
     */
    private $list;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Bot $bot, Queue $queue, ContainerInterface $container, Helper $helper, SerializerInterface $serializer, VeneraParser $parser)
    {
        $this->param = $param;
        $this->bot = $bot;
        $this->queue = $queue;
        $this->container = $container;
        $this->bot = $bot;
        $this->helper = $helper;
        $this->serializer = $serializer;
        $this->parser = $parser;
        $this->entity = $entity;
        $this->defaultFactor = 1;
        $this->param = $this->helper->convertObject($param->get(Constant::CONFIG_NAME[__CLASS__]));
        $this->list = null;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:run.crt.item')
            ->setDescription('runtime create item parser start');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->bot->init(Constant::RUNTIME_TELEGRAM_BOT);
        $this->queue->init($this->param->name);

        if (!$this->entity->getConnection()) {
            $this->bot->message(Constant::RUNTIME_GROUP, 'CreateProducer::' . Constant::BOT_SYSTEM_MESSAGES['error']);
            $this->setReturnCode(Constant::RUNTIME_RETURN_CODE);
            $this->shutdown();
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

        $this->throwExceptionOnShutdown();
    }

    protected function finishIteration(InputInterface $input, OutputInterface $output): void
    {
        $this->entity->clear();
        $time = $this->param->iterate;
        if (!$this->list) {
            $time = $this->param->sleep;
        }
        $this->setTimeout($time);
        parent::finishIteration($input, $output);
    }

    protected function finalize(InputInterface $input, OutputInterface $output): void
    {
        parent::finalize($input, $output);
    }

    private function iterate(): void
    {
        try {
            $data = $this->list->current();
        } catch (\InvalidArgumentException | \Exception $e) {
            $this->list = null;
            return;
        }

        if (!$data || !$data instanceof ParsePageItemDto) {
            $this->list = null;
            return;
        }

        /**
         * @var $product Product
         */
        $product = $this->entity->getRepository(Product::class)->findOneBy(['originalUrl' => $data->getUrl()]);
        if (!$product || !$product->getOriginalUrl()) {
            $this->queue->write(
                $this->serializer->serialize(
                    (new SenderCreateItemDto())
                        ->setUid($data->getUuid())
                        ->setUrl($data->getUrl())
                        ->setFactor($this->defaultFactor)
                    ,
                    'json'
                )
            );
        }
        $this->list->next();
    }

    private function injector()
    {
        gc_collect_cycles();
        gc_mem_caches();
        if (memory_get_usage() >= $this->param->limit) {
            $this->shutdown();
            return;
        }

        $this->helper->initParam();
        $this->defaultFactor = $this->helper->getParam()->getFactor() ?: 1;

        if (!$this->parser->auth()) {
            $this->bot->message(Constant::RUNTIME_GROUP, 'CreateProducer::' . Constant::BOT_SYSTEM_MESSAGES['error']);
            $this->shutdown();
            return;
        }

        $parseList = $this->parser->pageParse();
        if (null === $parseList) {
            $this->shutdown();
            return;
        }
        $this->parser->clear();

        if (count($parseList) < 1) {
            $this->list = null;
            return;
        }

        $this->list = $this->generateParsedItems($parseList);
        $this->list->rewind();
    }

    private function generateParsedItems($parseList): \Generator
    {
        foreach ($parseList as $item) {
            if ($item instanceof ParsePageItemDto && $item->getUrl() && $item->getUuid()) {
                yield $item;
            }
        }
    }

}
