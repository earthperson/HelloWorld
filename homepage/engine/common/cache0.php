<?php
/**
 * Запрет на кэширование данных
 *
 * http://www.php.su/articles/?cat=protocols&page=012
 */
// Expires - Задает дату истечения срока годности документа. Задание ее в прошлом
// определяет запрет кэш для данной страницы.
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");// Date in the past

// Last-Modified - Дата послднего изменения содержимого. Поле актуально только для
// статических страниц. Apache заменяет это поле значением поля Date для динамически
// генерируемых страниц, в том числе для страниц содержащих SSI.
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified

// HTTP/1.1
// Cache-control: no-cache - Управление кэш. Значение no-cache определяет запрет кэш
// данной страницы. Для версии протокола HTTP/1.0 действует "Pragma: no-cache".
header("Cache-Control: no-store, no-cache, must-revalidate ");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");
?>