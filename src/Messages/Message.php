<?php
namespace JsonBus\Messages;

/**
 * Interface for messages which will be used in communication between system nodes, workers and clients
 * @package Messages
 */
interface Message
{
    /**
     * @param array $message
     */
    public function __construct(array $message);
    /**
     * Convert message object to JSON string
     * @return string
     */
    public function toJson();

    /**
     * Convert message object to array
     * @return array
     */
    public function toArray();

    /**
     * Returns message field if exists
     * @param $field
     * @return mixed
     */
    public function __get($field);
}
