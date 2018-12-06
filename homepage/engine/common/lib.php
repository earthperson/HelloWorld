<?php
function idgen() {
	static $id = 'phpidgenerator0';
	return $id++;
}
/**
 * fulltime()
 * Возвращает время в формате 'сек, мксек'
 *
 * @return float
 */
function fulltime() {
	// считываем текущее время
	$time = microtime();
	// получаем секунды и микросекунды
	list($usec, $sec) = explode(' ', $time);
	// возвращаем стартовое время
	return ( (float)$sec + (float)$usec );
}

/**
 * cookie_check($param = '')
 * Проверяет включены ли cookie, где $param необходимо устанавливать,
 * если cookie отключены, а на странице используются параметры, передаваемые методом GET.
 * Для того, чтобы их не потерять, вследствии перезагрузки страницы при отключенных cookie,
 * до использования функции используйте следующий код:
 * 
 * $GETstr = '';
 * foreach($_GET as $key => $value) {
 * $GETstr .= '&' . $key . '=' . $value;
 * }
 * 
 * if(cookie_check($getstr)) {
 * 
 * }
 * 
 * @param string $param
 * @return bool
 * 
 * Copyright 2007 Ponomarev Dmitry
 * http://dmitry-ponomarev.ru
 */
function cookie_check($param = '') {
	if( !isset($_GET['cookie']) && !isset($_COOKIE['cookie']) ) {
		// устанавливаем cookie с именем 'test'
		setcookie('cookie', 'test');
		header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?cookie=test' . $param);
		exit;
	}
	else {
		if( !isset($_COOKIE['cookie']) ) {
			//cookie выключены
			return false;
		}
		else {
			//cookie включены
			return true;
		}
	}
}
/**
 * monthrename_func(&$monthname)
 * Русификация названия месяца
 *
 * @param string $monthname
 */
function monthrename(&$monthname) {
	$search = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$replace = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
	$monthname = str_replace($search, $replace, $monthname);
}

/**
 * is_admin($passwd, $name)
 * Проверяет является ли пользователь администратором на основании значения глобальной
 * переменной $sess В первом параметре указывается строка с именем пользователя
 * В качестве второго параметра указывается MD5 хэш строки пароля
 * Требуется сначала инициализировать сессию с помощью функции session_start(),
 * т.к., в этом случае в течение сессии повторный ввод пароля в адресной строке не требуется.
 * Если $_GET['admin'] = 'dstr' возвращает false и удаляет переменную сессии;
 * 
 * @param string $name
 * @param string $passwd
 * @return bool
 */
function is_admin($passwd, $name = 'admin') {
	$admin = false;
	
	if(isset($GLOBALS[$name])) {
		if($GLOBALS[$name] == $passwd) {
			$admin = true;
			if (!isset($_SESSION[$name])) {
				$_SESSION[$name] = $passwd;
			}
		}
	}

	if(isset($_GET['admin']) && ($_GET['admin'] == 'dstr')) {
		if(isset($_SESSION[$name])) {
			unset($_SESSION[$name]);
			$admin = false;;
		}		
	}

	if(isset($_SESSION[$name])) {
		if ($_SESSION[$name] === $passwd) {
			$admin = true;
		}
	}

	return $admin;
}

/**
 * add($str, $param, $filename);
 * Записывает строку, переданную первым параметром в файл.
 * Если второй параметр положительное число, строка добавляется в конец файла, иначе в начало файла.
 * Имя файла задается третьим параметром
 * 
 * Если строка, переданная первым параметром, не заканчивается символом переноса строки, он добавляется
 * 
 * @param string $str
 * @param int $param
 * @param string $filename
 * @return bool
 * 
 */
function add($str, $param, $filename) {
	// проверка строки для вставки
	if( $str[strlen($str) - 1] != "\n" )
	$str .= "\n";
	// поместим содержимое файла в массив
	$arr = @file($filename) or $arr = array();
	// Если второй параметр положительный
	if ($param > 0) {
		array_unshift($arr, $str);
	}
	// Если второй параметр отрицательный
	else {
		$arr[] = $str;
	}
	// открываем файл для записи
	$handler = @fopen($filename, 'w');
	if($handler) {
		// записываем строку в файл
		fwrite($handler, implode('', $arr));
		fclose($handler);
		return true;
	}
	else
	return false;
}



/**
 * del($id, $filename, $filelog = LOG_FILE)
 *
 * Удаляет строку с индексом переданным первым параметром
 * из файла с именем, переданным вторым параметром,
 * необязательный последний параметр указывает имя файла, куда записывается
 * сообщение об ошибке
 *
 * @param int $id
 * @param string $filename
 * @param string $filelog
 */
function del($id, $filename, $filelog = LOG_FILE) {
	$lines = @file($filename);
	if($lines) {
		for($i=0; $i < count($lines); $i++) {
			if ($id === $i) {
				unset($lines[$i]);
			}
			$handler = @fopen($filename, 'w');
			if($handler) {
				fwrite($handler, implode('', $lines));
				fclose($handler);
			}
			else {
				error_log('Ошибка! Файл: ' . __FILE__ . ' Функция: ' . __FUNCTION__ . ' Строка: ' . __LINE__ . "<br />\n", 3, $filelog);
			}
		}
	}
	else {
		error_log('Ошибка! Файл: ' . __FILE__ . ' Функция: ' . __FUNCTION__ . ' Строка: ' . __LINE__ . "<br />\n", 3, $filelog);
	}
}

/**
 * str_empty($str)
 *
 * Проверяет, что строка не пустая, т.е. содержит любые символы
 * отличные от символов пустого пространства [ \t\n\r\f\v]
 * \t - табуляция, \n - переход на новую строку, \r - возврат каретки,
 * \f - подача страницы, \v - вертикальная табуляция
 *
 * @param string $str
 * @return bool
 */
function str_empty($str) {
	// проверяем, что строка соответствует регулярному выражению
	if(preg_match('/\S/', $str)) return false;
	else return true;
}

/**
 * str_valid_name($str)
 *
 * Проверяет, что строка содержит только любые буквенно-цифровые символы,
 * знак подчеркивания и пробел.
 *
 * @param string $str
 * @return bool
 */
function str_valid_name($str) {
	// проверяем, что строка соответствует регулярному выражению
	$x = preg_match('/^[a-zа-яА-Яё][\wа-яА-Яё -]*$/i', $str);
	if($x === 0) return false;
	else return true;
}

/**
 * str_valid_mail($str)
 *
 * Проверяет, e-mail
 *
 * @param string $str
 * @return bool
 */
function str_valid_mail($str) {
	// проверяем, что строка соответствует регулярному выражению
	// стр. 480 полного справочника по javascript
	$x = preg_match('/[^@]+@(\w+\.)+\w+/', $str);
	if($x === 0) return false;
	else return true;
}

/**
 * str_format($arr, $param = false, $funcname = null)
 *
 * Форматирует строки, переданные в качестве элементов массива (первым параметром), в строку
 * для записи в файл на выходе - строка, где символы переноса строки заменены на '
 * а все специальные html символы заменены. В начале строки указывается текущая дата
 * в формате H:m d F Y, если второй параметр true, месяц будет русифицирован, при передаче третьим параметром функции русификации
 * (она должна возвращать значение по ссылке).  
 * 
 * @param array $arr
 * @param bool $param
 * @param string $funcname
 * @return string
 */
function str_format($arr, $param = false, $funcname = null) {
	for($i=0, $n = count($arr); $i < $n; $i++) {
		$arr[$i] = trim($arr[$i]);
		$arr[$i] = str_replace('|', '&brvbar;', $arr[$i]);
		$arr[$i] = preg_replace('/\r{2,}/', "\r", $arr[$i]);
		$arr[$i] = preg_replace('/\n{2,}/', "\n", $arr[$i]);
		// блок (1) предназначен, чтобы не допустить отображения большого количества переносов
		// строк в журнале, по достижении некоторого числа они заменяются пробелами
		// (пример:
		// spam
		// ...
		// spam)
		// узнаем число вхождений подстроки \n
		$LF_count = substr_count($arr[$i], "\n");
		// если больше константы, то заменяем пробелом
		if($LF_count >= JOURNAL_CRLF) {
			// Windows
			$arr[$i] = str_replace("\r\n", ' ', $arr[$i]);
			// Unix
			$arr[$i] = str_replace("\n", ' ', $arr[$i]);
		}
		// END (1)
	}
	$str = implode('|', $arr);
	// преобразуем одиночные кавычки в двойные, т.к. они будут использоваться в отформатированной строке,
	// в качестве указателя места переноса строки
	$str = str_replace("'", '"', $str);
	// преобразуем специальные символы в html сущности, не забудем преобразовать кавычки (ENT_QUOTES).
	$str = htmlspecialchars($str, ENT_QUOTES);
	// удаляем экранирующие бэкслэши, если директива magic_quotes_gpc включена
	if(get_magic_quotes_gpc()) $str = stripslashes($str);
	// заменяем \r\n на '
	$str = preg_replace('/[\r\n]+/', "'", $str);

	if($param) {
		$month = gmdate('F', (time() + 3600 * 3));
		// русификация названия месяца
		// call_user_func_array Вызывает пользовательскую функцию с массивом параметров
		if($funcname) call_user_func_array($funcname, array(&$month));
		$timestamp = gmdate('H:i d ', (time() + 3600 * 3)) . $month . gmdate(' Y', (time() + 3600 * 3)) . '|';
	}
	$str = $timestamp . $str;
	return $str;
}

/* Функция вывода навигации по страницам */
// автор - http://niko.net.ru/
function nav_page(
$count,    //Общее кол-во страниц
$num_page, //Номер текущей страницы
$url,      //Какой URL для ссылки на страницу (к нему добавляется номер страницы)
$page_nav, //сколько ссылок на страницы выводить одновременно
$img
) {

	$begin_loop=1; //начальное значение в цикле
	$end_loop=$count; //конечное значение в цикле
	$size = getimagesize($img);
	print('<div class="nav_page">[ Страницы (' . $count . '):&nbsp;<img src="' . $img . '" ' . $size[3] . ' alt="" />');
	if ($num_page>$count or $num_page<1) $num_page=1; //Проверка на корректность номера текущей страницы

	//Далее в функции идёт сам вывод навигации, получено здесь всё опытным путём
	if ($num_page>$page_nav)
	{
		print("&nbsp;<a href=\"$url");
		print(($page_nav*(floor($num_page/$page_nav)-($num_page%$page_nav==0 ? 1: 0))));
		print("\" class=\"nav_page_func\">(");
		print(($page_nav*(floor($num_page/$page_nav)-1-($num_page%$page_nav==0 ? 1: 0))+1));
		print("-");
		print(($page_nav*(floor($num_page/$page_nav)-($num_page%$page_nav==0 ? 1: 0))));
		print(")</a>...&nbsp;");
		$begin_loop=$page_nav*(floor($num_page/$page_nav)-($num_page%$page_nav==0 ? 1: 0))+1;
	}
	if ($count>$page_nav*(floor($num_page/$page_nav)-($num_page%$page_nav==0 ? 1: 0)+1))
	{
		$end_loop=$page_nav*ceil($num_page/$page_nav);
	}
	for ($i = $begin_loop; $i <= $end_loop;  $i++)
	{
		if ($i==$num_page)
		print("<b>&nbsp;$i&nbsp;</b>");
		else
		{
			print("&nbsp;<a href=\"$url$i\" class=\"nav_page_func\">$i</a>&nbsp;");
		}
	}//for
	if ($count>$page_nav*(floor($num_page/$page_nav)-($num_page%$page_nav==0 ? 1: 0)+1)) {
		print("&nbsp;...<a href=\"$url");
		print(($page_nav*ceil($num_page/$page_nav)+1));
		print("\" class=\"nav_page_func\">(");
		print(($page_nav*ceil($num_page/$page_nav)+1));
		if ($page_nav*ceil($num_page/$page_nav)+1<$count)
		{
			print("-");
			print(($count<=$page_nav*(ceil($num_page/$page_nav)+1) ? $count: $page_nav*(ceil($num_page/$page_nav)+1)));
		}
		print(")</a>");
	}
	print("&nbsp;]\n</div>\n");
}

/**
 * str_cutwcrlf(&$str, $maxlength)
 *
 * Функция при подсчете длины строки не учитывает символы переноса строки, 
 * если длина строки, переданной первым параметром, больше предела,
 * заданного вторым аргументом функция возвращает true и передает по ссылке
 * укороченную строку, в противном случае возвращает false.
 *
 * @param string $str
 * @param int $maxlength
 * @return bool
 */
function str_cutwcrlf(&$str, $maxlength) {
	// подсчитываем кол-во символов переноса строки раздельно,
	// чтобы код ниже возможно работал корректно под UNIX
	$CR_count = substr_count($str, "\r");
	$LF_count = substr_count($str, "\n");
	$CRLF_count = $CR_count + $LF_count;
	if ( strlen($str) > ($maxlength + $CRLF_count) ) {
		// заменяем \r[\n] на ' (для того, чтобы код ниже возможно работал корректно под UNIX)
		$string = trim($str);
		$string = preg_replace('/[\r\n]+/', "'", $str);
		// разбиваем строку на подстроки по CRLF и получаем их в виде массива
		$arr = explode("'", $string);
		// получаем строку без символов разделителя
		$string = implode('', $arr);
		// укорачиваем в соответствии с пределом
		$string = substr($string, 0, $maxlength);
		// восстанавливаем по массиву $arr символы переноса строки
		for($i=0, $j=0, $newstr = ''; strlen($newstr) < strlen($string); $i++) {
			$buffer = substr($string, $j, strlen($arr[$i]));
			$newstr .= $buffer;
			$newarr[$i] = $buffer;
			$j += strlen($arr[$i]);
		}
		$newstr = implode("\r\n", $newarr);
		// если строка была укорочена
		if (strlen($newstr) != strlen($str)) {
			$str = $newstr;
			return true;
		}
	}
	return false;
}

/**
 * Меню на подстраницах
 *
 * @param array $pagearr
 * @return string
 */
function selfmenu($pagearr) {
	$linkarrbuffer = '';
	while(list($key, $value) = each($pagearr)) {
		// на текущей странице не ссылка
		if(strpos($key, basename($_SERVER['PHP_SELF'])) !== false) {
			$linkarr[] = '<i>' . $value . '</i>';
			continue;
		}
		if(strpos($key, 'index.php') !== false) {
			$linkarrbuffer = '<a href="/' . $key . '?show=yes">' . $value . '</a>' . "\n";
			continue;
		}
		if(strpos($key, 'main.php') !== false) {
			$linkarr[] = '<a href="/' . $key . '">' . $value . '</a>';
			continue;
		}
		$linkarr[] = '<a href="/main/' . $key . '">' . $value . '</a>';
	}
	return implode(' / ', $linkarr) . ' / ' . $linkarrbuffer;
}

/**
 * Меню на главной странице
 *
 * @param unknown_type $pagearr
 */
function mainmenu($pagearr) {
	$link = '';
	while(list($key, $value) = each($pagearr)) {
		if(strpos($key, 'index.php') !== false) continue;
		if(strpos($key, basename($_SERVER['PHP_SELF'])) !== false) {
			$link .= '<a href="' . $key . '" class="home">' . $value . '</a>' . "\n";
			continue;
		}
		$link .= '<a href="/main/' . $key . '" class="menu">' . $value . '</a>' . "\n";
	}
	return $link;
}

/**
 * Функция считывает содержимое временного файла, указатель на файл указывается в аргументе функции
 * и возвращает его содержимое в виде строки, а также удаляет временный файл.
 * Если файл пустой возвращает false 
 *
 * @param resource $handle
 * @return mixed
 */
function tmpfile_error_show($handle) {
	if($handle) {
		rewind($handle);
		$str = '';
		while(!feof($handle)) {
			$str .= fgetc($handle);
		}
		fclose($handle);
		if (strlen($str) > 0)
		return $str;
	}
	return false;
}
/**
 * возвращает строку: Hour:Minutes:Seconds Day Month Year
 *
 * @return string
 */
function whatisthetime() {
	if (!(func_num_args() > 0)) {
		$timestamp = time() + (3600 * 3);
	}
	else {
		$timestamp = func_get_arg(0);
	}
	// день месяца без ведущих нулей
	$time_d = gmdate('j', $timestamp);
	// полное наименование месяца
	$time_m = gmdate('F', $timestamp);
	monthrename($time_m);
	return gmdate('H:i:s ', $timestamp) . $time_d . ' ' . $time_m . gmdate(' Y', $timestamp);
}
function mymail($from, $body) {
	$recipient = ucfirst('Dmitry') . ' <ponomarev.host@gmail.com>';
	$subject = 'Сообщение с домашней страницы';
	//Одна из часто возникающих проблем при отправке почты в koi8 - это формирование заголовка письма.
	//Для ее решения необходимо воспользоваться следующим кодом, который переводит строку в кодировке
	//win-1251 в понятный большинству почтовых клиентов koi8 заголовок. - из статьи
	$subject = '=?koi8-r?B?' . base64_encode(convert_cyr_string($subject, 'w', 'k')) . '?=';
	$headers = "";
	$headers .= "Content-Type: text/plain; charset=windows-1251\r\n";
	$headers .= "From: " . $from . " <ponomarev.host@gmail.com>\r\n";
	if(@mail($recipient, $subject, $body, $headers)) {
		return true;
	}
	else {
		return false;
	}
}
?>



