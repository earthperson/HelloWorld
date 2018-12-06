<?php
//date_default_timezone_set('Europe/Moscow');
putenv("TZ=Europe/Moscow");

error_reporting(E_ALL);

$CONFIG = array(
    'general' => array(
        'charset'       => 'utf-8'
    ),
    'db' => array(
        'host'          => 'localhost',
        'login'         => '',
        'password'      => '',
        'database'      => '',
    	'charset'       => 'utf8'
    )
);
// omit close tag
