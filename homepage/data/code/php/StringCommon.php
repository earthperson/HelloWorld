StringCommon Класс для работы со строками
<?php
class StringCommon {
	/**
     * str_empty($str)
     *
     * Проверяет, что строка не пустая, т.е. содержит любые символы
     * отличные от символов пустого пространства [ \t\n\r\v]
     * \t - табуляция, \n - переход на новую строку, \r - возврат каретки,
     * \v - вертикальная табуляция
     *
     * @param string $str
     * @return bool
     */
	public function str_empty($str) {
		$str = trim($str);
		if(strlen($str) > 0) return false;
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
	public function str_valid_name($str) {
		$str = trim($str);
		if(preg_match('/^[a-zа-яА-Яё][\wа-яА-Яё -]*$/i', $str)) {
			return true;
		}
		else return false;
	}
	/**
     * str_valid_mail($str)
     *
     * Проверяет, e-mail
     *
     * @param string $str
     * @return bool
     */
	public function str_valid_mail($str) {
		$str = trim($str);
		// стр. 480 полного справочника по JavaScript
		if(preg_match('/[^@]+@(\w+\.)+\w+/', $str)) {
			return true;
		}
		else return false;
	}
	/**
     * str_valid_passwd($str)
     *
     * Проверяет, пароль
     *
     * @param string $str
     * @return bool
     */
	public function str_valid_passwd($str) {
		$str = trim($str);
		if(preg_match('/^[\wа-яА-Яё -]+$/i', $str)) {
			return true;
		}
		else return false;
	}
	/**
     * str_lencrlf(&$str, $maxlength)
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
	public function str_lencrlf(&$str, $maxlength = null) {
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
     * monthrename(&$monthname)
     * Русификация названия месяца
     *
     * @param string $monthname
     */
	public function monthrename(&$monthname) {
		$search = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		$replace = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
		$monthname = str_replace($search, $replace, $monthname);
	}
	/**
     * str_format($arr, $param = false)
     *
     * Форматирует строки, переданные в качестве элементов массива (первым параметром), в строку
     * для записи в файл на выходе - строка, где символы переноса строки заменены на '
     * а все специальные html символы заменены. В начале строки указывается текущая дата
     * в формате H:m d F Y, если второй параметр true, месяц будет русифицирован
     * 
     * @param array $arr
     * @param bool $param
     * @return string
     */
	public function str_format($arr, $param = false, $num_CRLF = 8) {
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
			if($LF_count >= $num_CRLF) {
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
			$month = gmdate('F',  (time() + 3600 * 3));
			// русификация названия месяца
			$this->monthrename($month);
			$timestamp = gmdate('H:i:s d ', (time() + 3600 * 3)) . $month . gmdate(' Y',  (time() + 3600 * 3)) . '|';
		}
		$str = $timestamp . $str . "\n";
		return $str;
	}
	public function str_deformat($str, $class = 'undefined', $wrap = 29) {
		// расформатируем строку на составные части
		$arr = explode('|', $str);
		$str = '';
		for($i = 0, $n = count($arr); $i<$n; $i++) {
			if($this->str_empty($arr[$i])) {$arr[$i] = '&nbsp;';}
			// временно декодируем все html сущности, чтобы они
			// не разбивались функцией wordwrap, не забудем про кавычки (ENT_QUOTES).
			$arr[$i] = html_entity_decode($arr[$i], ENT_QUOTES);
			// дополним слишком длинные строки символом переноса строки
			// (в форматированной строке используется одиночная кавычка ')
			$arr[$i] = wordwrap($arr[$i], $wrap, "'", 1);
			// кодируем символы в html сущности, но одиночные кавычки не преобразуются
			// (ENT_COMPAT)
			$arr[$i] = htmlspecialchars($arr[$i], ENT_COMPAT);
			// расформатируем символ переноса строки
			$arr[$i] = str_replace("'", '<br />', $arr[$i]);
			$str .= '<div class="' . $class . '">' . $arr[$i] . "</div>\n";
		}
		return $str;
	}
	/**
	 * здесь используется таблица транслитерации sfb441
	 * http://www.sfb441.uni-tuebingen.de/b1/rus/translit.html
	 *
	 * @param unknown_type $arg
	 * @return unknown
	 */
	private function private_engine_mb($arg) {
		$rus = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ';
		// преобразуем строку в массив array str_split ( string string [, int split_length] )
		$rus = str_split($rus);
		$lat = array('a', 'b', 'v', 'g', 'd', 'e', 'oh', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'x', 'c', 'ch', 'sh', 'w', 'qh', 'y', 'q', 'eh', 'ju', 'ja', 'A', 'B', 'V', 'G', 'D', 'E', 'OH', 'ZH', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'X', 'C', 'CH', 'SH', 'W', 'QH', 'Y', 'Q', 'EH', 'JU', 'JA');
		// создадим новый массив array array_combine ( array keys, array values )
		if ($arg === 0) {
			return array_combine($rus, $lat);
		}
		else {
			return array_combine($lat, $rus);
		}
	}
	/**
	 * Функция перевода текста с кириллицы в транслит
	 *
	 * @param string $str
	 * @return string
	 */
	public function ru2lat($str) {
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
	 */
	public function lat2ru($str) {
		$rus = array('ё','ж','ц','ч','ш','щ','ю','я','Ё','Ж','Ц','Ч','Ш','Щ','Ю','Я');
		$lat = array('yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA');
		$str = str_replace($lat, $rus, $str);
		$str = strtr($str, "ABVGDEZIJKLMNOPRSTUFH#I'Eabvgdezijklmnoprstufh#i'e", "АБВГДЕЗИЙКЛМНОПРСТУФХЪЫЬЭабвгдезийклмнопрстуфхъыьэ");
		return ($str);
	}
	/**
	 * Функция перевода текста с кириллицы в транслит sfb441
	 *
	 * @param string $str
	 * @return string
	 */
	public function ru2lat_mb($str) {
		$arr = $this->private_engine_mb(0);
		// преобразуем символы, в данном случае функция вызывается
		// с двумя аргументами и имеет синтаксис: string strtr ( string str, array replace_pairs )
		return strtr($str, $arr);
	}
	/**
	 * Функция перевода текста с транслита в кириллицу sfb441
	 *
	 * @param string $str
	 * @return string
	 */
	public function lat2ru_mb($str) {
		$arr = $this->private_engine_mb(1);
		// преобразуем символы, в данном случае функция вызывается
		// с двумя аргументами и имеет синтаксис: string strtr ( string str, array replace_pairs )
		return strtr($str, $arr);
	}
	/**
     * Функция переводит n-байт в строку в удобном для чтения виде
     *
     * @param int $size
     * @return int
     */
	public function filesize2nat($size) {
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
	/**
	 * Возвращает строку 'раз' / 'раза' в зависимости от переданного аргумента
	 *
	 * @param int $number
	 * @return string
	 */
	public function quantity_ending($number) {
		$number = intval($number);
		// последняя цифра
		$n = $number % 10;
		// две последних цифры
		$m = $number % 100;
		// если заканчивается на 2,3,4, но не на 12,13,14 возвращаем строку 'раза'
		if((($n == 2) || ($n == 3) || ($n == 4))
		&& (($m != 12) || ($m != 13) || ($m != 14))) {
			return 'раза';
		}
		// иначе возвращаем строку 'раз'
		else {
			return 'раз';
		}
	}
}
?>