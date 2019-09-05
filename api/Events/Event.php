<?php

namespace App\Events;

abstract class Event
{
    /**
     * Returns the event name
     *
     * @return string
     */
    abstract public function eventName() : string;

    /**
     * Returns data related to the event
     */
    abstract public function data();

    public function __toString()
    {
        return json_encode([
            'event' => $this->eventName(),
            'data' => $this->data(),
        ]);
    }
}
