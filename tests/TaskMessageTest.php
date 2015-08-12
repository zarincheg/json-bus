<?php
namespace Tests;

use JsonBus\Messages\Task;

/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Date: 24.07.15
 * Time: 18:20
 */
class TaskMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \JsonBus\Messages\InvalidMessageException
     * @dataProvider invalidMessageProvider
     */
    public function testException($a)
    {
        new Task($a);
    }
    /**
     * @dataProvider validMessageProvider
     */
    public function testToJson($a)
    {
        $m = new Task($a);
        $this->assertJson($m->toJson());
        $this->assertJsonStringEqualsJsonString(json_encode($a, JSON_FORCE_OBJECT), $m->toJson());
    }

    /**
     * @dataProvider validMessageProvider
     */
    public function testToArray($a)
    {
        $m = new Task($a);
        $this->assertTrue(is_array($m->toArray()));
        $this->assertEquals($a, $m->toArray());
    }

    /**
     * @dataProvider validMessageProvider
     */
    public function testGet($a)
    {
        $m = new Task($a);
        $this->assertEquals($a['id'], $m->id);
        $this->assertEquals($a['requestId'], $m->requestId);
        $this->assertEquals($a['createTime'], $m->createTime);
        $this->assertEquals($a['status'], $m->status);

        if (isset($a['params'])) {
            $this->assertEquals($a['params'], $m->params);
        }

        if (isset($a['data'])) {
            $this->assertEquals($a['data'], $m->data);
        }
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
                "requestId" => "string"
            ]],
            [[
                "id" => "string",
                "requestId" => 123,
                "createTime" => 123
            ]],
            [[
                "id" => "string",
                "requestId" => 123,
                "createTime" => 123,
                "status" => 'test'
            ]]
        ];
    }

    /**
     * @return array
     */
    public function validMessageProvider()
    {
        $time = date('d-m-Y H:i:s');
        return [
            [[
                "type" => "task",
                "id" => "req123",
                "requestId" => "clientBob1",
                "createTime" => $time,
                "status" => 'active'
            ]],
            [[
                "type" => "task",
                "id" => "req123",
                "requestId" => "clientBob1",
                "createTime" => $time,
                "status" => 'active'
            ]],
            [[
                "type" => "task",
                "id" => "req123",
                "requestId" => "clientBob1",
                "createTime" => $time,
                "status" => 'active',
                "params" => ['test' => 1],
                "data" => ['test' => []]
            ]]
        ];
    }
}
