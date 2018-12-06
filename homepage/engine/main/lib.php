<?php
/**
 * Проверяет правильность заполнения форм в журнале посещения.
 *
 * @param string $name
 * @param string $msg
 * @param resource $journal_temp_file
 * @return mixed
 */
function journal_test($name, $msg, $spam, $journal_temp_file) {
	if($journal_temp_file) {
		$test = true;
		$name = trim($name);
		$msg = trim($msg);
		$spam = trim($spam);
		if(str_empty($name)) {
			fwrite($journal_temp_file, 'Ошибка: Поле "Имя:" обязательно для заполнения!<br />' . "\n");
			$test = false;
		}
		//
		if(str_empty($msg)) {
			fwrite($journal_temp_file, 'Ошибка: Поле "Заметка:" обязательно для заполнения!<br />' . "\n");
			$test = false;
		}
		//
		if(!str_valid_name($name)) {
			fwrite($journal_temp_file, 'Ошибка: Некорректное имя! Имя может содержать любые буквенно-цифровые символы, знак подчеркивания, пробел, дефис и должно начинаться с буквы.<br />' . "\n");
			$test = false;
		}
		//
		if (strlen($name) < 2) {
			fwrite($journal_temp_file, 'Ошибка: Имя слишком короткое! Минимум: два символа.<br />' . "\n");
			$test = false;
		}
		//
		if(str_cutwcrlf($name, JOURNAL_MAX_NAME_LENGTH)) {
			fwrite($journal_temp_file, 'Ошибка: Максимальная длина поля "Имя:" ' . JOURNAL_MAX_NAME_LENGTH . ' символов! Имя было укорочена до ' . JOURNAL_MAX_NAME_LENGTH . ' символов.<br />' . "\n");
			$test = false;
		}
		//
		if(str_cutwcrlf($msg, JOURNAL_MAX_MSG_LENGTH)) {
			fwrite($journal_temp_file, 'Ошибка: Максимальная длина поля "Заметка:" ' . JOURNAL_MAX_MSG_LENGTH . ' буквенно-цифровых символов символов (Символы переноса строки в это количество не входят)! Запись была укорочена до ' . JOURNAL_MAX_MSG_LENGTH . ' символов.<br />' . "\n");
			$test = false;
		}
		$a= split('[^0-9]+', $spam);
		if(!isset($a[0])) $a[0] = '';
		if(!isset($a[1])) $a[1] = '';
		if(!(isset($_SESSION['spamA'], $_SESSION['spamB']) && ( ($_SESSION['spamA'] . $_SESSION['spamB']) == (md5($a[0]) . md5($a[1]))) )) {
			fwrite($journal_temp_file, 'Ошибка: Неверно заполнено поле "Антиспам"!' . "\n");
			$test = false;
		}

		if($test) return true;
		else return array($name, $msg);
	}
	else
	return false;
}

/**
 * Проверяет переданные аргументы на наличие в них имени пользователя JOURNAL_NAME и пароля md5(JOURNAL_PASSWORD),
 * если они присутствуют одновременно строка переданная первым аргументом преобразуется в пустую строку, а строка,
 * переданная вторым аргументом в строку без первых JOURNAL_PASSWORD_LENGTH символов, соответствующих длине пароля.
 *
 * @param string $name
 * @param string $msg
 * @return array
 */
function journal_admin_test($name, $msg) {
	//
	$a = strcmp(JOURNAL_NAME, $name);
	$b = strcmp(JOURNAL_PASSWORD, md5(substr($msg, 0, JOURNAL_PASSWORD_LENGTH)));
	if ($a === 0 && $b === 0) {
		$name = '';
		$msg = substr($msg, JOURNAL_PASSWORD_LENGTH, strlen($msg));
	}
	return array($name, $msg);
}

function journal_unformat($str) {
	// расформатируем строку на составные части
	$arr = explode('|', $str);
	list($timestamp, $name, $msg) = $arr;
	$timestamp = '<div class="journaltimestamp">' . $timestamp . '</div>' . "\n";
	if($name == '')
	$name = '<div><span class="journalname">Имя: </span><i>Пономарев Дмитрий</i></div>' . "\n";
	else
	$name = '<div><span class="journalname">Имя: </span>' . $name . '</div>' . "\n";
	// временно преобразуем все html сущности в специальные символы, чтобы они
	// не разбивались функцией wordwrap, не забудем про кавычки (ENT_QUOTES).
	$msg = html_entity_decode($msg, ENT_QUOTES);
	// дополним слишком длинные строки символом переноса строки
	// (в форматированной строке используется одиночная кавычка ')
	$msg = wordwrap($msg, 29, "'", 1);
	// преобразуем обратно все специальные символы в html сущности, но одиночные кавычки не преобразуются
	// (ENT_COMPAT)
	$msg = htmlspecialchars($msg, ENT_COMPAT);
	// расформатируем символ переноса строки
	$msg = str_replace("'", '<br />', $msg);
	if(!str_empty($msg))
	$msg = '<div><span class="journalmsg">Заметка: </span>' . $msg . '</div>' . "\n";
	else $msg = '';
	return $timestamp . $name . $msg . "<br />\n";
}

function logos($arr, $num_page) {
	$begin = ($num_page - 1) * LOGOS_COUNT;
	$arr = array_slice($arr, $begin, LOGOS_COUNT);
	return implode('', $arr);
}

function journal_note($num_page) {
	if (file_exists(JOURNAL_DATA)) {
		$str = '';
		// поместим содержимое файла в массив
		// здесь используется урезанная оценка логических выражений
		$arr = @file(JOURNAL_DATA) or $arr = array();
		// выйти из функции, если файл пустой
		if (count($arr) == 0) return "&nbsp\n";
		// номер первой строки на $num_page странице
		$begin = ($num_page - 1) * JOURNAL_NOTE_COUNT;
		// получим срез массива длиной JOURNAL_NOTE_COUNT
		$arr = array_slice($arr, $begin, JOURNAL_NOTE_COUNT);
		// выйти из функции, если массив пустой
		if (count($arr) == 0) return "&nbsp\n";
		for($i = 0; $i < count($arr); $i++) {
			if(is_admin(ADMIN_PASSWD, ADMIN_NAME))
			$str .= '<div><a href="' . $_SERVER['PHP_SELF'] .  '?journaldel=' . $i . '">[ Delete ]</a></div>' . "\n";
			$str .= journal_unformat($arr[$i]);
		}
		return $str;
	}
	return "&nbsp\n";
}

function journal_delete_below_note() {
	// подсчитаем количество записей в журнале посещений
	$arr = @file(JOURNAL_DATA) or $arr = array();
	$count = count($arr);
	if ($count > JOURNAL_MAX_RECORD - 1)
	$arr = array_slice($arr, 0, JOURNAL_MAX_RECORD - 1);
	$handle = @fopen(JOURNAL_DATA, 'w');
	if($handle) fwrite($handle, implode('', $arr));
}

function news($file, $param, $param2 = 1, $num_page = 1, $mode = 0) {
	// поместим содержимое файла в массив
	$arr = @file($file) or $arr = array();
	$array_count = count($arr);
	// выйти из функции, если файл пустой
	if($array_count == 0) return false;
	// если не администратор, то показывать не все новости
	if(!$param && ($mode == 0)) {
		// номер первой строки на $num_page странице
		$begin = ($num_page - 1) * NEWS_COUNT;
		// выберем срез массива
		$arr = array_slice($arr, $begin, NEWS_COUNT);
	}
	if(!$param && ($mode == 1)) {
		// выберем срез массива
		$arr = array_slice($arr, 0, NEWS_COUNT_DEFAULT);
	}
	// показывать ссылку на архив новостей, если не администратор и
	// их больше чем показывается
	$link = '';
	if($array_count > count($arr)) {
		$link = '<div><a href="' . $_SERVER['PHP_SELF'] . '?newsarchive=part' . newsGETstr() . '">[ Архив новостей ]</a></div>' . "\n";
	}
	// получим из каждой строки требуемые данные
	$str = '';
	for($i=0; $i<count($arr); $i++) {
		$element = explode('|', $arr[$i]);
		if(is_admin(ADMIN_PASSWD, ADMIN_NAME)) $str .= '<div><a href="' . $_SERVER['PHP_SELF'] .  '?newsdel=' . $i . '">[ Delete ]</a></div>' . "\n";
		if(isset($element[0]) && $element[0] != '') $str .= '<div class="newsdate">' . $element[0] . '</div>' . "\n";
		if(isset($element[1]) && $element[1] != '') $str .= '<div class="newsdata">' . $element[1] . '</div>' . "\n";
	}
	if($param2 != 0) $str = $str . $link;
	// возвращаем отформатированную строку
	return $str;
}

function description($file) {
	// поместим содержимое файла в строку
	$str = @file_get_contents($file) or $str = '';
	return $str;
}

/**
 * Функция генерирует случайную ссылку - изображение
 *
 * @return array
 */
function mainlogos($arr) {
	$arr2 = parse_ini_file(LOGOS_DATA, true);
	$n = count($arr2) - 1;
	if($n > 0) {
		$key = array_rand($arr2, 2);
		if($key[0] == 'EOD') {
			$key = $key[1];
		}
		else {
			$key = $key[0];
		}
		list(,,,$imgsize) = getimagesize(LOGOS_PREFIX . basename($arr[$key]['logo']));
		return array($n, '<div><i style="font-size: smaller; font-family: Georgia, \'Times New Roman\', Times, serif; color: #27619C;">' . $arr[$key]['description'] . '</i></div><div><a href="' . $arr[$key]['reference'] . '" target="_blank" style="top: 0px;"><img src="' . $arr[$key]['logo'] . '" ' . $imgsize . ' alt="" border="0" id="opy20" onmouseover="setElementOpacity(\'opy20\', 0.5);" onmouseout="setElementOpacity(\'opy20\', 1.0);" title="Нажмите для перехода на официальный сайт" /></a></div>' . "\n");
	}
	else {
		return array($n, '');
	}
}

/**
 * Показать все логотипы
 *
 */
function mainlogosFull($arr) {
	$n=count($arr) - 1;
	$arr2 = array();
	$i = 20;
	foreach($arr as $key => $value) {
		$str = '';
		if($key === 'EOD') {
			continue;
		}
		list(,,,$imgsize) = getimagesize(LOGOS_PREFIX . basename($arr[$key]['logo']));
		if(is_admin(ADMIN_PASSWD, ADMIN_NAME)) {
			$str .= '<a href="/engine/main/a_logo.php?logos_replace=' . $_SERVER['PHP_SELF'] . '&logodel=' . $key . '">[ Delete ]</a>' . "\n";
		}
		$str .= '<div style="padding: 8px;"><div><i style="font-size: smaller; font-family: Georgia, \'Times New Roman\', Times, serif; color: #27619C;">' . $arr[$key]['description'] . '</i></div><div><a href="' . $arr[$key]['reference'] . '" target="_blank" style="top: 0px;"><img src="' . $arr[$key]['logo'] . '" ' . $imgsize . ' alt="" border="0" id="opy' . $i . '" onmouseover="setElementOpacity(\'opy' . $i . '\', 0.5);" onmouseout="setElementOpacity(\'opy' . $i . '\', 1.0);" title="Нажмите для перехода на официальный сайт" /></a></div></div>' . "\n";
		$arr2[] = $str;
		$i++;
	}
	return array($n, $arr2);
}
function journalGETstr() {
	$GETstr = '';
	$i=0;
	reset($_GET);
	while(list($key, $value) = each($_GET)) {
		if(isset($_GET['page'])) {
			if($key == 'page') continue;
		}
		if($i>0) {
			$GETstr .= '&amp;' . $key . '=' . $value;
		}
		else {
			$GETstr .= '?' . $key . '=' . $value;
		}
		$i++;
	}
	if($GETstr != '') {
		return $GETstr . "&amp;page=";
	}
	else {
		return "?page=";
	}
}
function newsGETstr() {
	$GETstr = '';
	foreach ($_GET as $key => $value) {
		if(isset($_GET['newsarchive'])) {
			if($key == 'newsarchive') continue;
		}
		if(isset($_GET['newspage'])) {
			if($key == 'newspage') continue;
		}
		$GETstr .= '&amp;' . $key . '=' . $value;
	}
	return $GETstr;
}
function logosGETstr($arg) {
	$GETstr = '';
	foreach ($_GET as $key => $value) {
		if(isset($_GET['logos'])) {
			if($key == 'logos') continue;
		}
		if(isset($_GET['logospage'])) {
			if($key == 'logospage') continue;
		}
		$GETstr .= '&amp;' . $key . '=' . $value;
	}
	if($arg) {
		return "?logos=show" . $GETstr . "&amp;logospage=";
	}
	else {
		return $GETstr;
	}
}
function votingGETstr() {
	$GETstr = '';
	$i=0;
	reset($_GET);
	while (list($key, $value) = each($_GET)) {
		if($i>0) {
			$GETstr .= '&' . $key . '=' . $value;
		}
		else {
			$GETstr .= '?' . $key . '=' . $value;
		}
		$i++;
	}
	return $GETstr;
}

/**
 * 
 * @param string $filename
 * @return array
 */
function myRSSparser($filename) {
	$str = file_get_contents($filename);
	// определим смещение до начала пунктов <item>
	$pos_start = strpos($str, '<item>');
	// определим смещение до конца пунктов </item>
	$pos_end = strrpos($str, '</item>');
	// получим заголовок файла
	$headline = substr($str, 0, $pos_start);
	$headline = preg_replace('%(\<link\>)(?:.*?)(\</link\>)%s', '$1http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '$2', $headline);
	$headline = preg_replace('%(\<title\>)(?:.*?)(\</title\>)%s', '$1' . TITLE . '$2', $headline);
	// получим нижнюю часть файла
	$footline = substr($str, $pos_end + strlen('</item>'));
	// получим массив пунктов
	$matches = array();
	preg_match_all('%\<item\>(.*?)\</item\>%s', $str, $matches, PREG_SET_ORDER);
	$arr = array();
	$arr['lines'] = array($headline, $footline);
	foreach ($matches as $value) {
		$arr['items'][] = $value[0] . "\n";
	}
	return $arr;
}

function addRSS($data, $time, $rssFileName) {
	$arr = myRSSparser($rssFileName);
	$str = '<item>
	<title>' . $time . '</title>
	<link>http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '</link>
	<description>' . $data . '</description>
	</item>' . "\n";
	if(count($arr['items']) > (RSS_COUNT - 1)) {
		$arr['items'] = array_slice($arr['items'], 0, (RSS_COUNT - 1));
	}
	if(count($arr['items']) == 1) {
		if(strpos($arr['items'][0], '<title />') && strpos($arr['items'][0], '<link />') && strpos($arr['items'][0], '<description />')) {
			array_shift($arr['items']);
		}
	}
	$f = fopen($rssFileName, 'w');
	if($f) {
		fwrite($f, $arr['lines'][0]);
		fwrite($f, $str);
		fwrite($f, trim(implode('', $arr['items'])));
		fwrite($f, $arr['lines'][1]);
		fclose($f);
	}
}

function delRSS($id, $rssFileName) {
	$arr = myRSSparser($rssFileName);
	if(count($arr['items']) > 1) {
		unset($arr['items'][$id]);
	}
	else {
		$arr['items'][0] = '
		<item>
		<title />
		<link />
		<description />		
		</item>';
	}
	$f = fopen($rssFileName, 'w');
	if($f) {
		fwrite($f, $arr['lines'][0]);
		fwrite($f, trim(implode('', $arr['items'])));
		fwrite($f, $arr['lines'][1]);
		fclose($f);
	}
}
?>