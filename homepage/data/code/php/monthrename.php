monthrename() Русификация названия месяца (см. также класс StringCommon)
<?php
/**
  * monthrename(&$monthname)
  * Русификация названия месяца
  *
  * @param string $monthname
  * @copyright http://dmitry-ponomarev.ru
  */
function monthrename(&$monthname) {
	$search = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$replace = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
	$monthname = str_replace($search, $replace, $monthname);
}
?>