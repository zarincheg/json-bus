<?php

namespace JsonBus;

use JsonBus\Messages\JsonBusMessage;
use JsonBus\Messages\Message;

/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 * at
 *
 * Date: 09.08.2015
 * Time: 21:58
 */
interface Queue
{
    /**
     * @param JsonBusMessage $message The message object with data
     * @return mixed
     */
    public function push(JsonBusMessage $message);

    /**
     * @param bool|false $acknowledge Auto acknowledgement flag
     * @return Message
     */
    public function get($acknowledge = false);
    public function ack();

    /**
     * @param array $messages Bunch of messages objects
     * @return mixed
     */
    public function pushBatch(array $messages);

    /**
     * @return array
     */
    public function getBatch();
}
