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
     */
    public function __construct(AMQPStreamConnection $amqpConnection, $queue, $exchange = 'jsonbus')
    {
        $this->connection = $amqpConnection;
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare($exchange, 'direct', false, true);
        $this->channel->queue_declare($queue, false, true);
        $this->channel->queue_bind($queue, $exchange, $queue);

        $this->exchange = $exchange;
        $this->queue = $queue;
    }

    /**
     * @param \JsonBus\Messages\JsonBusMessage $message
     * @return mixed|void
     */
    public function push(JsonBusMessage $message)
    {
        $this->channel->basic_publish($message->toAMQP(), $this->exchange, $this->queue);
    }

    /**
     * Callback for handle received messages
     * @param AMQPMessage $message
     */
    public function consumeCallback(AMQPMessage $message)
    {
        $this->lastReceivedMessage = $message;
    }

    /**
     * @param bool|false $acknowledge Auto acknowledgement flag
     * @return string
     */
    public function get($acknowledge = false)
    {
        $message = $this->channel->basic_get($this->queue, $acknowledge);

        // @todo Makes return JsonBus message object.
        return $message->body;
    }

    public function ack()
    {
        //$this->channel->basic_ack()
    }

    /**
     * @param array $messages Bunch of messages objects
     * @return mixed
     */
    public function pushBatch(array $messages)
    {
        // TODO: Implement pushBatch() method.
    }

    /**
     * @return array
     */
    public function getBatch()
    {
        // TODO: Implement getBatch() method.
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
