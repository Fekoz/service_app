<?php


namespace App\Command;


use App\Entity\Product;
use App\Service\Bot;
use App\Service\MarketPlace;
use App\Service\Queue;
use App\Service\Validator;
use App\Service\VeneraParser;
use App\Util\Constant;
use App\Util\Dto\MarketPlaceDto;
use App\Util\Dto\SenderCreateItemDto;
use App\Util\Dto\SenderEmailDto;
use App\Util\Dto\SenderMarketExportDto;
use App\Util\Dto\SenderTelegramDto;
use App\Util\Dto\SenderUpdateItemDto;
use App\Util\Tools\SystemUtil;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TestConsumers extends Command
{
    const COMMAND = 'app:tstcs';
    const DESC = 'tst';

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var object
     */
    private $param;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var MarketPlace
     */
    private $mp;

    /**
     * @var EntityManagerInterface
     */
    protected $entity;

    public function __construct(Queue $queue, ParameterBagInterface $param, EntityManagerInterface $entity, MarketPlace $mp)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, ['SKIP_NULL_VALUES' => true])];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->param = SystemUtil::convertObjectInArray($param->get('consumer.param'));
        $this->queue = $queue;
        $this->entity = $entity;
        $this->mp = $mp;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESC)
        ;
    }

    private function package():void
    {
        $this->queue->init($this->param->package->name, 'package messages reader');
        $message = (new SenderMarketExportDto())
            ->setName('auto')
            ->setMid('6d2e8f92a098e7ebaa9a500bf724d43a')
            ->setCounterPkg(rand(1, 6));
        $this->queue->writeQueue($this->serializer->serialize($message,'json'), $this->param->package->name);
    }

    private function email():void
    {
        $this->queue->init($this->param->email->name, 'email messages reader');
        $message = (new SenderEmailDto())
            ->setMessage('Message: Привет мир, скрипт настроен!')
            ->setTo('lolpikds@mail.ru')
            ->setTitle('Message artem');
        $this->queue->writeQueue($this->serializer->serialize($message,'json'), $this->param->email->name);
    }

    private function telegram()
    {
        $this->queue->init($this->param->telegram->name, 'telegram messages reader');
        $message = (new SenderTelegramDto())
            ->setMessage('Message 2: Привет мир, скрипт настроен!');
        $this->queue->writeQueue($this->serializer->serialize($message,'json'), $this->param->telegram->name);
    }

    private function update()
    {
        $this->queue->init($this->param->runupditem->name, 'runupditem messages reader');
        $message = (new SenderUpdateItemDto())
            ->setId(4172)
            ->setFactor(5)
            ->setStart((new \DateTime())->format('Y-m-d H:i:s'))
            ->setEnd((new \DateTime())->add(new \DateInterval('PT1H'))->format('Y-m-d H:i:s'))
        ;
        $this->queue->writeQueue($this->serializer->serialize($message,'json'), $this->param->runupditem->name);
    }

    private function create()
    {
        $this->queue->init($this->param->runcrtitem->name, 'runupditem messages reader');
        $message = (new SenderCreateItemDto())
            ->setUid('TEST_UID')
            ->setUrl('/null')
            ->setFactor(1)
        ;
        $this->queue->writeQueue($this->serializer->serialize($message,'json'), $this->param->runcrtitem->name);
    }

    private function marketplace()
    {
//        $this->mp->run('CD468C-SIRIUS-BEIGE');
//        $this->mp->run('6F3E0B-BAKARAT-670-BEIGE');
//        $this->mp->run('07816B-LAMER-070-BEIGE');
//        $this->mp->run('D475FA-FARSI-1200-000');
        $this->mp->run('A20ECA-SHAGGY-ULTRA-LILAC');
        $this->mp->process(function ($a) {
            if ($a instanceof MarketPlaceDto) {
                dump($a);
                echo "\r\n\n\n--------------------\r\n";
            }
        });
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->marketplace();
        return Command::SUCCESS;
    }
}
