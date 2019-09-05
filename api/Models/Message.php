<?php

namespace App\Models;

use App\Models\User;

class Message extends BaseModel {

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->pluralizeClassName();
    }

    /**
     * Retrieves message history between two given users
     * 
     * @param   int     $user_1
     * @param   int     $user_2
     * @param   string  $order
     * @return  array
     */
    public function historyBetweenUserIds(int $user_1, int $user_2, string $order = 'DESC') : array
    {
        $this->db->query(
            "SELECT * FROM {$this->table} 
            WHERE user_from IN (:user_from, :user_to) 
            AND user_to IN (:user_from, :user_to)
            ORDER BY date {$order}"
        );

        $this->db->bind(':user_from', $user_1);
        $this->db->bind(':user_to', $user_2);

        if (!$this->db->hasResults()) {
            return [];
        }

        return $this->prepareMessages(
            $this->db->results()
        );
    }

    /**
     * Stores a message between given users in the database
     *
     * @param   int     $user_1
     * @param   int     $user_2
     * @param   string  $content
     * @return  bool
     */
    public function store(int $user_1, int $user_2, string $content) : bool
    {
        $this->db->query(
            "INSERT INTO {$this->table} (user_from,user_to,content) 
            VALUES (:user_from,:user_to,:content)"
        );

        $this->db->bind(':user_from', $user_1);
        $this->db->bind(':user_to', $user_2);        
        $this->db->bind(':content', $content);

        return $this->db->execute();
    }

    /**
     * Prepares front-end data
     *
     * @param   array   $messages
     * @return  array
     */
    private function prepareMessages(array $messages) : array
    {
        $results = [];

        foreach ($messages as $message) {
            $results[] = [
                'user_from' => (new User())->find($message->user_from)->getUsername(),
                'user_to'   => (new User())->find($message->user_to)->getUsername(),
                'content'   => $message->content,
                'date'      => $message->date
            ];
        }

        return $results;
    }
}
