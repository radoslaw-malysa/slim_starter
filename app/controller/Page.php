<?php

    namespace Controller;
    
    use \Slim\Views\PhpRenderer as PhpRenderer;
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    
    class Page {
        
        private $view;
        private $page;
        
        public function __construct(PhpRenderer $view)
        {
            $this->view = $view;
        }
        
        //strona glowna
        public function home_page(Request $request, Response $response, $args)
        {
            
            
            //render page
            return $this->view->render($response, 'index.phtml', []);
        }
        
    }