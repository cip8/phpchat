<?php

namespace App\Models;

use App\Repository\Database;

use Symfony\Component\Inflector\Inflector;

abstract class BaseModel {

    protected $db;
    protected $table;
    protected $id;

    public function __construct()
    {
        $this->db = new Database;
    }

    /**
     * Finds resource by ID#
     *
     * @param   int     $id
     * @return  Object
     */
    public function findById(int $id) : Object
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    /**
     * @return Object
     */
    public function all() : Object
    {
        $this->db->query("SELECT * FROM {$this->table}");
        
        return $this->db->results();
    }

    /**
     * Returns the plural of the current / extended class name
     *
     * @return string
     */
    protected function pluralizeClassName() : string
    {
        return Inflector::pluralize(
            strtolower(
                (new \ReflectionClass($this))->getShortName()
            )
        );
    }

}