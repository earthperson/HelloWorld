<?php
define('TITLE', '�������� �������� ���������� �������');
date_default_timezone_set('Europe/Moscow');
/* pages */
$listPages = array(
	'index.php'      => '��������',
	'main.php'       => '�������',
	
	'releases.php'      => '������',
	'code.php'          => '������ � �������',
	//'map.php'           => '������������ � ������',
	//'miscellaneous.php' => '������',
    'contacts.php'      => '��������',
    
//	'reserve2.php'   => '���������2'
);

/**
* ��������� �����������������
*/
define('ADMIN_NAME','admin_entry');		                            // ��� ��� ������� � �������� �����������������
define('ADMIN_PASSWD','e92ce22705068278b4a192290ca0ff88');	    // ������ ��� ������� � �������� �����������������

/**
 * ���� � ����� � ����������� �� �������
 */
define('LOG_FILE', '../logs/myerrors.log');

/**
 * Host
 */
define('HOST', 'http://www.sweb.ru/');
?>