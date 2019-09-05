<?php

namespace App\Events;

use App\Events\Event;
use App\Models\User;

class UserEnter extends Event
{
    protected $username;
    protected $message;

    /**
     * @param   int     $userId
     * @param   string  $message
     */
    public function __construct(int $userId, string $message)
    {
        $this->username = (new User())->find($userId)->getUsername();
        $this->message  = $message;
    }

    /**
     * @return string
     */
    public function eventName() : string
    {
        return 'enter';
    }

    /**
     * @return Object
     */
    public function data() : Object
    {
        return (object) [
            'username'  => $this->username,
            'message'   => $this->message
        ];
    }
}
