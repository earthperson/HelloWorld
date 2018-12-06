<?php
// включаем буферизацию вывода
ob_start();

require_once '../common/lib.php';
require_once 'config.php';
require_once '../classes/ErrorLog.php';

// Cоздаем экземпляр класса для вывода сообщения об ошибках
$el = new ErrorLog();

/**
 * Возвращает массив переданных аргументов, если они прошли проверку, иначе строку ошибки
 *
 * @param string $reference
 * @param string $logo
 * @param string $descr
 * @return mixed
 */
function check_fields($reference, $logo, $descr) {
	$str = '';
	if(strncmp($reference, 'http://', strlen('http://'))) {
		$reference = 'http://' . $reference;
	}
	if(strncmp($logo, LOGOS_PREFIX, strlen(LOGOS_PREFIX))) {
		$logo = LOGOS_PREFIX . $logo;
	}
	if(strlen($reference) == strlen('http://')) {
		$str .= 'Не заполнено поле "Reference"!' . "\r\n";
	}
	if(!@exif_imagetype('../../' . $logo)) {
		$str .= 'Ошибка в имени изображения!' . "\r\n";
	}
	if(str_empty($descr)) {
		$str .= 'Не заполнено поле "Description"!' . "\r\n";
	}
	if($str) {
		return $str;
	}
	return array($reference, $logo, $descr);
}

function form_str($arr) {
	if(is_readable('../../' . LOGOS_DATA)) {
		$str = "\n[" . count(parse_ini_file('../../' . LOGOS_DATA, true)) . ']'
		. "\nreference = " . $arr[0]
		. "\nlogo = " . $arr[1];
		if(substr_count($arr[2], "\x20") > 0) {
			$str .= "\ndescription = \"" . $arr[2] . "\"\n";
		}
		else {
			$str .= "\ndescription = " . $arr[2] . "\n";
		}
		return $str;
	}
	else {
		return false;
	}
}

function write_formed_str($str) {
	$f_str = file_get_contents('../../' . LOGOS_DATA);
	$f_str = str_replace("\n[EOD]", '', $f_str);
	$f = fopen('../../' . LOGOS_DATA, 'w');
	if($f) {
		fwrite($f, $f_str . $str . "\n[EOD]");
		fclose($f);
		return true;
	}
	else {
		return false;
	}
}

function logo_err($str, $el) {
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_REQUEST['logos_replace'] . $el->mylog($str, '', false));
	exit();
}

function logo_sccss($str, $el) {
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_REQUEST['logos_replace'] . $el->mysuccess($str, '', false));
	exit();
}

function logodel($id) {
	$str = @file_get_contents('../../' . LOGOS_DATA);
	$str = preg_replace('%\[' . $id . '\].*?(\[(?:\d+|EOD)\])%s', '$1', $str);
	$f = fopen('../../' . LOGOS_DATA, 'w');
	if($f) {
		fwrite($f, $str);
		fclose($f);
		return true;
	}
	else {
		return false;
	}
}

// если нужно добавить логотип
if(isset($_POST['logo_submit'])) {
	// если не произошла ошибка при передаче параметров скрипту
	if(isset($_POST['reference'], $_POST['logo'], $_POST['description_logo'], $_POST['logos_replace'])) {
		// проверяем, что хотя бы одно поле было заполнено
		if(!(str_empty($_POST['reference']) && str_empty($_POST['logo']) && str_empty($_POST['description_logo']))) {
			// проверка полей
			$checked_fields = check_fields(
			trim($_POST['reference']),
			trim($_POST['logo']),
			trim($_POST['description_logo']));
			if(is_array($checked_fields_str = $checked_fields)) {
				// формирование строки для записи и запись в файл
				if($formed_str = form_str($checked_fields)) {
					if(write_formed_str($formed_str)) {
						logo_sccss('Логотип успешно добавлен!' . "\r\n" . $formed_str, $el);
					}
					else {
						logo_err('Ошибка при записи в файл!', $el);
					}
				}
				else {
					logo_err('Ошибка при формировании строки!', $el);
				}
			}
			else {
				logo_err($checked_fields_str, $el);
			}
		}
		else {
			logo_err('Не заполнено ни одно поле!', $el);
		}
	}
	else {
		logo_err('Ошибка при передаче параметров скрипту!', $el);
	}
}

// если нужно удалить логотип
if(isset($_GET['logodel'])) {
	// если число или числовая строка
	if(is_numeric($_GET['logodel'])) {
		if(logodel(intval($_GET['logodel']), $el)) {
			logo_sccss('Логотип успешно удален!', $el);
		}
		else {
			logo_err('Ошибка при удалении!', $el);
		}
	}
	else {
		logo_err('Неправильный параметр!', $el);
	}
}
// отправляем содержимое буфера вывода и
// выключаем буферизацию вывода
ob_end_flush();
// +-------------------------------------------------------------------------------------------+
?>