<?php

namespace App\Socket;

use App\Events\UserExit;
use App\Socket\SocketBase;
use App\Traits\ChatEventsTrait;

use Exception;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class ChatSocket extends SocketBase implements MessageComponentInterface
{
    use ChatEventsTrait;

    protected $connections;

    protected $conversations;

    /**
     * Called when a new connection is opened
     * 
     * @param   ConnectionInterface     $connection   The socket that just connected
     * @throws  Exception
     */
    public function onOpen(ConnectionInterface $connection)
    {
        $this->connections[$connection->resourceId] = $connection;
    }

    /**
     * Triggered when a client sends data through given socket
     * 
     * @param   ConnectionInterface     $connection     The socket that sent the message
     * @param   string                  $message        The message received
     * @throws  Exception
     */
    public function onMessage(ConnectionInterface $connection, $message)
    {
        $payload = json_decode($message);

        if (method_exists($this, $method = 'handle' . ucfirst($payload->event))) {
            $this->{$method}($connection, $payload);
        }
    }

    /**
     * Called when a socket / connection is closed
     * 
     * @param   ConnectionInterface     $connection     The socket that is closing
     * @throws  Exception
     */
    public function onClose(ConnectionInterface $connection)
    {
        $cId = $connection->resourceId;

        if (!isset($this->conversations[$cId])) {
            return;
        }

        $user_1 = $this->conversations[$cId]['user_1'];
        $user_2 = $this->conversations[$cId]['user_2'];

        $this->broadcast(
            new UserExit($user_1, 'has left the conversation')
        )->toConversation([$user_2, $user_1], $this->conversations);

        unset(
            $this->connections[$cId], 
            $this->conversations[$cId]
        );
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown, the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * 
     * @param   ConnectionInterface     $connection
     * @param   \Exception              $e
     * @throws  \Exception
     */
    public function onError(ConnectionInterface $connection, Exception $e)
    {
        $connection->close();
    }
}