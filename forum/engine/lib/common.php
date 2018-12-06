<?php
/**
 * Отправка заголовков для предотвращения кэширования
 *  
 * @return null;
 * @copyright Взято из "PHP5 библиотека профессионала" стр. 778
 */
function cache0() {
	// Last-Modified - Дата последнего изменения содержимого. Поле актуально только для
	// статических страниц. Apache заменяет это поле значением поля Date для динамически
	// генерируемых страниц, в том числе для страниц содержащих SSI.
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified

	// Expires - Задает дату истечения срока годности документа. Задание ее в прошлом
	// определяет запрет кэш для данной страницы.
	header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");// Date in the past

	// HTTP/1.1
	// Cache-control: no-cache - Управление кэш. Значение no-cache определяет запрет кэш
	// данной страницы. Для версии протокола HTTP/1.0 действует "Pragma: no-cache".
	header("Cache-Control: no-store, no-cache, must-revalidate ");
	header("Cache-Control: post-check=0, pre-check=0", false);

	// HTTP/1.0
	header("Pragma: no-cache");
	return null;
}
function tables_install($db) {
	/*if(!$db->dbQuery('
	CREATE TABLE IF NOT EXISTS topic (
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	title VARCHAR(255) NOT NULL DEFAULT \'\',
	poster VARCHAR(255) NOT NULL DEFAULT \'\',
	created DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	PRIMARY KEY(id)
	) TYPE=MyISAM')) {
	return false;
	}
	if(!$db->dbQuery('
	CREATE TABLE IF NOT EXISTS request (
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	parent INT(11) UNSIGNED,
	title VARCHAR(255) NOT NULL DEFAULT \'\',
	poster VARCHAR(255) NOT NULL DEFAULT \'\',
	created DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	body BLOB,
	PRIMARY KEY(id)
	) TYPE=MyISAM')) {
	return false;
	}
	if(!$db->dbQuery('
	CREATE TABLE IF NOT EXISTS response (
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	parent INT(11) UNSIGNED,
	poster VARCHAR(255) NOT NULL DEFAULT \'\',
	created DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	body BLOB,
	PRIMARY KEY(id)
	) TYPE=MyISAM')) {
	return false;
	}
	if(!$db->dbQuery('
	CREATE TABLE IF NOT EXISTS registration (
	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	created DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	login CHAR(10) BINARY NOT NULL DEFAULT \'\',
	passwd CHAR(10) BINARY NOT NULL DEFAULT \'\',
	name VARCHAR(255) BINARY NOT NULL DEFAULT \'\',
	UNIQUE (login),
	PRIMARY KEY(id)
	) TYPE=MyISAM')) {
	return false;
	}*/
	return true;
}
/**
  * str_empty($str)
  *
  * Проверяет, что строка не пустая, т.е. содержит любые символы
  * отличные от символов пустого пространства [ \t\n\r\v]
  * \t - табуляция, \n - переход на новую строку, \r - возврат каретки,
  * \v - вертикальная табуляция
  *
  * @param string $str
  * @return bool
  */
function str_empty($str) {
	$str = trim($str);
	if(strlen($str) > 0) return false;
	else return true;
}
function authorisation_form($switcher) {
($switcher) ? $disp = 'show' : $disp = 'hidden';
return '<div style="margin-bottom: 10px;">
<form method="post" action="' . $_SERVER['PHP_SELF'] . '">
<div>Авторизация / <a href="' . $_SERVER['PHP_SELF'] . '?registration=' . $disp . '">Регистрация</a></div>
<div><label for="login">Логин: </label><input type="text" name="login" id="login" size="11" maxlength="10" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" /></div>
<div><label for="passwd">Пароль: </label><input type="password" name="passwd" id="passwd" size="11" maxlength="10" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" /></div>
<div><input type="submit" name="authorisationForm" value="Авторизоваться" /></div>
</form>
</div>';
}
function registration_form() {
	return '<div style="margin-bottom: 10px;">
<form method="post" action="' . $_SERVER['PHP_SELF'] . '">
<div><label for="login2">Логин: </label><input type="text" name="login" id="login2" size="11" maxlength="10" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" /></div>
<div><label for="passwd2">Пароль: </label><input type="password" name="passwd" id="passwd2" size="11" maxlength="10" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" /></div>
<div><label for="name">Имя: </label><input type="text" name="name" id="name" size="11" maxlength="10" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" /></div>
<div><input type="submit" name="registrationForm" value="Зарегистрироваться" /></div>
</form>
</div>';
}
function registrationConcurrence($login, $db, $el) {
	if($db->dbQuery("SELECT name FROM registration WHERE login='$login' LIMIT 1")) {
		$num_rows = mysql_num_rows($db->dbResult);
		if($num_rows > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		$el->mylog("Ошибка при проверки логина на совпадение!\r\n");
	}
	return false;
}
function registrationWrite($login, $passwd, $name, $db) {
	if($db->dbQuery("INSERT INTO registration(created, login, passwd, name) VALUES (
	NOW(), '$login', '$passwd', '$name')")) {
	if(isset($_SESSION)) {
		$_SESSION['username'] = $name;
		return true;
	}
	}
	else {
		return false;
	}
}
function authorisationConcurrence($login, $passwd, $db, $el) {
	if($db->dbQuery("SELECT name FROM registration WHERE login='$login' and passwd='$passwd' LIMIT 1")) {
		$num_rows = mysql_num_rows($db->dbResult);
		if($num_rows > 0) {
			$row = mysql_fetch_assoc($db->dbResult);
			if(isset($_SESSION)) {
				$_SESSION['username'] = $row['name'];
				return true;
			}
			else {
				$el->mylog("Ошибка при установке значения переменной сессии!\r\n");
			}
		}
		else {
			return false;
		}
	}
	else {
		$el->mylog("Ошибка при проверки логина и пароля на совпадение!\r\n");
	}
	return false;
}
?>