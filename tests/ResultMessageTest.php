<?php
namespace Tests;

use JsonBus\Messages\Result;

/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Date: 24.07.15
 * Time: 18:20
 */
class ResultMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \JsonBus\Messages\InvalidMessageException
     * @dataProvider invalidMessageProvider
     */
    public function testException($a)
    {
        new Result($a);
    }
    /**
     * @dataProvider validMessageProvider
     */
    public function testToJson($a)
    {
        $m = new Result($a);
        $this->assertJson($m->toJson());
        $this->assertJsonStringEqualsJsonString(json_encode($a, JSON_FORCE_OBJECT), $m->toJson());
    }

    /**
     * @dataProvider validMessageProvider
     */
    public function testToArray($a)
    {
        $m = new Result($a);
        $this->assertTrue(is_array($m->toArray()));
        $this->assertEquals($a, $m->toArray());
    }

    /**
     * @dataProvider validMessageProvider
     */
    public function testGet($a)
    {
        $m = new Result($a);
        $this->assertEquals($a['job'], $m->job);

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
                "taskId" => "string",
                "jobStatus" => "string"
            ]],
            [[
                "taskId" => "",
                "jobStatus" => ""
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
                "type" => "result",
                "job" => [
                    "id" => "req123",
                    "taskId" => "clientBob1",
                    "createTime" => $time,
                    "status" => 'success'
                ]
            ]],
            [[
                "type" => "result",
                "job" => [
                    "type" => "job",
                    "id" => "req123",
                    "taskId" => "clientBob1",
                    "createTime" => $time,
                    "status" => 'fail'
                ]
            ]],
            [[
                "type" => "result",
                "job" => [
                    "type" => "job",
                    "id" => "req123",
                    "taskId" => "clientBob1",
                    "createTime" => $time,
                    "status" => 'success',
                    "params" => ['test' => 1],
                    "data" => ['test' => []]
                ],
                "data" => []
            ]]
        ];
    }
}
