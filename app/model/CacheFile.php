<?php
namespace Model;

class CacheFile
{
    //private $_connection;
    private static $_instance;
    private $cache_dir;
    //private $key_prefix;
    
    public static function getInstance()
    {
        if (self::$_instance === null) { 
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct()
    {
        $this->cache_dir = 'cache/';
        //$this->key_prefix = '';
    }
    
    private function __clone()
    {
    }
    
    function getFilePath($key)
    {
        return $this->cache_dir . md5($key);
    }
    
    public function get($key)
    {
        return @file_get_contents($this->getFilePath($key));
    }
    
    public function set($key, $value, $expiration=null)
    {
        $file = $this->getFilePath($key);
        $tmp_file = $file . uniqid('', true);
        
        file_put_contents($tmp_file, $value, LOCK_EX);
        rename($tmp_file, $file);
    }
    
    public function touch($key, $expiration)
    {
        
    }
    
    public function delete($key)
    {
        @\unlink($this->getFilePath($key));
    }
}
