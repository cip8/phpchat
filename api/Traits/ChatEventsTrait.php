<?php

namespace App\Traits;

use App\Events\Error;
use App\Events\History;
use App\Events\Message as MessageEvent;
use App\Events\UserEnter;
use App\Models\Message;
use App\Models\User;

use Ratchet\ConnectionInterface;

trait ChatEventsTrait
{
    /**
     * Handles the 'message' event
     * 
     * @param   ConnectionInterface     $connection
     * @param   Object                  $payload
     * @return  void
     */
    protected function handleMessage(ConnectionInterface $connection, Object $payload)
    {
        $message = $payload->data;

        $cId = $connection->resourceId;

        $user_1 = $this->conversations[$cId]['user_1'];
        $user_2 = $this->conversations[$cId]['user_2'];

        $recipientSessionId = isset($this->sessionId[$user_2][$user_1])
                                    ? $this->sessionId[$user_2][$user_1]
                                    : false;

        if ($recipientSessionId) {
            $this->broadcast(
                new MessageEvent($user_1, $message)
            )->to($recipientSessionId);
        }

        // Stores message in the database
        $store = (new Message())->store(
            $user_1,
            $user_2, 
            $message->content
        );

        // Broadcasts error if message can't be stored
        if(!$store) {
            $errorMessage = "We could't store your message in the database!";
            
            var_dump($errorMessage); // 'Log' error

            // Broadcasts error to both users
            $this->broadcast(
                new Error($errorMessage)
            )->toThis($connection);

            if ($recipientSessionId) {
                $this->broadcast(
                    new Error($errorMessage)
                )->to($recipientSessionId);
            }
        }
    }

    /**
     * Handles the user 'enter' event
     * 
     * @param   ConnectionInterface     $connection
     * @param   mixed                  $payload
     * @return  void
     */
    protected function handleEnter(ConnectionInterface $connection, Object $payload)
    {
        $cId = $connection->resourceId;

        // Tries to 'authenticate' the user & create a conversation
        $authUsers = $this->logInUsers($payload->data->user_from, $payload->data->user_to, $cId);

        if ($authUsers !== true) {
            $errorMessage = "Username {$authUsers['failed']} doesn't exist - please create a new account!";
                
            var_dump($errorMessage); // 'Log' error

            // Broadcasts error to user attempting to connect
            return $this->broadcast(
                new Error($errorMessage)
            )->toThis($connection);
        } 

        $user_1 = $this->conversations[$cId]['user_1'];
        $user_2 = $this->conversations[$cId]['user_2'];

        $this->sessionId[$user_1][$user_2] = $cId;
        // $saveSession = (new Session())->store($user_1, $user_2);
        
        // Broadcasts the history between the two given users
        $this->broadcast(
            new History($user_1, $user_2)
        )->toThis($connection);

        $recipientSessionId = isset($this->sessionId[$user_2][$user_1])
                                    ? $this->sessionId[$user_2][$user_1]
                                    : false;

        if ($recipientSessionId) {
            // Notifies the recipient that current user(_1) is online
            $this->broadcast(
                new UserEnter($user_1, 'has just joined the conversation')
            )->to($recipientSessionId);

            $this->broadcast(
                new UserEnter($user_2, 'is online right now')
            )->toThis($connection);
        }
    }

    /**
     * Checks if given users exist in the database
     * and creates a conversation for them
     *
     * @param   string  $user_from
     * @param   string  $user_to
     * @param   int     $cId        Connection ID#
     * @return  void
     */
    private function logInUsers(string $user_from, string $user_to, int $cId) 
    {
        $userNames = [ 
            'user_1' => $user_from,
            'user_2' => $user_to 
        ];

        foreach($userNames as $key => $userName) {

            $user = (new User())->findByUsername($userName);
            
            if (!$user) {
                return [
                    'failed' => $userName
                ];
            }

            // Stores info about user_1 & user_2
            $this->conversations[$cId][$key] = $user->getId();
        }

        return true;
    }

}
