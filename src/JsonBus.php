<?php
/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Date: 12.08.2015
 * Time: 12:47
 */

namespace JsonBus;

use JsonBus\Messages\JsonBusMessage;
use JsonBus\Messages\MessageStructureNotFoundException;
use JsonBus\Messages\MessageTypeUndefinedException;
use JsonBus\Messages\UnregisteredMessageTypeException;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Fabric class for creating JsonBusMessage from messages in JSON format
 * @package JsonBus
 */
class JsonBus
{
    protected static $registeredMessages = [];

    /**
     * @param string $json The valid message in JSON format
     * @return JsonBusMessage
     * @throws MessageTypeUndefinedException
     * @throws UnregisteredMessageTypeException
     */
    public static function make($json)
    {
        $message = json_decode($json, true);

        if (!isset($message['type'])) {
            throw new MessageTypeUndefinedException('Undefined message type');
        }

        if (!isset(self::$registeredMessages[$message['type']])) {
            throw new UnregisteredMessageTypeException('Unregistered message type');
        }

        $class = self::$registeredMessages[$message['type']];

        return new $class($message);
    }

    /**
     * Register message types
     * @param string $name Name of message type
     * @param string $class Name of message structure class
     * @throws MessageStructureNotFoundException
     */
    public static function register($name, $class)
    {
        if (!class_exists("JsonBus".$class)) {
            throw new MessageStructureNotFoundException('JsonBus'.$class);
        }

        self::$registeredMessages[$name] = 'JsonBus'.$class;
    }

    /**
     * Deregister message types
     * @param null|string $name Type of message
     * @return bool
     */
    public static function deregister($name = null)
    {
        if (!$name) {
            self::$registeredMessages = [];
            return true;
        }

        if (isset(self::$registeredMessages[$name])) {
            unset(self::$registeredMessages[$name]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Helper for create queue
     * @param $name Queue name
     * @return RabbitQueue
     */
    public static function queue($name)
    {
        $connection = new AMQPStreamConnection(
            getenv('RABBITMQ_HOST'),
            getenv('RABBITMQ_PORT'),
            getenv('RABBITMQ_USER'),
            getenv('RABBITMQ_PASSWORD')
        );

        return new RabbitQueue($connection, $name);
    }
}
