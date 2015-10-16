<?php
/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Date: 10.08.2015
 * Time: 12:38
 */

namespace JsonBus;

use JsonBus\Messages\JsonBusMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class RabbitQueue implements work with RabbitMQ queues
 * @package JsonBus
 */
class RabbitQueue implements Queue
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;
    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel;
     */
    private $channel;
    /**
     * @var string Name of RabbitMQ exchange
     */
    private $exchange = '';
    /**
     * @var string Name of queue
     */
    private $queue;
    /**
     * @var AMQPMessage
     */
    private $lastReceivedMessage;

    /**
     * @param AMQPStreamConnection $amqpConnection
     * @param string $queue Queue name
     * @param string $exchange Exchange name
     * @param null $key Routing key
     */
    public function __construct(AMQPStreamConnection $amqpConnection, $queue, $exchange = 'jsonbus', $key = null)
    {
        $this->connection = $amqpConnection;
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare($exchange, 'direct', false, true);
        $this->channel->queue_declare($queue, false, true);

        if (!$key) {
            $key = $queue;
        }

        $this->channel->queue_bind($queue, $exchange, $key);

        $this->exchange = $exchange;
        $this->queue = $queue;
    }

    /**
     * @param \JsonBus\Messages\JsonBusMessage $message
     * @param null $key Routing key
     * @return mixed|void
     */
    public function push(JsonBusMessage $message, $key = null)
    {
        if (!$key) {
            $key = $this->queue;
        }

        $this->channel->basic_publish($message->toAMQP(), $this->exchange, $key);
    }

    /**
     * Callback for handle received messages
     * @param callable $callback
     * @param string $tag
     */
    public function registerCallback(callable $callback, $tag = '')
    {
        $this->channel->basic_consume($this->queue, $tag, false, true, false, false, $callback);
    }

    /**
     * @param string $tag Consumer tag
     */
    public function clearCallbacks($tag = '')
    {
        $this->channel->basic_cancel($tag);
    }

    /**
     * @return bool
     */
    public function process()
    {
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        return true;
    }

    /**
     * @param bool|false $acknowledge Auto acknowledgement flag
     * @return JsonBusMessage
     */
    public function get($acknowledge = false)
    {
        $message = $this->channel->basic_get($this->queue, $acknowledge);

        return JsonBus::make($message->body);
    }

    /**
     * @param array $messages Bunch of messages objects
     * @return mixed
     * @throws \Exception
     */
    public function pushBatch(array $messages)
    {
        foreach ($messages as $message) {
            if (!($message instanceof JsonBusMessage)) {
                throw new \Exception("Each message must be instance of JsonBusMessage");
            }

            $this->channel->batch_basic_publish($message->toAMQP(), $this->exchange, $this->queue);
        }

        $this->channel->publish_batch();
    }

    /**
     * @return array
     */
    public function getBatch()
    {
        // TODO: Implement getBatch() method.
    }

    /**
     * Acknowledge message
     * @param JsonBusMessage $message
     * @return void
     */
    public function ack(JsonBusMessage $message)
    {
        $this->channel->basic_ack($message->deliveryTag);
    }

    /**
     * Closing connection to the server with possible to delete queue
     * @param bool|false $delete Set true for delete queue before closing connection
     */
    public function close($delete = false)
    {
        if ($delete) {
            $this->delete();
        }

        $this->channel->close();
        $this->connection->close();
    }

    /**
     * Unbind and delete queue
     */
    public function delete()
    {
        $this->channel->queue_unbind($this->queue, $this->exchange);
        $this->channel->queue_delete($this->queue);
    }
}
