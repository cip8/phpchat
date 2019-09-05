<?php

namespace App\Repository;

use PDO;
use PDOException;

class Database {

    private $db;
    private $statement;

    public function __construct()
    {
        $dsn = getenv('DB_CONNECTION') . ':' . getenv('DB_NAME');

        // Creates PDO instance
        try {
            $this->db = new PDO($dsn);
        } catch(PDOException $e){
            var_dump($e->getMessage()); // 'Log' error
            throw new PDOException('Configuration error!');
        }
    }

    /**
     * Prepares PDO statement
     *
     * @param   string  $sql
     * @return  void
     */
    public function query(string $sql) 
    {
        $this->statement = $this->db->prepare($sql);
    }

    /**
     * Binds values to query statement
     *
     * @param   string  $param
     * @param   mixed   $value
     * @return  void
     */
    public function bind(string $param, $value)
    {
        $this->statement->bindValue($param, $value);
    }

    /**
     * Executes PDO statement
     *
     * @return void
     */
    public function execute()
    {
        return $this->statement->execute();
    }

    /**
     * Retrieves a single record
     *
     * @return Object
     */
    public function single() : Object
    {
        $this->execute();

        return $this->statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Retrieves multiple records
     *
     * @return array
     */
    public function results() : array
    {
        $this->execute();

        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Returns the total count of current result set
     *
     * @return int
     */
    public function rowCount() : int
    {
        return count($this->results());
    }

    /**
     * Checks if current result set has items
     *
     * @return bool
     */
    public function hasResults() : bool
    {
        return $this->rowCount() > 0 
            ? true
            : false;
    }    
}
