<?php
define('TITLE', 'Домашняя страница Пономарева Дмитрия');
date_default_timezone_set('Europe/Moscow');
/* pages */
$listPages = array(
	'index.php'      => 'Заставка',
	'main.php'       => 'Главная',
	
	'releases.php'      => 'Релизы',
	'code.php'          => 'Классы и функции',
	//'map.php'           => 'Эксперименты с картой',
	//'miscellaneous.php' => 'Разное',
    'contacts.php'      => 'Контакты',
    
//	'reserve2.php'   => 'Резервная2'
);

/**
* Настройки администрирования
*/
define('ADMIN_NAME','admin_entry');		                            // Имя для доступа к странице администрирования
define('ADMIN_PASSWD','e92ce22705068278b4a192290ca0ff88');	    // Пароль для доступа к странице администрирования

/**
 * Путь к файлу с сообщениями об ошибках
 */
define('LOG_FILE', '../logs/myerrors.log');

/**
 * Host
 */
define('HOST', 'http://www.sweb.ru/');
?>