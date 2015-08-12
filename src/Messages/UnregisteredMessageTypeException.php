<?php
/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Exception for the case in which the message type was not registered by JsonBus
 *
 * Date: 24.07.15
 * Time: 21:53
 */

namespace JsonBus\Messages;


/**
 * Class InvalidMessageException
 * @package Messages
 */
class UnregisteredMessageTypeException extends \Exception
{

}
