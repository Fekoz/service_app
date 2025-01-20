<?php


namespace App\Service;

use App\Util\Constant;
use App\Util\SenderUtilInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class Queue
{
    /**
     * @var $param array
     */
    private $param;

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @var string
     */
    private $queue;

    /**
     * @var string
     */
    private $desc;

    /**
     * @var \Closure
     */
    private $callback;

    /**
     * @var int
     */
    private $limit;

    /*
        Declare:
        Durable: True
        Passive: False
        Exclusive: false
        Auto_delete: false

        Ack:
        No_local: false
        No_ack: false
        Exclusive: false
        NoWait: false
    */

    public function __construct(ParameterBagInterface $param)
    {
        $this->param = (object) $param->get(Constant::CONFIG_NAME[__CLASS__]);
        $this->callback = null;
        $this->connection = new AMQPStreamConnection($this->param->host, $this->param->port, $this->param->name, $this->param->pass);
        $this->channel = $this->connection->channel();
    }

    public function init(string $queue = 'ANY', string $desc = 'Default') {
        $this->queue = $queue;
        $this->desc = $desc;

        $this->channel->queue_declare($this->queue,
            Constant::QUEUE_PASSIVE,
            Constant::QUEUE_DURABLE,
            Constant::QUEUE_EXCLUSIVE,
            Constant::QUEUE_AUTO_DELETE
        );
    }

    public function read() {
        if (!$this->callback ||
            !$this->callback instanceof \Closure)
        {
            $this->callback = function (AMQPMessage $message) {
                $this->acknowledge($message);
            };
        }

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->queue, $this->desc,
            Constant::QUEUE_NO_LOCAL,
            Constant::QUEUE_NO_ACK,
            Constant::QUEUE_EXCLUSIVE,
            Constant::QUEUE_NO_WAIT,
            $this->handlerRead()
        );
    }

    private function handlerRead(): \Closure
    {
        return function (AMQPMessage $message) {
            gc_collect_cycles();
            gc_mem_caches();
            if (memory_get_usage() >= $this->limit) {
                $this->closed();
            }

            try {
                $this->callback->call($this, $message);
            } catch (\Exception $e) {
                $this->acknowledge($message);
            }
        };
    }

    public function write(string $message = '')
    {
        $this->channel->basic_publish(new AMQPMessage($message), '', $this->queue);
    }

    public function writeQueue(string $message = '', string $name = 'ANY')
    {
        $this->channel->basic_publish(new AMQPMessage($message), '', $name);
    }

    public function register(\Closure $callback = null, $limit = 2000000) {
        $this->callback = $callback;
        $this->limit = $limit;
    }

    public function acknowledge(AMQPMessage $message) {
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }

    public function await()
    {
        try {
            while (true) {
                $this->channel->wait();
            }
        } catch (\Exception $e) {

        }
    }

    public function closed()
    {
        $this->channel->close();
        $this->connection->close();
    }

}
