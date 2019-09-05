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

        $this->broadcast(
            new MessageEvent($user_1, $message)
        )->toConversation([$user_2, $user_1], $this->conversations);

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
            )->toConversation([$user_1, $user_2], $this->conversations);

            $this->broadcast(
                new Error($errorMessage)
            )->toConversation([$user_2, $user_1], $this->conversations);
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

        $authUsers = $this->logInUsers([
            'user_1' => $payload->data->user_from,
            'user_2' => $payload->data->user_to
        ], $cId);

        if ($authUsers !== true) {
            $errorMessage = "Username {$authUsers['failed']} doesn't exist - please create a new account!";
                
            var_dump($errorMessage); // 'Log' error

            // Broadcasts error to user attempting to connect
            return $this->broadcast(
                new Error($errorMessage)
            )->toThis($connection);
        } 

        // Broadcasts the history between the two given users
        $user_1 = $this->conversations[$cId]['user_1'];
        $user_2 = $this->conversations[$cId]['user_2'];

        $this->broadcast(
            new History($user_1, $user_2)
        )->toThis($connection);

        // Notifies the recipient that current user(_1) is online
        $this->broadcast(
            new UserEnter($user_1, 'has just joined the conversation')
        )->toConversation([$user_2, $user_1], $this->conversations);

        // Checks the current status of the other user(_2)
        foreach ($this->conversations as $conversation) { // h
            if (
                $conversation['user_1'] === $user_2 &&
                $conversation['user_2'] === $user_1
            ) {
                $this->broadcast(
                    new UserEnter($user_2, 'is online right now')
                )->toThis($connection);
            }
        }
    }

    /**
     * Checks if given users exist in the database & 'logs' them into the app
     *
     * @param   array   $userNames
     * @param   int     $cId        Connection ID#
     * @return  void
     */
    private function logInUsers(array $userNames, int $cId) 
    {
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
