<?php


namespace App\Command;


use App\Entity\Product;
use App\Service\Bot;
use App\Service\Helper;
use App\Service\Queue;
use App\Util\Constant;
use App\Util\Dto\SenderUpdateItemDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Wrep\Daemonizable\Command\EndlessContainerAwareCommand;

class RuntimeUpdateItem extends EndlessContainerAwareCommand
{
    const DEFAULT_TIME = 'PT1H';

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
     * @var float|null
     */
    private $defaultFactor;

    /**
     * @var int
     */
    private $timeout;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Bot $bot, Queue $queue, ContainerInterface $container, Helper $helper, SerializerInterface $serializer)
    {
        $this->entity = $entity;
        $this->param = $param;
        $this->bot = $bot;
        $this->queue = $queue;
        $this->container = $container;
        $this->bot = $bot;
        $this->helper = $helper;
        $this->serializer = $serializer;
        $this->defaultFactor = 1;
        $this->param = $this->helper->convertObject($param->get(Constant::CONFIG_NAME[__CLASS__]));
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:run.upd.item')
            ->setDescription('runtime update item parser start');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->bot->init(Constant::RUNTIME_TELEGRAM_BOT);
        $this->queue->init($this->param->name);

        if (!$this->entity->getConnection()) {
            $this->bot->message(Constant::RUNTIME_GROUP, 'UpdateProducer::' . Constant::BOT_SYSTEM_MESSAGES['error']);
            $this->setReturnCode(Constant::RUNTIME_RETURN_CODE);
            $this->shutdown();
        }
    }

    protected function startIteration(InputInterface $input, OutputInterface $output): void
    {
        gc_collect_cycles();
        gc_mem_caches();
        if (memory_get_usage() >= $this->param->limit) {
            $this->shutdown();
        }

        $this->helper->initParam();
        $this->defaultFactor = $this->helper->getParam()->getFactor() ?: 1;

        if (!$this->entity->isOpen()) {
            $this->container->get('doctrine')->resetManager();
            $this->entity = $this->container->get('doctrine.orm.default_entity_manager');
        }
        parent::startIteration($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $list = $this->entity->getRepository(Product::class)->getUpdateLastItemList(new \DateTime(), $this->getLastDate(), $this->param->list);
        $list->rewind();
        $this->timeout = 0;
        $this->iterate($list);
        unset($list);
        $this->throwExceptionOnShutdown();
    }

    protected function finishIteration(InputInterface $input, OutputInterface $output): void
    {
        $this->entity->clear();
        $time = $this->timeout * $this->param->iterate;
        if ($this->timeout <= 0) {
            $time = $this->param->sleep;
        }

        $this->setTimeout($time);
        parent::finishIteration($input, $output);
    }

    protected function finalize(InputInterface $input, OutputInterface $output): void
    {
        parent::finalize($input, $output);
    }

    private function iterate(\Generator $list): void
    {
        try {
            $id = $list->current();
        } catch (\InvalidArgumentException | \Exception $e) {
            return;
        }

        if (!$id || empty($id)) {
            return;
        }

        $now = new \DateTime();
        $end = clone $now;
        try {
            $end->add(new \DateInterval($this->param->range));
        } catch (\InvalidArgumentException | \Exception $e) {
            $end->add(new \DateInterval(self::DEFAULT_TIME));
        }

        $this->queue->write(
            $this->serializer->serialize(
                (new SenderUpdateItemDto())
                    ->setId($id)
                    ->setStart($now->format('Y-m-d H:i:s'))
                    ->setEnd($end->format('Y-m-d H:i:s'))
                    ->setFactor($this->defaultFactor),
                'json'
            )
        );

        $this->timeout++;
        $list->next();
        $this->iterate($list);
    }

    private function getLastDate(): \DateInterval
    {
        try {
            $last = new \DateInterval($this->param->range);
        } catch (\Exception $e) {
            $last = new \DateInterval(self::DEFAULT_TIME);
        }

        return $last;
    }

}
