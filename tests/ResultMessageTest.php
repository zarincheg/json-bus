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
        $this->assertEquals($a['taskId'], $m->taskId);
        $this->assertEquals($a['jobStatus'], $m->jobStatus);

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
        return [
            [[
                "taskId" => "task123",
                "jobStatus" => "done"
            ]],
            [[
                "taskId" => "task345",
                "jobStatus" => "fail"
            ]],
            [[
                "taskId" => "task678",
                "jobStatus" => "done",
                "data" => []
            ]]
        ];
    }
}
