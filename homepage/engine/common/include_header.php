<?php
// +-----------------------+
// | модуль html заголовка |
// +-----------------------+

function include_head ($title = 'some page', $head_content = '') {
	$str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<title>' . $title . '</title>
	<meta http-equiv="content-type" 
		content="text/html; charset=windows-1251" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="author" content="Пономарев Дмитрий" />
    <meta name="description" content="' . substr($title, 0, strpos($title, " |")) . '" />
    <meta name="keywords" content="' . substr($title, 0, strpos($title, " |")) . '" />' . "\n"
	. $head_content . '</head>' . "\n";
   return $str;
}

function include_shortcut_icon ($href = '/favicon.ico') {
	$str = "\t" . '<link rel="shortcut icon" href="' . $href . '" type="image/x-icon" />' . "\n";
	return $str;
}

function include_stylesheet () {
	$arg_num = func_num_args();
	if ($arg_num > 0) {
		$arg_list = func_get_args();
		$str = "\t" . '<style type="text/css" media="all">' . "\n";
		for($i = 0; $i < $arg_num; $i++) {
			if(strpos(ltrim($arg_list[$i]), '{') === false) {			
				$str .= "\t" . '@import "' . $arg_list[$i] . '";' . "\n";
			}
			else {
				$str .= "\t" . $arg_list[$i] . "\n";
			}
		}
		$str .= "\t" . '</style>' . "\n";
	}
	else {
		$str = false;
	}
	return $str;
}

function include_javascript () {
	$arg_num = func_num_args();
	if ($arg_num > 0) {
		$arg_list = func_get_args();
		$str = '';
		for($i = 0; $i < $arg_num; $i++) {
			if(substr(ltrim($arg_list[$i]), 0, 1) !== '<') {
				$str .= "\t" . '<script type="text/javascript" src="' . $arg_list[$i] . '"></script>' . "\n";
			}
			else {
				$str .= "\t" . $arg_list[$i] . "\n";
			}
		}
	}
	else {
		$str = false;
	}
	return $str;
}
?>