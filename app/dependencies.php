<?php
    $container = $app->getContainer();
    
    //log
    $container['logger'] = function($c) {
        $logger = new \Monolog\Logger('app_logger');
        $file_handler = new \Monolog\Handler\StreamHandler("../log/app.log");
        $logger->pushHandler($file_handler);
        return $logger; 
    };
    
    //db
    /*$container['db'] = function ($c) {
        return \Db::getInstance()->getConnection();
    };*/
    
    //php render
    $container['view'] = new \Slim\Views\PhpRenderer(VIEW_PATH);
    
    //http cache
    $container['cache'] = function () {
        return new \Slim\HttpCache\CacheProvider();
    };
    
    //filecache 
    $container['sitecache'] = function() {
        return \Model\CacheFile::getInstance();
    };
    
    //filecache middleware
    $from_cache = function ($request, $response, $next) {
        
        $response = $next($request, $response);
        
        if ($response->getStatusCode() == 200 && CACHE_ON) {
            $filecache = $this->get('sitecache');
            $filecache->set($_SERVER['REQUEST_URI'], $response->getBody());
        }
        
        return $response;
    };
    
    //error 404
    $container['notFoundHandler'] = function ($c) { 
        return function ($request, $response) use ($c) {
            return (new Controller\Error($c->get('view'), $c->get('logger')))->error404($request, $response);
        };
    };
    
    //rejestracja kontrolerów
    $container['Page'] = function($c) {
        return new Controller\Page($c->get('view'));
    };
    
    //auth
    $container['Auth'] = function($c) {
        return new Controller\Auth($c->get('view'));
    };
    
    //cms
    $container['Cms'] = function($c) {
        return new Controller\Cms($c->get('view'));
    };