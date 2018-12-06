<?php
/**
 * Получает случайную цитату известных людей
 *
 * @return string
 */
function saying($file) {
	// поместим содержимое файла в массив 
	$arr = @file($file) or $arr = array();
	// выйти из функции, если файл пустой
	if (count($arr) == 0) return false;
	// сгенерировать случайное число
	mt_srand(floatval(microtime()) * 100000000);
	// и выбрать строку в соответсвии с этим числом
	$str = trim($arr[ mt_rand(0, count($arr)-1) ]);
	// получить из строки требуемые данные
	$arr = explode('|', $str);
	$str = '';
	if( isset($arr[0]) && ($arr[0] != '') ) $str .= '<div class="phrase"><q>' . $arr[0] . "</q></div>\n";
	if( isset($arr[1]) && ($arr[1] != '') ) $str .= '<div class="author">' . $arr[1] . "</div>\n";
	if( isset($arr[2]) && ($arr[2] != '') ) $str .= '<div class="description">' . $arr[2] . "</div>\n";
	// и возвратить отформатированную строку
	return $str;
}

/**
 * Получает все цитаты известных людей
 *
 * @return string
 */
function admin_saying($file) {
	// поместим содержимое файла в массив 
	$arr = @file($file) or $arr = array();
	// выйти из функции, если файл пустой
	if (count($arr) == 0) return false;
	// добавим к каждому элементу массива (строке с цитатой) строку ссылкой [ Delete ]
	for($i = 0; $i < count($arr); $i++) {
		$arr[$i] = '<a href="' . $_SERVER['PHP_SELF'] .  '?del=' . $i . '">[ Delete ]</a> ' . $arr[$i] . "<br />\n";
	}
	// объединим все элементы массива в строку
	$str = implode('', $arr);
	// и возвратим отформатированную строку
	return $str;
}
?>