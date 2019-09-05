<?php

namespace App\Events;

use App\Events\Event;

class Error extends Event
{
    protected $message;

    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function eventName() : string
    {
        return 'error';
    }

    /**
     * @return Object
     */
    public function data() : Object
    {
        return (object) [
            'error' => $this->message
        ];
    }
}
