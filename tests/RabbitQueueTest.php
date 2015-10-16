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
use JsonBus\JsonBus;
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

        JsonBus::register('request', '\Messages\Request');

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

        $this->assertInstanceOf('\JsonBus\Messages\JsonBusMessage', $result);
        $this->assertEquals([
            "type" => "request",
            "id" => "1",
            "clientId" => "php-unit",
            "subject" => "just second test",
            "params" => [
                "case" => "push-get"
            ]
        ], $result->toArray());
    }

    public function testConsumeCallback()
    {
        $dotenv = new Dotenv(dirname(__DIR__));
        $dotenv->load();

        JsonBus::register('request', '\Messages\Request');

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

        $queue->registerCallback(function ($message) use ($queue) {
            $message = JsonBus::make($message->body);
            file_put_contents('test.json', $message->toJson());
            $queue->clearCallbacks('test');
        }, 'test');

        $queue->process();

        $this->assertJsonStringEqualsJsonString($message->toJson(), file_get_contents('test.json'));
    }
}
