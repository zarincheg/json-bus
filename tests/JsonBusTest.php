<?php
/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Date: 12.08.2015
 * Time: 16:39
 */

namespace JsonBus\Tests;


use JsonBus\JsonBus;

/**
 * Class JsonBusTest
 * @package JsonBus\Tests
 */
class JsonBusTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        JsonBus::register('request', '\Messages\Request');

        $json = json_encode([
            "type" => "request",
            "id" => "1",
            "clientId" => "php-unit",
            "subject" => "just second test",
            "params" => [
                "case" => "push-get"
            ]
        ]);
        $message = JsonBus::make($json);

        JsonBus::deregister();

        $this->assertInstanceOf('\JsonBus\Messages\JsonBusMessage', $message);
    }

    /**
     * @expectedException \JsonBus\Messages\UnregisteredMessageTypeException
     */
    public function testMakeWithoutRegister()
    {
        $json = json_encode([
            "type" => "request",
            "id" => "1",
            "clientId" => "php-unit",
            "subject" => "just second test",
            "params" => [
                "case" => "push-get"
            ]
        ]);

        JsonBus::make($json);
    }

    /**
     * @expectedException \JsonBus\Messages\MessageTypeUndefinedException
     */
    public function testMakeWithoutType()
    {
        $json = json_encode([
            "id" => "1",
            "clientId" => "php-unit",
            "subject" => "just second test",
            "params" => [
                "case" => "push-get"
            ]
        ]);

        JsonBus::make($json);
    }

    /**
     * @expectedException \JsonBus\Messages\MessageStructureNotFoundException
     */
    public function testRegisterWithoutClass()
    {
        JsonBus::register('test', 'GoodTestClass');
        JsonBus::deregister();
    }

    /**
     * @expectedException \JsonBus\Messages\InvalidMessageException
     */
    public function testMakeWithBadMessage()
    {
        JsonBus::register('request', '\Messages\Request');

        $json = json_encode([
            "type" => "request",
            "id" => "1",
            "subject" => "just second test",
            "params" => [
                "case" => "push-get"
            ]
        ]);
        JsonBus::make($json);
        JsonBus::deregister();
    }

    protected function tearDown()
    {
        JsonBus::deregister();
    }
}
