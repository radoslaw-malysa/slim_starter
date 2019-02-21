<?php
    $config['displayErrorDetails'] = true;
    $config['addContentLengthHeader'] = false;
    
    define('SITE_URL', (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]");
    define('SITE_NAME', 'Muzeum Wsi Radomskiej');

    define('ITEMS_ON_PAGE', 36); //ilosc itemow w wynikach, paginacja
    
    /*db*/
    define('DB_HOST', 'localhost');
    define('DB_USER', '28860266_mwr');
    define('DB_PASS', 'dqonby@NDW221');
    define('DB_BASE', '28860266_mwr');
    //define('DB_CHARSET', 'utf8');
    //define('DB_COLLATION', 'utf8_polish_ci');
    
    define('PREFIX', 'bdz_');
    define('SUFFIX', '');
    
    //img
    define('WATERMARK', 'images/watermark_white.png');
    define('IMG', 'img/');
    define('SOURCE', 'img/dygitalizacja/');
    define('PREVIEW', 'img/preview/');
    define('PREVIEW_SIZE', '1080');
    define('MIN', 'img/min/');
    define('MIN_SIZE', '320');
    
    /*cms tabs*/
    define('T_CONTENT', PREFIX.'content'.SUFFIX);
    define('T_RESOURCES', PREFIX.'resources'.SUFFIX);
    define('T_PAGES', PREFIX.'pages'.SUFFIX);
    define('T_MODULES', PREFIX.'modules'.SUFFIX);
    define('T_USERS', PREFIX.'users'.SUFFIX);
    /*bdz tas*/
    define('T_ITEMS', PREFIX.'items'.SUFFIX);
    define('T_SOURCES', PREFIX.'sources'.SUFFIX);
    define('T_PROPERTIES', PREFIX.'properties'.SUFFIX);
    define('T_AGES', PREFIX.'ages'.SUFFIX);
    define('T_SUBJECT', PREFIX.'subject'.SUFFIX);
    define('T_ITEM_SUBJECT', PREFIX.'item_subject'.SUFFIX);
    define('T_TYPE', PREFIX.'type'.SUFFIX);
    define('T_FORMAT', PREFIX.'format'.SUFFIX);
    define('T_PUBLISHER', PREFIX.'publisher'.SUFFIX);
    define('T_RIGHTS', PREFIX.'rights'.SUFFIX);
    
    /*dev*/
    define('VIEW_PATH', 'app/view/'); //sciezka do templatek
    define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
    
    /*email*/
    define('MAIL_WWW_HOST', 'pawelec.info');
    define('MAIL_WWW', 'rm@pawelec.info');
    define('MAIL_WWW_PASSWORD', '1qaz@WSX');
    define('MAIL_WWW_NAME', 'Poczta '.$_SERVER['HTTP_HOST']);
    
    /*meta*/
    define('SITE_NAME', 'Muzeum Wsi Radomskiej BDZ');
    define('FB_APP_ID', '1870518046542021');
    define('CREATOR', 'IMD');
    define('DEFAULT_TITLE', 'Bazwa Danych Zbiorów Muzeum Wsi Radomskiej');
    define('DEFAULT_DESCRIPTION', 'Cyfrowa kolekcja unikalnych fotografii, dokumentów, wywiadów ze zbiorów Muzeum Wsi Radomskiej');
    define('DEFAULT_KEYWORDS', 'muzeum, wieś, radomskie, skansen, zbiory muzealne, baza danych, eksponaty');
    