<?php

namespace App\Events;

use App\Events\Event;
use App\Models\User;

class Message extends Event
{
    protected $username;
    protected $message;

    /**
     * @param   int     $userId
     * @param   Object  $message
     */
    public function __construct(int $userId, Object $message)
    {
        $this->username = (new User())->find($userId)->getUsername();
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function eventName() : string
    {
        return 'message';
    }

    /**
     * @return Object
     */
    public function data() : Object
    {
        $payload = $this->message;
        $payload->user_from = $this->username;

        return $payload;
    }
}
