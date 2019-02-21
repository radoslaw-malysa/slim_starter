<?php

class Db
{
    private $_connection;
    private static $_instance; 
    private $_host = DB_HOST;
    private $_username = DB_USER;
    private $_password = DB_PASS;
    private $_database = DB_BASE;
    
    public static function getInstance()
    {
        if (self::$_instance === null) { 
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct()
    {
        try {
            $conn  = new \PDO("mysql:host=$this->_host;dbname=$this->_database", $this->_username, $this->_password);
            $conn->query('SET NAMES utf8');
            $conn->query('SET CHARACTER_SET utf8_polish_ci');
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            
            $this->_connection = $conn;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    private function __clone()
    {
    }
    
    public function getConnection()
    {
        return $this->_connection;
    }
}