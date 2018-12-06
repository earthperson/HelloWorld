myMysqlConnect() Функция для подключения к БД, с поддержкой кириллицы (см. также класс DatabaseCommon)
<?php
/**
 * Функция для подключения к БД, с поддержкой кириллицы
 *
 * @param string $db_host
 * @param string $db_user
 * @param string $db_passwd
 * @param string $db_name
 * @return mixed
 */
function mymysql_connect($db_host, $db_user, $db_passwd, $db_name) {
	if($dbLink = @mysql_connect($db_host, $db_user, $db_passwd)) {
		mysql_select_db($db_name, $dbLink);
		mysql_query('SET CHARACTER SET cp1251');
		mysql_query("SET NAMES cp1251");
		return $dbLink;
	}
	else {
		return mysql_error();
	}
}
?>