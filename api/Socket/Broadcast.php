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
    public function toThis(ConnectionInterface $connection)
    {
        $connection->send($this->event);
    }

    /**
     * Broadcasts event to the other connected user
     *
     * @param   array           $users
     * @param   conversations   $conversations
     * @return  void
     */
    public function toConversation(array $users, array $conversations)
    {
        foreach ($conversations as $cId => $conversation) { // h
            if (
                $conversation['user_1'] === $users[0] &&
                $conversation['user_2'] === $users[1]
            ) {
                $this->connections[$cId]->send($this->event);
            }
        }
    }
}