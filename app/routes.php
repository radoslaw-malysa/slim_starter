<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    
    //rest api
    $app->get('/api/rdf', 'Page:rdf');
    $app->get('/api/rdf/item[/{id}]', 'Page:rdf');
    $app->get('/api/rdf/find[/{q}]', 'Page:rdf');
    
    $app->get('/api/atom', 'Page:atom');
    $app->get('/api/atom/item[/{id}]', 'Page:atom');
    $app->get('/api/atom/find[/{q}]', 'Page:atom');
    
    //adm
    $app->get('/adm', 'Cms:adm'); //ekran logowania
    $app->post('/adm/in', 'Cms:login'); //zalogowanie
    $app->any('/adm/out', 'Cms:logout'); //wylogowanie
    
    //cms
    $app->get('/assets_compile', 'Cms:assets_compile'); //kompilacja css/js (wrzucic tylko dla zalogowanego admina)
    
    if ($_SESSION['user_id']) {
        $app->post('/cms/up', 'Cms:upload'); //upload plikow
        $app->get('/cms/render_content/{id}[/{id_module}]', 'Cms:render_content'); //renderuje content
        
        $app->get('/cms/{table}', 'Cms:get_table'); //GET widok tabeli
        $app->get('/cms/{table}/{id}[/{field}]', 'Cms:get_record'); //GET edycja jednego pola {field} lub calego rekordu {id} z {table}
        
        $app->post('/cms/save-source/{id}', 'Cms:post_source'); //wyjatek dla forma w modalu w ed_items
        $app->post('/cms/{table}[/{id}[/{field}]]', 'Cms:post_record'); //POST zapis pola {field} lub rekordu {id} do {table}
        
        //dev
        $app->get('/exe/{command}', 'Cms:run'); 
    }
    
    
    //auth
    $app->post('/auth/login', 'Auth:login'); //zalogowanie usra
    $app->get('/auth/wyloguj', 'Auth:logout'); //zalogowanie usra
    $app->get('/auth/zapomnij', 'Auth:forget'); //skasowanie konta
    $app->post('/auth/update', 'Auth:user_panel_save');
    $app->post('/auth/register', 'Auth:register'); //zalozenie nowego konta
    $app->get('/potwierdzenie/{token}', 'Auth:confirm'); //potwierdzenie
    $app->any('/ja', 'Auth:user_panel')->setName('ja'); //panel uzytkownika
    
    //bdz
    $app->get('/item/{id_item}', 'Page:item_page')->setName('item-persistent')->add($from_cache); //strona eksponatu
    $app->get('/bdz/download/preview/{token}', 'Page:download_source_preview'); //pobieranie plikow niskiej rozdz
    $app->get('/bdz/download/{token}', 'Page:download_source'); //pobieranie plikow niskiej rozdz
    $app->get('/error/{error_code}', 'Page:error_page');
    
    $app->get('/bdz/szukaj[/{page}]', 'Page:items_page')->setName('items_szukaj'); //szukaj
    $app->get('/bdz/nowosci', 'Page:items_page')->setName('items_news')->add($from_cache); //najnowsze
    $app->get('/bdz/ciekawostki', 'Page:items_page')->setName('items_rare')->add($from_cache); //ciekawe
    
    $app->get('/bdz/{tag}', 'Page:items_subject')->setName('items_subject')->add($from_cache); //eksponaty wg tematu
    $app->get('/bdz/temat/{tag}', 'Page:items_subject')->setName('items_subject')->add($from_cache); //eksponaty wg tematu DO LIKWIDACJI
    $app->get('/bdz/typ/{tag}', 'Page:items_type')->setName('items_type')->add($from_cache); //eksponaty wg typu
    $app->get('/bdz/czas/{tag}', 'Page:items_dates')->setName('items_dates')->add($from_cache); //eksponaty wg czasu
    
    $app->get('/bdz/{title}/{id_item}/{page_no}', 'Page:item_page')->setName('item_page')->add($from_cache); //podstrona eksponatu gdy kilka skanów
    $app->get('/bdz/{title}/{id_item}', 'Page:item_page')->setName('item')->add($from_cache); //strona eksponatu
    
    //strona
    $app->get('/ping', 'Page:ping'); //dynamiczne elementy na stronie
    //$app->get('/{category}/{subcategory}/{page_link}', 'Page:get_page')->add($from_cache); //podstrona
    //$app->get('/{category}/{page_link}', 'Page:get_page')->add($from_cache); //podstrona
    $app->get('/{page_link}', 'Page:home_page')->add($from_cache); //podstrona
    $app->get('/', 'Page:home_page')->add($from_cache); //home
