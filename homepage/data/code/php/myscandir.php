myscandir() Получить отсортированный список ФАЙЛОВ по указанному пути
<?php
/**
 * Получает список ФАЙЛОВ по указанному пути, вложенные каталоги не просматриваются,
 * возвращает отсортированный (используется алгоритм сортировки, при котором порядок буквенно-цифровых 
 * строк будет привычным для человека) по имени ФАЙЛОВ массив, или true, если каталог пуст или false,
 * в случае возникновения ошибки, причина по которой не используется natsort() - она сохраняет ключи
 * для элемента $arr
 *
 * @param string $dirname
 * @return mixed
 * @copyright http://dmitry-ponomarev.ru
 */
function myscandir($dirname) {
	if(is_dir($dirname)) {
		if($DH = opendir($dirname)) {
			while (false !== ($filename = readdir($DH))) {
				if($filename != '.' && $filename != '..') {
					if(is_file($dirname . $filename)) {
						$arr[] = $filename;
					}
				}
			}
			closedir($DH);
			if(isset($arr)) {
				function myscandir_cmp($a, $b) {
					return strnatcmp($a, $b);
				}
				usort($arr, 'myscandir_cmp');
				return $arr;
			}
			else {
				return true;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}
?>