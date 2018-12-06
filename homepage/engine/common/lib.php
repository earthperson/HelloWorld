<?php
function idgen() {
	static $id = 'phpidgenerator0';
	return $id++;
}
/**
 * fulltime()
 * ���������� ����� � ������� '���, �����'
 *
 * @return float
 */
function fulltime() {
	// ��������� ������� �����
	$time = microtime();
	// �������� ������� � ������������
	list($usec, $sec) = explode(' ', $time);
	// ���������� ��������� �����
	return ( (float)$sec + (float)$usec );
}

/**
 * cookie_check($param = '')
 * ��������� �������� �� cookie, ��� $param ���������� �������������,
 * ���� cookie ���������, � �� �������� ������������ ���������, ������������ ������� GET.
 * ��� ����, ����� �� �� ��������, ���������� ������������ �������� ��� ����������� cookie,
 * �� ������������� ������� ����������� ��������� ���:
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
		// ������������� cookie � ������ 'test'
		setcookie('cookie', 'test');
		header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?cookie=test' . $param);
		exit;
	}
	else {
		if( !isset($_COOKIE['cookie']) ) {
			//cookie ���������
			return false;
		}
		else {
			//cookie ��������
			return true;
		}
	}
}
/**
 * monthrename_func(&$monthname)
 * ����������� �������� ������
 *
 * @param string $monthname
 */
function monthrename(&$monthname) {
	$search = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$replace = array('������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������');
	$monthname = str_replace($search, $replace, $monthname);
}

/**
 * is_admin($passwd, $name)
 * ��������� �������� �� ������������ ��������������� �� ��������� �������� ����������
 * ���������� $sess � ������ ��������� ����������� ������ � ������ ������������
 * � �������� ������� ��������� ����������� MD5 ��� ������ ������
 * ��������� ������� ���������������� ������ � ������� ������� session_start(),
 * �.�., � ���� ������ � ������� ������ ��������� ���� ������ � �������� ������ �� ���������.
 * ���� $_GET['admin'] = 'dstr' ���������� false � ������� ���������� ������;
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
 * ���������� ������, ���������� ������ ���������� � ����.
 * ���� ������ �������� ������������� �����, ������ ����������� � ����� �����, ����� � ������ �����.
 * ��� ����� �������� ������� ����������
 * 
 * ���� ������, ���������� ������ ����������, �� ������������� �������� �������� ������, �� �����������
 * 
 * @param string $str
 * @param int $param
 * @param string $filename
 * @return bool
 * 
 */
function add($str, $param, $filename) {
	// �������� ������ ��� �������
	if( $str[strlen($str) - 1] != "\n" )
	$str .= "\n";
	// �������� ���������� ����� � ������
	$arr = @file($filename) or $arr = array();
	// ���� ������ �������� �������������
	if ($param > 0) {
		array_unshift($arr, $str);
	}
	// ���� ������ �������� �������������
	else {
		$arr[] = $str;
	}
	// ��������� ���� ��� ������
	$handler = @fopen($filename, 'w');
	if($handler) {
		// ���������� ������ � ����
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
 * ������� ������ � �������� ���������� ������ ����������
 * �� ����� � ������, ���������� ������ ����������,
 * �������������� ��������� �������� ��������� ��� �����, ���� ������������
 * ��������� �� ������
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
				error_log('������! ����: ' . __FILE__ . ' �������: ' . __FUNCTION__ . ' ������: ' . __LINE__ . "<br />\n", 3, $filelog);
			}
		}
	}
	else {
		error_log('������! ����: ' . __FILE__ . ' �������: ' . __FUNCTION__ . ' ������: ' . __LINE__ . "<br />\n", 3, $filelog);
	}
}

/**
 * str_empty($str)
 *
 * ���������, ��� ������ �� ������, �.�. �������� ����� �������
 * �������� �� �������� ������� ������������ [ \t\n\r\f\v]
 * \t - ���������, \n - ������� �� ����� ������, \r - ������� �������,
 * \f - ������ ��������, \v - ������������ ���������
 *
 * @param string $str
 * @return bool
 */
function str_empty($str) {
	// ���������, ��� ������ ������������� ����������� ���������
	if(preg_match('/\S/', $str)) return false;
	else return true;
}

/**
 * str_valid_name($str)
 *
 * ���������, ��� ������ �������� ������ ����� ��������-�������� �������,
 * ���� ������������� � ������.
 *
 * @param string $str
 * @return bool
 */
function str_valid_name($str) {
	// ���������, ��� ������ ������������� ����������� ���������
	$x = preg_match('/^[a-z�-��-߸][\w�-��-߸ -]*$/i', $str);
	if($x === 0) return false;
	else return true;
}

/**
 * str_valid_mail($str)
 *
 * ���������, e-mail
 *
 * @param string $str
 * @return bool
 */
function str_valid_mail($str) {
	// ���������, ��� ������ ������������� ����������� ���������
	// ���. 480 ������� ����������� �� javascript
	$x = preg_match('/[^@]+@(\w+\.)+\w+/', $str);
	if($x === 0) return false;
	else return true;
}

/**
 * str_format($arr, $param = false, $funcname = null)
 *
 * ����������� ������, ���������� � �������� ��������� ������� (������ ����������), � ������
 * ��� ������ � ���� �� ������ - ������, ��� ������� �������� ������ �������� �� '
 * � ��� ����������� html ������� ��������. � ������ ������ ����������� ������� ����
 * � ������� H:m d F Y, ���� ������ �������� true, ����� ����� �������������, ��� �������� ������� ���������� ������� �����������
 * (��� ������ ���������� �������� �� ������).  
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
		// ���� (1) ������������, ����� �� ��������� ����������� �������� ���������� ���������
		// ����� � �������, �� ���������� ���������� ����� ��� ���������� ���������
		// (������:
		// spam
		// ...
		// spam)
		// ������ ����� ��������� ��������� \n
		$LF_count = substr_count($arr[$i], "\n");
		// ���� ������ ���������, �� �������� ��������
		if($LF_count >= JOURNAL_CRLF) {
			// Windows
			$arr[$i] = str_replace("\r\n", ' ', $arr[$i]);
			// Unix
			$arr[$i] = str_replace("\n", ' ', $arr[$i]);
		}
		// END (1)
	}
	$str = implode('|', $arr);
	// ����������� ��������� ������� � �������, �.�. ��� ����� �������������� � ����������������� ������,
	// � �������� ��������� ����� �������� ������
	$str = str_replace("'", '"', $str);
	// ����������� ����������� ������� � html ��������, �� ������� ������������� ������� (ENT_QUOTES).
	$str = htmlspecialchars($str, ENT_QUOTES);
	// ������� ������������ ��������, ���� ��������� magic_quotes_gpc ��������
	if(get_magic_quotes_gpc()) $str = stripslashes($str);
	// �������� \r\n �� '
	$str = preg_replace('/[\r\n]+/', "'", $str);

	if($param) {
		$month = gmdate('F', (time() + 3600 * 3));
		// ����������� �������� ������
		// call_user_func_array �������� ���������������� ������� � �������� ����������
		if($funcname) call_user_func_array($funcname, array(&$month));
		$timestamp = gmdate('H:i d ', (time() + 3600 * 3)) . $month . gmdate(' Y', (time() + 3600 * 3)) . '|';
	}
	$str = $timestamp . $str;
	return $str;
}

/* ������� ������ ��������� �� ��������� */
// ����� - http://niko.net.ru/
function nav_page(
$count,    //����� ���-�� �������
$num_page, //����� ������� ��������
$url,      //����� URL ��� ������ �� �������� (� ���� ����������� ����� ��������)
$page_nav, //������� ������ �� �������� �������� ������������
$img
) {

	$begin_loop=1; //��������� �������� � �����
	$end_loop=$count; //�������� �������� � �����
	$size = getimagesize($img);
	print('<div class="nav_page">[ �������� (' . $count . '):&nbsp;<img src="' . $img . '" ' . $size[3] . ' alt="" />');
	if ($num_page>$count or $num_page<1) $num_page=1; //�������� �� ������������ ������ ������� ��������

	//����� � ������� ��� ��� ����� ���������, �������� ����� �� ������� ����
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
 * ������� ��� �������� ����� ������ �� ��������� ������� �������� ������, 
 * ���� ����� ������, ���������� ������ ����������, ������ �������,
 * ��������� ������ ���������� ������� ���������� true � �������� �� ������
 * ����������� ������, � ��������� ������ ���������� false.
 *
 * @param string $str
 * @param int $maxlength
 * @return bool
 */
function str_cutwcrlf(&$str, $maxlength) {
	// ������������ ���-�� �������� �������� ������ ���������,
	// ����� ��� ���� �������� ������� ��������� ��� UNIX
	$CR_count = substr_count($str, "\r");
	$LF_count = substr_count($str, "\n");
	$CRLF_count = $CR_count + $LF_count;
	if ( strlen($str) > ($maxlength + $CRLF_count) ) {
		// �������� \r[\n] �� ' (��� ����, ����� ��� ���� �������� ������� ��������� ��� UNIX)
		$string = trim($str);
		$string = preg_replace('/[\r\n]+/', "'", $str);
		// ��������� ������ �� ��������� �� CRLF � �������� �� � ���� �������
		$arr = explode("'", $string);
		// �������� ������ ��� �������� �����������
		$string = implode('', $arr);
		// ����������� � ������������ � ��������
		$string = substr($string, 0, $maxlength);
		// ��������������� �� ������� $arr ������� �������� ������
		for($i=0, $j=0, $newstr = ''; strlen($newstr) < strlen($string); $i++) {
			$buffer = substr($string, $j, strlen($arr[$i]));
			$newstr .= $buffer;
			$newarr[$i] = $buffer;
			$j += strlen($arr[$i]);
		}
		$newstr = implode("\r\n", $newarr);
		// ���� ������ ���� ���������
		if (strlen($newstr) != strlen($str)) {
			$str = $newstr;
			return true;
		}
	}
	return false;
}

/**
 * ���� �� ������������
 *
 * @param array $pagearr
 * @return string
 */
function selfmenu($pagearr) {
	$linkarrbuffer = '';
	while(list($key, $value) = each($pagearr)) {
		// �� ������� �������� �� ������
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
 * ���� �� ������� ��������
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
 * ������� ��������� ���������� ���������� �����, ��������� �� ���� ����������� � ��������� �������
 * � ���������� ��� ���������� � ���� ������, � ����� ������� ��������� ����.
 * ���� ���� ������ ���������� false 
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
 * ���������� ������: Hour:Minutes:Seconds Day Month Year
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
	// ���� ������ ��� ������� �����
	$time_d = gmdate('j', $timestamp);
	// ������ ������������ ������
	$time_m = gmdate('F', $timestamp);
	monthrename($time_m);
	return gmdate('H:i:s ', $timestamp) . $time_d . ' ' . $time_m . gmdate(' Y', $timestamp);
}
function mymail($from, $body) {
	$recipient = ucfirst('Dmitry') . ' <ponomarev.host@gmail.com>';
	$subject = '��������� � �������� ��������';
	//���� �� ����� ����������� ������� ��� �������� ����� � koi8 - ��� ������������ ��������� ������.
	//��� �� ������� ���������� ��������������� ��������� �����, ������� ��������� ������ � ���������
	//win-1251 � �������� ����������� �������� �������� koi8 ���������. - �� ������
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



