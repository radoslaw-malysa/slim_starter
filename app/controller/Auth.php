<?php

    namespace Controller;
    
    use \Slim\Views\PhpRenderer as PhpRenderer;
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    
    class Auth {
        
        private $view;
        private $page;
        
        public function __construct(PhpRenderer $view)
        {
            $this->view = $view;
        }
        
        //natywne zalogowanie do app
        function login(Request $request, Response $response, $params = null)
        {
            //\Model\Session::kill();
            $post_data = $request->getParsedBody();
            $user = \Model\User::get_by_email($post_data['email']);
            
            if (isset($user->id)) //jest uzytkownik z emailem
            {
                if (password_verify((string)$post_data['password'], $user->pass)) {
                    if ($user->state == 1 || $user->state == 0 || $user->state == 4)
                    {
                        $_SESSION['user_id'] = $user->id;
                        $_SESSION['user_name'] = $user->name;
                        $_SESSION['user_group'] = $user->id_group;
                        
                        return $response->withJson(['status'=>'success', 'message'=>'Witaj']);
                    }
                    else if ($user->state == '3') {
                        return $response->withJson(['status'=>'fail', 'message'=>'Konto nie zostało jeszcze potwierdzone. Link aktywacyjny został wysłany w momencie zakładania konta. Sprawdź pocztę.']);
                    }
                    else if ($user->state == 2) {
                        return $response->withJson(['status'=>'fail', 'message'=>'Konto jest zablokowane. W celu odblokowania skontaktuj się z administratorem.']);
                    }
                    else {
                        return $response->withJson(['status'=>'fail', 'message'=>'Status użytkownika nieznany. Skontaktuj się z administratorem.']);
                    }
                }
                else {
                    return $response->withJson(['status'=>'fail', 'message'=>'Hasło nieprawidłowe. Jeśli zapomniałeś, skorzystaj z funkcji "Przypomnij hasło"']);
                }
            } else {
                return $response->withJson(['status'=>'fail', 'message'=>'Brak użytkowika z podanym adresem e-mail.']);
            }
        }
        
        function register(Request $request, Response $response, $params = null)
        {
            $form_data = $request->getParsedBody();
            
            if (!$form_data['email']) {
                return $response->withJson(['status'=>'fail', 'message'=>'Wypełnij pole E-mail']);
            } else if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
                return $response->withJson(['status'=>'fail', 'message'=>'Adres e-mail jest nieprawidłowy.']);
            } else if (!$form_data['password']) {
                return $response->withJson(['status'=>'fail', 'message'=>'Wypełnij hasło']);
            } else if ($form_data['password'] != $form_data['password2']) {
                return $response->withJson(['status'=>'fail', 'message'=>'Hasło i powtórka hasła muszą być jednakowe.']);
            } else if (\Model\Users::email_exists($form_data['email'])) {
                return $response->withJson(['status'=>'fail', 'message'=>'Adres e-mail został już wcześniej zarejestrowany. Skorzystaj z funkcji przypomnienia hasła.']);
            } else {
                //utworz nowego usera
                $user = new \Model\User();
                $user->email = $form_data['email'];
                $user->pass = \Model\Users::password_encode($form_data['password']);
                $user->name = trim($form_data['name']);
                $user->state = 3; //1-aktywny, 2 - zablokowane, 3 - czeka na potwierdzenie
                $user->id_group = 3; //gosc
                $user->save();
                
                if ($user->id) {
                    $user = \Model\User::get_by_id($user->id);
                    $user_hash = $user->get_token();
                    $confirm_url = SITE_URL . '/potwierdzenie/' . $user_hash;
                    
                    //wyslij maila z potwierdzeniem
                    if ($this->send_email([
                        'subject' => $_SERVER['HTTP_HOST'].' - Nowe konto',
                        'content' => '<p>Dzień dobry,</p><p>Miło powitać Cię w gronie użytkowników portalu '.$_SERVER['HTTP_HOST'].'.</p><p>W celu pełnego aktywowania konta kliknij tu: <a href="'.$confirm_url.'">'.$confirm_url.'</a><br /></p>
                        <p></p>
                        <p>Pozdrawiamy serdecznie<br />Zespół '.$_SERVER['HTTP_HOST'].'</p>',
                        'address' => $form_data['email']
                    ])) {
                        return $response->withJson(['status'=>'success', 'message'=>'Konto zostało utworzone. Sprawdź pocztę i potwierdź operację.']);
                    } else {
                        return $response->withJson(['status'=>'success', 'message'=>'Błąd poczty. Nie udało nam się wysłać do Ciebie wiadomości. Spróbuj ponownie za chwilę.']);
                    }
                } else {
                    return $response->withJson(['status'=>'fail', 'message'=>'Nie udało się utowrzyć konta. Spróbuj ponownie.']);
                }
            }
        }
        
        function confirm(Request $request, Response $response, $params = null)
        {
            if ($params['token']) {
                
                $user = \Model\User::get_by_token($params['token']);
                
                if ($user->id) {
                    if ($user->state == 3) {
                        $user->set('state', 1)->save();
                        $message = 'Twoje konto zostało uwierzytelnione.<br />Po zalogowaniu możesz korzystać z rozszerzonej funkcjonalności serwisu.';
                    } else if ($user->state == 1) {
                        $message = 'Twoje konto zostało już wcześniej uwierzytelnione i jest aktywne.<br />Po zalogowaniu możesz korzystać z rozszerzonej funkcjonalności serwisu.';
                    } else if ($user->state == 2) {
                        $message = 'Twoje konto jest zablokowane. Skontaktuj się z nami.';
                    } else {
                        $message = 'Status Twojego konta wymaga wyjaśnienia. Skontaktuj się z nami.';
                    }
                } else {
                    $message = 'Nieprawidłowy token. Spradź czy link jest poprawny i kompletny.';
                }
            } else {
                $message = 'Link aktywacyjny jest nieprawidłowy.';
            }
            
            $this->page = new \Model\Page('potwierdzenie');
            
            $rendered_content = $this->view->fetch(('message/message.phtml'), [
                'content' => (object)[
                    'title' => 'Witaj',
                    'lead' => $message,
                    'content' => '<a href="/" class="btn btn-secondary">Przejdź do strony</a>'
                ]
            ]);
            
            //render page
            return $this->view->render($response, 'index.phtml', [
                'rendered_content' => $rendered_content,
                'menu' => $this->view->fetch('menu/menu.phtml', [
                    'menu' => $this->page->get_menu()
                ]),
                'footer' =>  $this->view->fetch('footer/footer.phtml', [
                    'content' => $this->page->get_footer()
                ]),
                'css' => $this->view->fetch('_css/index.phtml', ['files' => $this->page->get_css()]),
                'js' => $this->view->fetch('_js/index.phtml', ['files' => $this->page->get_js()]),
                'meta' => (new \Model\Meta())->from_page($this->page),
                'route_name' => $request->getAttribute('route')->getName()
            ]);
        }
        
        //panel uzytkownika
        function user_panel(Request $request, Response $response, $params = null)
        {
            $this->page = new \Model\Page($_SERVER['REQUEST_URI']);
            
            if ($_SESSION['user_id'])
            {
                //render standard modules
                foreach ($this->page->get_content() as $section_content) {
                    $rendered_content .= $this->view->fetch(($section_content->name.'/'.$section_content->name.'.phtml'), [
                        'content' => $section_content
                    ]);
                }
            }
            else 
            {
                $rendered_content = $this->view->fetch(('message/message.phtml'), [
                    'content' => (object)[
                        'title' => 'Coś poszło nie tak...',
                        'lead' => 'Nie jesteś zalogowany. Prawdopodobnie Twoja sesja wygasła. Zaloguj się ponownie i powtórz operację.',
                        'content' => '<a href="/" class="logowanie btn btn-secondary">Zaloguj się</a>'
                    ]
                ]);
            }
            /*$this->page = new \Model\Page('ja');
            
            $rendered_content = $this->view->fetch(('user_profile/user_profile.phtml'), [
                'content' => (object)[
                    'title' => 'Moje dane',
                    'lead' => $message,
                    'content' => '<a href="/" class="btn btn-secondary">Przejdź do strony</a>'
                ]
            ]);*/
            
            //render page
            return $this->view->render($response, 'index.phtml', [
                'rendered_content' => $rendered_content,
                'menu' => $this->view->fetch('menu/menu.phtml', [
                    'menu' => $this->page->get_menu()
                ]),
                'footer' =>  $this->view->fetch('footer/footer.phtml', [
                    'content' => $this->page->get_footer()
                ]),
                'css' => $this->view->fetch('_css/index.phtml', ['files' => $this->page->get_css()]),
                'js' => $this->view->fetch('_js/index.phtml', ['files' => $this->page->get_js()]),
                'meta' => (new \Model\Meta())->from_page($this->page),
                'route_name' => $request->getAttribute('route')->getName()
            ]);
        }
        
        function user_panel_save(Request $request, Response $response, $params = null)
        {
            //$method = $request->getMethod();
            if ($request->isPost())
            {
                $form_data = $request->getParsedBody();
                $user = \Model\User::get_by_id($_SESSION['user_id']);
                
                if ($form_data['token'] == $user->get_token()) {
                    
                    if ($form_data['password'] && ($form_data['password'] != $form_data['password2'])) {
                        return $response->withJson(['status'=>'fail', 'message'=>'Hasło i powtórka hasła muszą być jednakowe.']);
                    } else if (!$form_data['name']) {
                        return $response->withJson(['status'=>'fail', 'message'=>'Podaj imię i nazwisko']);
                    } else if (!$form_data['firm']) {
                        return $response->withJson(['status'=>'fail', 'message'=>'Podaj firmę. Jeśli chcesz zarejestrować się jako osoba prywatna wpisz "Osobaa prywatna".']);
                    } else {
                        //$user->email = $post_data['email'];
                        if ($form_data['password'] && ($form_data['password'] == $form_data['password2'])) {
                            $user->pass = \Model\Users::password_encode($form_data['password']);
                        }
                        $user->name = trim($form_data['name']);
                        $user->firm = trim($form_data['firm']);
                        //$user->state = 3; //1-aktywny, 2 - zablokowane, 3 - czeka na potwierdzenie
                        //$user->id_group = 3; //gosc
                        $user->save();
                        return $response->withJson(['status'=>'success', 'message'=>'Twoje dane zostały pomyślnie zaktualizowane.']);
                    }
                } else {
                    
                }
            }
        }
        
        //wyloguj
        public function logout(Request $request, Response $response, $params = null)
        {
            //\Model\Session::kill();
            unset($_SESSION['user_id']);
            return $response->withStatus(301)->withHeader('Location', '/?auth_bye=1');
        }
        
        //skasuj konto
        public function forget(Request $request, Response $response, $params = null)
        {
            $user = \Model\User::get_by_id($_SESSION['user_id']);
            
            if ($user->id) {
                $user->forget();
                
                $content = (object)[
                    'title'=>'Dziękujemy, do zobaczenia...',
                    'lead'=>'Twoje konto zostało usunięte z systemu.',
                    'content'=>''
                ];
            } else {
                $content = (object)[
                    'title'=>'Coś poszło nie tak...',
                    'lead'=>'Procedura zamknięcia konta nie powiodła się.',
                    'content'=>'Zaloguj się jeszcze raz i spróbuj ponownie.'
                ];
            }
            
            //\Model\Session::kill();
            unset($_SESSION['user_id']);
            
            $this->page = new \Model\Page('auth/zapomnij');
            
            $rendered_content = $this->view->fetch(('message/message.phtml'), [
                'content' => $content
            ]);
            
            //render page
            return $this->view->render($response, 'index.phtml', [
                'rendered_content' => $rendered_content,
                'menu' => $this->view->fetch('menu/menu.phtml', [
                    'menu' => $this->page->get_menu()
                ]),
                'footer' =>  $this->view->fetch('footer/footer.phtml', [
                    'content' => $this->page->get_footer()
                ]),
                'css' => $this->view->fetch('_css/index.phtml', ['files' => $this->page->get_css()]),
                'js' => $this->view->fetch('_js/index.phtml', ['files' => $this->page->get_js()]),
                'meta' => (new \Model\Meta())->from_page($this->page),
                'route_name' => $request->getAttribute('route')->getName()
            ]);
        }
        
        private function send_email($params=[])
        {
            include('../vendor/phpmailer/class.phpmailer.php');
            //require(__DIR__.'/../../vendor/phpmailer/class.pop3.php');
            require('../vendor/phpmailer/class.smtp.php');
            
            $mail = new \PHPMailer(true);
            $mail->IsSMTP();
            $mail->IsHTML(true);
            
            try {
              $mail->Host       = MAIL_WWW_HOST; 
              //$mail->SMTPDebug  = 2; 
              $mail->SMTPAuth   = true;
              //$mail->Port       = 587;
              $mail->Username   = MAIL_WWW;
              $mail->Password   = MAIL_WWW_PASSWORD;
              //$mail->AddReplyTo('name@yourdomain.com', 'First Last');
              $mail->AddAddress($params['address'], '');
              $mail->SetFrom(MAIL_WWW, MAIL_WWW_NAME);
              $mail->AddReplyTo(MAIL_WWW, MAIL_WWW_NAME);
              
              $mail->CharSet ="utf-8";
              $mail->Subject = $params['subject'];
              //$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; 
              //$mail->MsgHTML($params['content']);
              $mail->Body = $params['content'];
              //$mail->AddAttachment('images/phpmailer.gif');      // attachment
              //$mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
              
              if ($mail->Send()) {
                return true;
              } else {
                return false;
              }
              
            } catch (phpmailerException $e) {
              return FALSE;
              //echo $e->errorMessage(); //Pretty error messages from PHPMailer
            } catch (Exception $e) {
              return FALSE;
              //echo $e->getMessage(); //Boring error messages from anything else!
            }
        }
        
        
    }