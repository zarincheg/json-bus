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
     * Convert message to AMQP format
     * @return AMQPMessage
     */
    public function toAMQP()
    {
        return new AMQPMessage($this->toJson());
    }
}