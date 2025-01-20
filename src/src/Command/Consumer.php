<?php


namespace App\Command;

use App\Service\Bot;
use App\Service\Persist;
use App\Service\Queue;
use App\Service\Sender as SenderService;
use App\Service\Validator;
use App\Service\VeneraParser;
use App\Util\Constant;
use App\Util\Consumer\SenderUtilCreateItem;
use App\Util\Dto\SenderUtilOptionDto;
use App\Util\Consumer\SenderUtilEmail;
use App\Util\Consumer\SenderUtilMarketImport;
use App\Util\Consumer\SenderUtilTelegram;
use App\Util\Consumer\SenderUtilUpdateItem;
use App\Util\Tools\SystemUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class Consumer extends Command
{
    const COMMAND = 'app:consumer';
    const DESC = 'Sender';
    const HELP = 'helper';

    /**
     * @var SenderService
     */
    private $senderService;

    /**
     * @var Bot
     */
    private $bot;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var EntityManagerInterface
     */
    public $entity;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var VeneraParser
     */
    private $parser;

    /**
     * @var object
     */
    private $param;

    public function __construct(
        EntityManagerInterface $entity,
        SenderService $senderService,
        Bot $bot,
        Queue $queue,
        Validator $validator,
        VeneraParser $parser,
        ParameterBagInterface $param
    ) {
        $this->entity = $entity;
        $this->senderService = $senderService;
        $this->bot = $bot;
        $this->queue = $queue;
        $this->parser = $parser;
        $this->validator = $validator;
        $this->param = SystemUtil::convertObjectInArray($param->get(Constant::CONFIG_NAME[__CLASS__]));

        $this->senderService->init();
        $this->bot->init($this->param->telegram->name);
        if (!$this->parser->auth()) {
            return;
        }

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESC)
            ->addOption(
                $this->param->telegram->name,
                null,
                InputOption::VALUE_NONE,
                'App send tg message'
            )
            ->addOption(
                $this->param->email->name,
                null,
                InputOption::VALUE_NONE,
                'App send mail message'
            )
            ->addOption(
                $this->param->package->name,
                null,
                InputOption::VALUE_NONE,
                'App yml message'
            )
            ->addOption(
                $this->param->runupditem->name,
                null,
                InputOption::VALUE_NONE,
                'App update items'
            )
            ->addOption(
                $this->param->runcrtitem->name,
                null,
                InputOption::VALUE_NONE,
                'App create items'
            )
            ->addOption(
                self::HELP,
                null,
                InputOption::VALUE_NONE,
                'Need Help?'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption(self::HELP)) {
            $output->writeln("Command list:\r\n");
            $output->writeln("\t--" . self::HELP . "\t - Хелпер");
            $output->writeln("\t--" . $this->param->telegram->name . "\t - Отправить сообщение из очереди в телеграмм");
            $output->writeln("\t--" . $this->param->email->name . "\t\t - Отправить сообщение из очереди на почту");
            $output->writeln("\t--" . $this->param->package->name . "\t\t - Записать товар из ценовой сетки в пакетную продажу");
            $output->writeln("\t--" . $this->param->runupditem->name . "\t\t - Обновление товаров из парсера");
            $output->writeln("\t--" . $this->param->runupditem->name . "\t\t - Создание товаров из парсера");
            $output->writeln("\r\n");
            return Command::SUCCESS;
        }

        if ($input->getOption($this->param->email->name)) {
            $this->queue->init($this->param->email->name, 'Email messages reader');
            $this->queue->register(SenderUtilEmail::read(
                    (new SenderUtilOptionDto())
                        ->setSerializer($this->serializer)
                        ->setSenderService($this->senderService)
                        ->setQueue($this->queue)
                ),
                $this->param->email->limit
            );
            $this->queue->read();
            $this->queue->await();
            $this->queue->closed();
            return Command::SUCCESS;
        }

        if ($input->getOption($this->param->telegram->name)) {
            $this->queue->init($this->param->telegram->name, 'Telegram messages reader');
            $this->queue->register(SenderUtilTelegram::read(
                    (new SenderUtilOptionDto())
                        ->setSerializer($this->serializer)
                        ->setBot($this->bot)
                        ->setQueue($this->queue)
                ),
                $this->param->telegram->limit
            );
            $this->queue->read();
            $this->queue->await();
            $this->queue->closed();
            return Command::SUCCESS;
        }

        if ($input->getOption($this->param->package->name)) {
            $this->queue->init($this->param->package->name, 'Market Export messages reader');
            $this->queue->register(SenderUtilMarketImport::read(
                    (new SenderUtilOptionDto())
                        ->setSerializer($this->serializer)
                        ->setEntityManager($this->entity)
                        ->setBot($this->bot)
                        ->setQueue($this->queue)
                ),
                $this->param->package->limit
            );
            $this->queue->read();
            $this->queue->await();
            $this->queue->closed();
            return Command::SUCCESS;
        }

        if ($input->getOption($this->param->runupditem->name)) {
            $this->queue->init($this->param->runupditem->name, 'Item update message reader');
            $this->queue->register(SenderUtilUpdateItem::read(
                    (new SenderUtilOptionDto())
                        ->setSerializer($this->serializer)
                        ->setEntityManager($this->entity)
                        ->setQueue($this->queue)
                        ->setValidator($this->validator)
                        ->setParser($this->parser)
                ),
                $this->param->runupditem->limit
            );
            $this->queue->read();
            $this->queue->await();
            $this->queue->closed();
            return Command::SUCCESS;
        }

        if ($input->getOption($this->param->runcrtitem->name)) {
            $this->queue->init($this->param->runcrtitem->name, 'Item create message reader');
            $this->queue->register(SenderUtilCreateItem::read(
                (new SenderUtilOptionDto())
                    ->setSerializer($this->serializer)
                    ->setEntityManager($this->entity)
                    ->setQueue($this->queue)
                    ->setValidator($this->validator)
                    ->setParser($this->parser)
            ),
                $this->param->runupditem->limit
            );
            $this->queue->read();
            $this->queue->await();
            $this->queue->closed();
            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }

}
