<?php
/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Date: 11.08.2015
 * Time: 15:05
 */

namespace JsonBus\Messages;


use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class for using Json bus messages with AMQP protocol
 * @package Messages
 */
class JsonBusMessage extends BaseMessage
{
    /**
     * @var string Publisher key(tag). It is used as a delivery key for AMQP messages
     *             and also possible for the message sender identification
     */
    protected $publisher = '';
    protected $deliveryTag = null;

    /**
     * @param array $message
     * @throws InvalidMessageException
     */
    public function __construct(array $message)
    {
        if (!isset($message['type']) || $message['type'] !== $this->schema) {
            $message['type'] = $this->schema;
        }

        parent::__construct($message);
    }

    /**
     * Convert message to AMQP format
     * @return AMQPMessage
     */
    public function toAMQP()
    {
        return new AMQPMessage($this->toJson());
    }
}
