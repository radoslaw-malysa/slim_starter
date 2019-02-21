<?php

    namespace Controller;
    
    use \Slim\Views\PhpRenderer as PhpRenderer;
    use \Psr\Log\LoggerInterface;
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    
    
    class Error {
        
        public function __construct(PhpRenderer $view, LoggerInterface $logger)
        {
            $this->view = $view;
            $this->logger = $logger;
        }
        
        public function error404(Request $request, Response $response, $params = null)
        {
            $destination_image = ltrim($request->getUri()->getPath(), '/'); // /img/preview/katalog/test.jpg
            
            if (strpos($destination_image, MIN) !== false) //bdz miniatury
            {
                $source_image = str_replace(MIN, PREVIEW, $destination_image);
                
                if (file_exists($source_image)) {
                    $this->create_image($source_image, $destination_image, MIN_SIZE, MIN_SIZE, 'landscape', false);
                } else {
                    $this->logger->error("[404] Brak obrazka zrodlowego: ".$source_image);
                    $response->write(file_get_contents('images/error404.jpg'));
                    return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
                }
            }
            
            if (file_exists($destination_image)) {
                $response->write(file_get_contents($destination_image));
                return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
            } else {
                $this->logger->error("[404] Brak obrazka docelowego: ".$destination_image);
                $response->write(file_get_contents('images/error404.jpg'));
                return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
            }
        }
        
        private function create_image($source_image, $destination_image, $width, $height, $resize_method='auto', $watermark=false)
        {
            $destination_dir = pathinfo($destination_image, PATHINFO_DIRNAME);
            \Model\Tools::makedirs($destination_dir);
            
            $magicianObj = new \Magician\imageLib($source_image);
            
            if ($resize_method == 'crop-landscape-only') { 
                list($src_width, $src_height, $src_type, $src_attr) = getimagesize($source_image);
                if ($src_width < $src_height) {
                    $magicianObj -> resizeImage($width, $height, 'auto');
                } else {
                    $magicianObj -> resizeImage($width, $height, 'crop-auto');
                }
            } else if ($resize_method) { 
                $magicianObj -> resizeImage($width, $height, $resize_method);
            } else { 
                $magicianObj -> resizeImage($width, $height, 'crop-auto');
            }
            
            //znak wodny
            if ($watermark && WATERMARK) {
                $magicianObj->addWatermark(WATERMARK,'tr','20');
            }
            
            $magicianObj -> saveImage($destination_image, 80);
        }
        
        /*public function _error404(Request $request, Response $response, $params = null)
        {
            $url = $request->getUri()->getPath();
            $url_info = pathinfo($url);
            $url_dest = trim($url_info['dirname'], '/'); //sciezka miniatury
            $dir_parts = explode('/', $url_dest);
            
            //obrazki minitury
            if ($dir_parts[0].'/'.$dir_parts[0].'/' == PREVIEW) //bdz
            {
                $dir_dest = $dir_parts[count($dir_parts)-1];
                
                
            }
            else if ($dir_parts[0] == 'img') //cms
            {
                
                $dir_dest = $dir_parts[count($dir_parts)-1]; //(min_300x300)
                
                if (substr($dir_dest, 0, 3) == 'min') {
                    $source_image =  str_replace($dir_dest, '', $url_dest).$url_info['basename']; //(img/test.jpg)
                    $dest_image = ltrim($url, '/'); //(img/min_300x300/test.jpg)
                    
                    if (file_exists($source_image)) {
                        list($nic, $wymiary) = explode('_', $dir_dest);
                        
                        if (strpos($wymiary, 'x') !== false) {
                            list($width, $height) = explode('x', $wymiary);
                        }
                        
                        if ($width || $height) {
                            \Model\Tools::makedirs($url_dest); //katalog na miniatury
                            $magicianObj = new \Magician\imageLib($source_image);
                            $magicianObj -> resizeImage($width, $height, 'crop-auto');
                            $magicianObj -> saveImage($dest_image, 80);
                        } else {
                            $this->logger->error("[404] Nieprawidlowa nazwa katalogu na miniatury (min_SZERxWYS): ".$request->getUri()->getPath());
                        }
                    }
                    else {
                        //trzeba zrobic
                        //$dest_image = odpowiednia zaslepka dla okreslonej miniatury
                        //$response->write(file_get_contents($dest_image));
                        //return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
                        $this->logger->error("[404] nie moge utworzyc miniatury, brak obrazka zrodlowego: ".$request->getUri()->getPath());
                    }
                    
                    if (file_exists($dest_image)) {
                        $response->write(file_get_contents($dest_image));
                        return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
                    } else {
                        $this->logger->error("[404] [thumb] Brak miniatury brak zaslepki: ".$request->getUri()->getPath());
                        //$response->write(file_get_contents('images/error404.jpg'));
                        //return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE)->withStatus(404);
                        return $response->withStatus(404)->withHeader('Content-Type', 'text/html')->write('not found');
                    }
                }
                else //inne obrazki
                {
                    $dest_image = ltrim($url, '/');
                    if (file_exists($dest_image)) {
                        $response->write(file_get_contents($dest_image));
                        return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
                    } else {
                        $this->logger->error("[404] [thumb] Brak miniatury brak zaslepki: ".$request->getUri()->getPath());
                        return $response->withStatus(404)->withHeader('Content-Type', 'text/html')->write('not found');
                    }
                }
            }
            else //strony inne pliki
            {
                $this->logger->error("[404] ".$request->getUri()->getPath()." -. ".$url);
                $content = 'oj niedobrze 404'; 
                
                return $this->view->render($response, '_error_404/index.phtml', [
                    'content' => $content
                ]); //->withStatus(404);
            }
        }*/
    }