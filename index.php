<?php
    ini_set('display_errors',1);
    error_reporting(E_ERROR | E_WARNING | E_PARSE); //E_ALL
    
    //$time_start = microtime(true);
    
    session_start();
    
    define('CACHE_ON', false);
    define('MODE', 'dev'); // dev/prod
    
    if (CACHE_ON) {
        require 'app/model/CacheFile.php';
        $cached = \Model\CacheFile::getInstance()->get($_SERVER['REQUEST_URI']);
    }
    
    if ($cached) {
        die($cached);
    } else {
        //ob_start("ob_gzhandler");
        require 'app/app.php';
        $app->run();
        //ob_end_flush();
    }