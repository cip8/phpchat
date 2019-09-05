<?php

namespace App\Socket;

use App\Events\Event;

use Ratchet\ConnectionInterface;

class Broadcast
{
    protected $event;
    protected $connections;

    /**
     * @param   Event   $event
     * @param   array   $connections
     */
    public function __construct(Event $event, array $connections)
    {
        $this->event        = $event;
        $this->connections  = $connections;
    }
    
    /**
     * Broadcasts event to this connected user
     *
     * @param   ConnectionInterface $connection
     * @return  void
     */
    public function toThis(ConnectionInterface $connection) : void
    {
        $connection->send($this->event);
    }

    /**
     * Broadcasts event to given connection ID
     *
     * @param   int     $connectionId
     * @return  void
     */
    public function to(int $connectionId) : void
    {
        $this->connections[$connectionId]->send($this->event);
    }
}