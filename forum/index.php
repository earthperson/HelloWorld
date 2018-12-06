<?php
// задаем время жизни для файлов cookie данного сеанса
session_set_cookie_params(0);
// инициализировать сессию
session_start();
// начать буферизацию вывода
ob_start();
// подключаем необходимые файлы
require_once('engine/classes.php');
require_once('engine/config.php');
require_once('engine/lib/common.php');
require_once('engine/lib/topic.php');
require_once('engine/lib/request.php');
require_once('engine/lib/response.php');

cache0();

// Создаем экземпляр класса для вывода сообщения об ошибках
$el = new ErrorLog('logs/');
// Создаем экземпляр класса для работы с БД
$db = new DatabaseCommon('logs/', DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
?>
<html>
<head>
<title>Форум</title>
<meta http-equiv="content-type" content="text/html; charset=windows-1251" />
<style type="text/css">
	@import "css/styles.css";
</style>
</head>
<body>
<?php
// вывод сообщений об ошибках
print($el->showlog());
?>
<h3>Форум</h3>
<?php
// +-------------------------------------------------------------------------------------------+
// | Блок регистрации                                                                          |
// +-------------------------------------------------------------------------------------------+
if(isset($_REQUEST['registrationForm'])) {
	if(!str_empty($_REQUEST['login']) && !str_empty($_REQUEST['passwd']) && !str_empty($_REQUEST['name'])) {
		// поиск записей в таблице registration на предмет совпадения логина
		if(registrationConcurrence($_REQUEST['login'], $db, $el)) {
			// если есть совпадения
			$el->mylog("Логин уже зарегистрирован!\r\n");
		}
		else {
			// если совпадений нет то создаем новую запись
			if(registrationWrite($_REQUEST['login'], $_REQUEST['passwd'], $_REQUEST['name'], $db)) {
				$el->mysuccess("Успешная регистрация!\r\n");
			}
			else {
				$el->mylog("Ошибка при записи регистрационных данных!\r\n");
			}
		}
	}
}
// +-------------------------------------------------------------------------------------------+
// | Блок авторизации                                                                          |
// +-------------------------------------------------------------------------------------------+
if(isset($_REQUEST['authorisationForm'])) {
	if(!str_empty($_REQUEST['login']) && !str_empty($_REQUEST['passwd'])) {
		if(!(($_REQUEST['login'] == ADMIN_LOGIN) && ($_REQUEST['passwd'] == ADMIN_PASSWD))) {
			// поиск записей в таблице registration на предмет совпадения логина и пароля
			// если есть совпадения сообщаем об успешной авторизации, добавляем значение переменной сессии
			if(authorisationConcurrence($_REQUEST['login'], $_REQUEST['passwd'], $db, $el)) {
				$el->mysuccess("Успешная авторизация!\r\n");
			}
			else {
				$el->mylog("Неправильный логин или пароль!\r\n");
			}
		}
		else {
			$_SESSION['admin'] = 'admin';
		}
	}
}
// +-------------------------------------------------------------------------------------------+
// | Блок уничтожения сессии                                                                   |
// +-------------------------------------------------------------------------------------------+
if(isset($_REQUEST['sess']) && isset($_REQUEST['sess']) == 'destroy') {
	// удалить все переменные сессии
	session_unset();
	// разрушить сессию
	session_destroy();
}
// +-------------------------------------------------------------------------------------------+

if(!(isset($_SESSION['username']) || isset($_SESSION['admin']))) {
	if(isset($_REQUEST['registration']) && $_REQUEST['registration'] == 'show') {
		print authorisation_form(0);
		print registration_form();
	}
	else {
		print authorisation_form(1);
	}
}
else if(isset($_SESSION['username']) && !isset($_SESSION['admin'])){
	print '<div style="padding: 4px; color: navy;"><b>Привет, ' . ucfirst($_SESSION['username']) . '</b>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?sess=destroy">Выход</a></div>' . "\n";
}
else {
	print '<div style="padding: 4px; color: navy;"><b>Привет, ' . ucfirst($_SESSION['admin']) . '</b>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?sess=destroy">Выход</a></div>' . "\n";
}
if(!$db->dbConnectError) {
	// инсталляция таблиц
	if(tables_install($db)) {
		// +-------------------------------------------------------------------------------------------+
		// | Блок тем                                                                                  |
		// +-------------------------------------------------------------------------------------------+
		if(!isset($_REQUEST['topicID']) && !isset($_REQUEST['requestID'])) {
			// если нужно добавить тему
			if(isset($_REQUEST['topicSbmt'])) {
				if(!topicWrite($_REQUEST['poster'], $_REQUEST['topicTitle'], $_REQUEST['requestTitle'], $_REQUEST['requestBody'], $db) and !isset($_GET['err'])) {
					$el->mylog("Ошибка при записи темы!\r\n", '&err=1');
				}
			}
			// если нужно удалить тему
			if(isset($_REQUEST['topicDel'])) {
				if(!topicDel($_REQUEST['topicDel'], $db) and !isset($_GET['err'])) {
					$el->mylog("Ошибка при удалении темы!\r\n", '&err=1');
				}
			}
			// показать перечень тем форума
			topicLine();
			if(!showTopics($db) and !isset($_GET['err'])) {
				$el->mylog("Ошибка при запросе перечня тем форума!\r\n", '&err=1');
			}
			// показать форму для добавления новой темы
			if(isset($_SESSION['username']) && !isset($_SESSION['admin'])) {
				topicForm($_SESSION['username']);
			}
			else if(isset($_SESSION['admin'])) {
				topicForm($_SESSION['admin']);
			}
		}
		// +-------------------------------------------------------------------------------------------+
		// | Блок вопросов                                                                             |
		// +-------------------------------------------------------------------------------------------+
		else if(!isset($_REQUEST['requestID'])) {
			// если нужно добавить новый вопрос в теме
			if(isset($_REQUEST['requestSbmt'])) {
				if(!requestWrite($_REQUEST['poster'], $_REQUEST['requestParent'], $_REQUEST['requestTitle'], $_REQUEST['requestBody'], $db) and !isset($_GET['err'])) {
					$el->mylog("Ошибка при записи темы!\r\n", '&err=1');
				}
			}
			// если нужно удалить вопрос
			if(isset($_REQUEST['requestDel'])) {
				if(!requestDel($_REQUEST['topicID'], $_REQUEST['requestDel'], $db) and !isset($_GET['err'])) {
					$el->mylog("Ошибка при удалении вопроса!\r\n", '&err=1');
				}
			}
			requestLine();
			// показать перечень заголовков вопросов темы
			if(!showRequest($_REQUEST['topicID'], $db) and !isset($_GET['err'])) {
				$el->mylog("Ошибка при запросе перечня заголовков вопросов темы!\r\n", '&err=1');
			}
			// показать форму для добавления нового вопроса темы
			if(isset($_SESSION['username']) && !isset($_SESSION['admin'])) {
				requestForm($_REQUEST['topicID'], $_SESSION['username']);
			}
			else if(isset($_SESSION['admin'])) {
				requestForm($_REQUEST['topicID'], $_SESSION['admin']);
			}
		}
		// +-------------------------------------------------------------------------------------------+
		// | Блок ответов                                                                              |
		// +-------------------------------------------------------------------------------------------+
		else {
			// если нужно добавить новый ответ
			if(isset($_REQUEST['responseSbmt'])) {
				if(!responseWrite($_REQUEST['poster'], $_REQUEST['topicID'], $_REQUEST['responseParent'], $_REQUEST['responseBody'], $db) and !isset($_GET['err'])) {
					$el->mylog("Ошибка при записи ответа!\r\n", '&err=1');
				}
			}
			// если нужно удалить ответ
			if(isset($_REQUEST['responseDel'])) {
				if(!responseDel($_REQUEST['topicID'], $_REQUEST['requestID'], $_REQUEST['responseDel'], $db) and !isset($_GET['err'])) {
					$el->mylog("Ошибка при удалении ответа!\r\n", '&err=1');
				}
			}
			responseLine1($_REQUEST['topicID']);
			// показать тело вопроса темы
			if(!showRequestBody($_REQUEST['requestID'], $db) and !isset($_GET['err'])) {
				$el->mylog("Ошибка при запросе тела вопроса темы!\r\n", '&err=1');
			}
			responseLine2();
			// показать перечень ответов
			if(!showResponse($_REQUEST['topicID'], $_REQUEST['requestID'], $db) and !isset($_GET['err'])) {
				$el->mylog("Ошибка при запросе тела вопроса темы!\r\n", '&err=1');
			}
			// показать форму для добавления ответа
			if(isset($_SESSION['username']) && !isset($_SESSION['admin'])) {
				responseForm($_REQUEST['topicID'], $_REQUEST['requestID'], $_SESSION['username']);
			}
			else if(isset($_SESSION['admin'])) {
				responseForm($_REQUEST['topicID'], $_REQUEST['requestID'], $_SESSION['admin']);
			}
		}
	}
	else {
		$db->dbErrors();
	}
	$db->dbClose();
}
else {
	$db->dbErrors();
}
?>
</body>
</html>
<?php
// отправляем содержимое буфера вывода и выключаем буферизацию вывода
ob_end_flush();
?>