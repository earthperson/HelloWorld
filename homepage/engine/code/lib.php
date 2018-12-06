<?php
/**
 * Получает список ФАЙЛОВ по указанному пути, вложенные каталоги не просматриваются,
 * возвращает отсортированный по имени ФАЙЛОВ массив, или true, если каталог пуст или false,
 * в случае возникновения ошибки
 *
 * @param string $dirname
 * @return mixed
 */
function myscan_dir_cmp($a, $b) {
	return strnatcmp($a, $b);
}
function myscan_dir($dirname) {
	if(is_dir($dirname)) {
		if($DH = opendir($dirname)) {
			while (false !== ($filename = readdir($DH))) {
				if($filename != '.' && $filename != '..' && $filename != '.htaccess') {
					if(is_file($dirname . $filename)) {
						$arr[] = $filename;
					}
				}
			}
			closedir($DH);
			if(isset($arr)) {
				usort($arr, 'myscan_dir_cmp');
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
function code($path, $switcher = false) {
	$string = @file_get_contents($path);
	if(isset($_GET)) {
		$getstr = '';
		foreach($_GET as $key => $value) {
			if($key == 'pagephp' || $key == 'pagejs') {
				$getstr .= '&' . $key . '=' . $value;
			}
		}
	}
	if($string) {
		$matches = preg_split('%(\<\?php)|(\<script)%', $string);
		if(count($matches) > 1) {
			$about = trim($matches[0]);
			$string = preg_replace('%' . preg_quote($matches[0]) . '%', '', $string, 1);
		}
		else {
			$about = '?';
		}
		$path_parts = pathinfo($path);
		if($switcher) {
			print '<div class="tree">' . "\n";
			print '<a href="' . $_SERVER['PHP_SELF'] . '?' . urlencode($path_parts['basename']) . '=hidden' . $getstr . '">-</a> ' . $about . "</div><div>\n";
			if(!($path_parts['extension'] == 'php' || $path_parts['extension'] == 'PHP')) {
				$string = str_replace('\\', '__bug_back_slash', $string);
				$highlightstr = @highlight_string("<?php\n" . trim($string) . "\n?>", true);
				// если установлен флаг s, '.' означает любой символ, включая символ перехода на новую строку.
				// синтаксис (?: ) - незахватывающие скобки, т.е. выражение в скобках недоступно для возвратной ссылки.
				$highlightstr = str_replace('__bug_back_slash', '\\', $highlightstr);
				print(preg_replace('%&lt;\?php[\n\r]*(?:<br \/>)*(.*)\?&gt;%s', '$1', $highlightstr));
			}
			else {
				@highlight_string($string);
			}
			print '</div>' . "\n";
		}
		else {
			print '<div class="tree">' . "\n";
			/*'<a href="' . $_SERVER['PHP_SELF'] . '?' . urlencode($path_parts['basename']) . '=show' . $getstr . '">+</a> ' . $about . '</div>' . "\n";*/
				print '<a href="' . $_SERVER['PHP_SELF'] . '?' . urlencode($path_parts['basename']) . '=show' . $getstr . '">+</a> ' . $about . '</div>' . "\n";
		}
	}
	return null;
}
?>