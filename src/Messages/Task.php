<?php
/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Date: 25.07.15
 * Time: 16:59
 */

namespace JsonBus\Messages;


/**
 * Class Request
 * @package Messages
 */
class Task extends JsonBusMessage
{
    protected $schema = 'task';
}
