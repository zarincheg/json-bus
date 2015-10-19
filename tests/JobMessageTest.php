<?php
namespace Tests;

use JsonBus\Messages\Job;

/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Date: 24.07.15
 * Time: 18:20
 */
class JobMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \JsonBus\Messages\InvalidMessageException
     * @dataProvider invalidMessageProvider
     */
    public function testException($a)
    {
        new Job($a);
    }
    /**
     * @dataProvider validMessageProvider
     */
    public function testToJson($a)
    {
        $m = new Job($a);
        $this->assertJson($m->toJson());
        $this->assertJsonStringEqualsJsonString(json_encode($a, JSON_FORCE_OBJECT), $m->toJson());
    }

    /**
     * @dataProvider validMessageProvider
     */
    public function testToArray($a)
    {
        $m = new Job($a);
        $this->assertTrue(is_array($m->toArray()));
        $this->assertEquals($a, $m->toArray());
    }

    /**
     * @dataProvider validMessageProvider
     */
    public function testGet($a)
    {
        $m = new Job($a);
        $this->assertEquals($a['id'], $m->id);
        $this->assertEquals($a['taskId'], $m->taskId);
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
                "taskId" => "string"
            ]],
            [[
                "id" => "string",
                "taskId" => 123,
                "createTime" => 123
            ]],
            [[
                "id" => "string",
                "taskId" => 123,
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
                "type" => "job",
                "id" => "req123",
                "taskId" => "clientBob1",
                "createTime" => $time,
                "status" => 'success'
            ]],
            [[
                "type" => "job",
                "id" => "req123",
                "taskId" => "clientBob1",
                "createTime" => $time,
                "status" => 'fail'
            ]],
            [[
                "type" => "job",
                "id" => "req123",
                "taskId" => "clientBob1",
                "createTime" => $time,
                "status" => 'success',
                "params" => ['test' => 1],
                "data" => ['test' => []]
            ]]
        ];
    }
}
