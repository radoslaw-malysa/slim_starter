<?php
    //autoloader (refresh cache: composer dump-autoload)
    require 'vendor/autoload.php';
    
    //config apki
    require 'config.php';
    
    //db
    require 'Db.php';
    
    $app = new \Slim\App(["settings" => $config]);
    
    //http cache (reszta w DI)
    //$app->add(new \Slim\HttpCache\Cache('public', 86400));
    
    //DI
    require 'dependencies.php';
    
    //router
    require 'routes.php';