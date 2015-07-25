<?php
/**
 * Created by Kirill Zorin <zarincheg@gmail.com>
 * Personal website: http://libdev.ru
 *
 * Structure of Request message
 *
 * Date: 24.07.15
 * Time: 19:39
 */
namespace Messages;

use JsonSchema\Uri\UriRetriever;

/**
 * Class Request
 * @package Messages
 */
class BaseMessage implements Message
{
    private $message = null;

    /**
     * @param array $message
     * @throws InvalidMessageException
     */
    public function __construct(array $message)
    {
        $schema = dirname(__DIR__) . '/MessagesSchema/' . $this->schema . '.json';
        $errors = '';
        $message = json_encode($message, JSON_FORCE_OBJECT);
        $retriever = new UriRetriever;
        $schema = $retriever->retrieve('file://' . realpath($schema));

        $validator = new \JsonSchema\Validator();
        $validator->check(json_decode($message), $schema);

        if ($validator->isValid()) {
            $this->message = $message;
        } else {
            foreach ($validator->getErrors() as $error) {
                $errors .= sprintf("[%s] %s\n", $error['property'], $error['message']);
            }

            throw new InvalidMessageException($errors);
        }
    }

    /**
     * Convert message object to JSON string
     * @return string
     */
    public function toJson()
    {
        return $this->message;
    }

    /**
     * Convert message object to array
     * @return array
     */
    public function toArray()
    {
        return json_decode($this->message, true);
    }

    /**
     * Returns message field if exists
     * @param $field
     * @return mixed
     */
    public function __get($field)
    {
        $m = $this->toArray();
        return $m[$field];
    }
}
