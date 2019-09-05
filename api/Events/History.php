<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Message;
use App\Models\User;

class History extends Event
{
    protected $user_1;
    protected $user_2;

    /**
     * @param int $user_1
     * @param int $user_2
     */
    public function __construct(int $user_1, int $user_2)
    {
        $this->user_1 = $user_1;
        $this->user_2 = $user_2;
    }

    /**
     * @return string
     */
    public function eventName() : string
    {
        return 'history';
    }

    /**
     * @return array
     */
    public function data()
    {
        return (new Message)->historyBetweenUserIds($this->user_1, $this->user_2);
    }
}
