<?php


namespace App\Service;


use App\Entity\Product;
use App\Util\Constant;
use App\Util\SystemServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StatsSender implements SystemServiceInterface
{
    /**
     * @var object
     */
    private $param;

    /**
     * @var EntityManagerInterface
     */
    private $entity;

    /**
     * @var Bot
     */
    private $bot;

    /**
     * @var string
     */
    private $message;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, Bot $bot)
    {
        $this->entity = $entity;
        $this->bot = $bot;
        $this->bot->init(Constant::BOT_TELEGRAM);
        $this->param = (object) $param->get('queue.param');
    }

    public function import(array $list = [])
    {
        return;
    }

    private function getQueue(): void
    {
        $this->message .= "\n\n";
        $client = new Client([
            'base_uri' => 'http://' . $this->param->host . ':15672/',
            'auth' => [$this->param->name, $this->param->pass],
        ]);

        try {
            $response = $client->get('/api/queues');
        } catch (GuzzleException $e) {
            $this->message .= 'Получить состояние очередей не получилось.';
            return;
        }

        $data = \json_decode($response->getBody(), true);

        foreach ($data as $queue) {
            $queueName = $queue['name'];
            $messageCount = $queue['messages'];
            $activeConsumers = $queue['consumers'];
            $this->message .= "Очередь: $queueName, Количество: $messageCount, Слушателей: $activeConsumers" . "\n";
        }
    }

    private function getDb()
    {
        $this->message .= "\n\n";
        $list = $this->entity->getRepository(Product::class)->getCountUpdateLastItems(new \DateTime(), new \DateInterval('PT2H'));
        $this->message .= "Товаров требующих обновления за последние 2 часа: " . $list . "\n";
    }

    public function run()
    {
        $this->message = '[System health checker] ';
        $this->message .= "Промежуточный отчет\n\n";
        $this->getQueue();
        $this->getDb();
        $this->message .= "\n\nСтатистика актуальна на " . (new \DateTime())->format('Y-m-d H:i:s');
        $this->bot->message(null, $this->message);
    }
}
