<?php
/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Date: 11.08.2015
 * Time: 14:21
 */

namespace JsonBus\Tests;


use Dotenv\Dotenv;
use JsonBus\RabbitQueue;
use JsonBus\Messages\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Test communication bus with RabbitMQ queues
 * @package JsonBus\Tests
 */
class RabbitQueueTest extends \PHPUnit_Framework_TestCase
{
    public function testPushGet()
    {
        $dotenv = new Dotenv(dirname(__DIR__));
        $dotenv->load();

        $message = new Request([
            "id" => "1",
            "clientId" => "php-unit",
            "subject" => "just second test",
            "params" => [
                "case" => "push-get"
            ]
        ]);
        $connection = new AMQPStreamConnection(
            getenv('RABBITMQ_HOST'),
            getenv('RABBITMQ_PORT'),
            getenv('RABBITMQ_USER'),
            getenv('RABBITMQ_PASSWORD')
        );
        $queue = new RabbitQueue($connection, 'requests');
        $queue->push($message);

        $result = $queue->get(true);
        $queue->close();

        $this->assertJson($result);
    }
}
