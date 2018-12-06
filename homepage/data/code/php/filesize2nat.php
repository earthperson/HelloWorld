filesize2nat() Функция для перевода n-байт в строку в удобном для чтения виде (см. также класс StringCommon)
<?php
/**
 * Функция переводит n-байт в строку в удобном для чтения виде
 *
 * @param int $size
 * @return int
 * @copyright http://dmitry-ponomarev.ru
 */
function filesize2nat($size) {
	if(floor($size / (1 << 30)) > 0) {
		$size = (string) round($size / (1 << 30), 3) . 'Gb';
	}
	else if(floor($size / (1 << 20)) > 0) {
		$size = (string) round($size / (1 << 20), 3) . 'Mb';
	}
	else if (floor($size / (1 << 10)) > 0){
		$size = (string) round($size / (1 << 10), 3) . 'Kb';
	}
	else {
		$size = (string) $size . 'b';
	}
	return $size;
}
?>