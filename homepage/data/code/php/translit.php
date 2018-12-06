ru2lat() lat2ru() Функции перевода текста с транслита в кириллицу и обратно (см. также класс StringCommon)
<?php
/**
  * Функция перевода текста с кириллицы в транслит
  *
  * @param string $str
  * @return string
  * @copyright http://dmitry-ponomarev.ru
  */
function ru2lat($str) {
	$rus = array('ё','ж','ц','ч','ш','щ','ю','я','Ё','Ж','Ц','Ч','Ш','Щ','Ю','Я');
	$lat = array('yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA');
	$str = str_replace($rus, $lat, $str);
	$str = strtr($str, "АБВГДЕЗИЙКЛМНОПРСТУФХЪЫЬЭабвгдезийклмнопрстуфхъыьэ", "ABVGDEZIJKLMNOPRSTUFH#I'Eabvgdezijklmnoprstufh#i'e");
	return ($str);
}
/**
  * Функция перевода текста с транслита в кириллицу
  *
  * @param string $str
  * @return string
  * @copyright http://dmitry-ponomarev.ru
  */
function lat2ru($str) {
	$rus = array('ё','ж','ц','ч','ш','щ','ю','я','Ё','Ж','Ц','Ч','Ш','Щ','Ю','Я');
	$lat = array('yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA');
	$str = str_replace($lat, $rus, $str);
	$str = strtr($str, "ABVGDEZIJKLMNOPRSTUFH#I'Eabvgdezijklmnoprstufh#i'e", "АБВГДЕЗИЙКЛМНОПРСТУФХЪЫЬЭабвгдезийклмнопрстуфхъыьэ");
	return ($str);
}
?>