<?php

namespace App\Models;

class User extends BaseModel {

    protected $table;
    protected $id;
    protected $username;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->pluralizeClassName();
    }

    /**
     * Finds db user by its username
     *
     * @param   string  $username
     * @return  mixed   [self/null]
     */
    public function findByUsername(string $username)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE username = :username");
        $this->db->bind(':username', $username);

        if (!$this->db->hasResults()) {
            return null;
        }

        $user           = $this->db->single();
        $this->id       = $user->id;
        $this->username = $user->username;

        return $this;
    }

    /**
     * Finds db user by its ID#
     *
     * @param   int     $id
     * @return  mixed   [self/null]
     */
    public function find(int $id)
    {
        $user = $this->findById($id);

        if (!$user) {
            return null;
        }

        $this->id       = $user->id;
        $this->username = $user->username;

        return $this;
    }

    /**
     * Getter / user id
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Getter / username
     *
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }
}
