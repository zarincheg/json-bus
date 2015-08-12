<?php
namespace Tests;

use JsonBus\Messages\Request;

/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Date: 24.07.15
 * Time: 18:20
 */
class RequestMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \JsonBus\Messages\InvalidMessageException
     * @dataProvider invalidMessageProvider
     */
    public function testException($a)
    {
        new Request($a);
    }
    /**
     * @dataProvider validMessageProvider
     */
    public function testToJson($a)
    {
        $m = new Request($a);
        $this->assertJson($m->toJson());
        $this->assertJsonStringEqualsJsonString(json_encode($a, JSON_FORCE_OBJECT), $m->toJson());
    }

    /**
     * @dataProvider validMessageProvider
     */
    public function testToArray($a)
    {
        $m = new Request($a);
        $this->assertTrue(is_array($m->toArray()));
        $this->assertEquals($a, $m->toArray());
    }

    /**
     * @dataProvider validMessageProvider
     */
    public function testGet($a)
    {
        $m = new Request($a);
        $this->assertEquals($a['id'], $m->id);
        $this->assertEquals($a['clientId'], $m->clientId);
        $this->assertEquals($a['subject'], $m->subject);
        $this->assertEquals($a['params'], $m->params);
        $this->assertEquals($a['data'], $m->data);
    }

    /**
     * @return array
     */
    public function invalidMessageProvider()
    {
        return [
            [[]],
            [[1,2,3]],
            [['id' => 'test']],
            [['id' => true]],
            [[
                "id" => "string",
                "clientId" => "string"
            ]],
            [[
                "id" => "string",
                "clientId" => 123,
                "subject" => 123
            ]]
        ];
    }

    /**
     * @return array
     */
    public function validMessageProvider()
    {
        return [
            [[
                "type" => "request",
                "id" => "req123",
                "clientId" => "clientBob1",
                "subject" => "TestMe",
                "params" => ['testNumParam' => 10],
                "data" => []
            ]],
            [[
                "type" => "request",
                "id" => "req123",
                "clientId" => "clientBob1",
                "subject" => "TestMe",
                "params" => ['testNumParam' => 10],
                "data" => "string"
            ]]
        ];
    }
}
