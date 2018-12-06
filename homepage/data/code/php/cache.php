cache0(), cache_period() Функции управления кэш-памятью браузера.
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
/**
 * Отправка заголовков для инициализации кэширования
 * 
 * @param int $seconds
 * @return null;
 * @copyright Взято из "PHP5 библиотека профессионала" стр. 779
 */
function cache_period($seconds = 86400) {
	// Время последней модификации отправляется как настоящее время модификации файла
	$lastModified = filemtime(__FILE__) + date('Z');
	header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModified) . " GMT");
	
	// Expires - Задает дату истечения срока годности документа.
	$expires = time() + $seconds;
	header("Expires: " . gmdate("D, d M Y H:i:s", $expires) . " GMT");
    
	// Сообщить кэш-памяти о возможности существования $seconds секунд
	header("Cache-Control: max-age=" . $seconds);
	return null;
}
?>