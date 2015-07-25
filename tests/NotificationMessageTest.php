<?php
namespace Tests;

use Messages\Notification;

/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Date: 24.07.15
 * Time: 18:20
 */
class NotificationMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Messages\InvalidMessageException
     * @dataProvider invalidMessageProvider
     */
    public function testException($a)
    {
        new Notification($a);
    }
    /**
     * @dataProvider validMessageProvider
     */
    public function testToJson($a)
    {
        $m = new Notification($a);
        $this->assertJson($m->toJson());
        $this->assertJsonStringEqualsJsonString(json_encode($a, JSON_FORCE_OBJECT), $m->toJson());
    }

    /**
     * @dataProvider validMessageProvider
     */
    public function testToArray($a)
    {
        $m = new Notification($a);
        $this->assertTrue(is_array($m->toArray()));
        $this->assertEquals($a, $m->toArray());
    }

    /**
     * @dataProvider validMessageProvider
     */
    public function testGet($a)
    {
        $m = new Notification($a);
        $this->assertEquals($a['requestId'], $m->requestId);
        $this->assertEquals($a['status'], $m->status);
        $this->assertEquals($a['message'], $m->message);

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
                "requestId" => "request1",
                "status" => 'success'
            ]],
            [[
                "requestId" => "request2",
                "status" => 'fail',
                "message" => []
            ]],
            [[
                "requestId" => "request3",
                "status" => 'none',
                "message" => "Test message of notification",
                "data" => ['test' => []]
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
                "requestId" => "request1",
                "status" => 'success',
                "message" => "Test message of notification"
            ]],
            [[
                "requestId" => "request2",
                "status" => 'fail',
                "message" => "Test message of notification"
            ]],
            [[
                "requestId" => "request3",
                "status" => 'fail',
                "message" => "Test message of notification",
                "data" => ['test' => []]
            ]]
        ];
    }
}
