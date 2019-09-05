<?php

namespace App\Socket;

use App\Events\Event;
use App\Socket\Broadcast;

abstract class SocketBase
{
    /**
     * Broadcasts event
     *
     * @param   Event   $event
     * @return  Broadcast
     */
    protected function broadcast(Event $event) : Broadcast
    {
        return new Broadcast($event, $this->connections);
    }
}
