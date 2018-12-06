<?php
// �������� ����������� ������
ob_start();

require_once '../common/lib.php';
require_once 'config.php';
require_once '../classes/ErrorLog.php';

// C������ ��������� ������ ��� ������ ��������� �� �������
$el = new ErrorLog();

/**
 * ���������� ������ ���������� ����������, ���� ��� ������ ��������, ����� ������ ������
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
		$str .= '�� ��������� ���� "Reference"!' . "\r\n";
	}
	if(!@exif_imagetype('../../' . $logo)) {
		$str .= '������ � ����� �����������!' . "\r\n";
	}
	if(str_empty($descr)) {
		$str .= '�� ��������� ���� "Description"!' . "\r\n";
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

// ���� ����� �������� �������
if(isset($_POST['logo_submit'])) {
	// ���� �� ��������� ������ ��� �������� ���������� �������
	if(isset($_POST['reference'], $_POST['logo'], $_POST['description_logo'], $_POST['logos_replace'])) {
		// ���������, ��� ���� �� ���� ���� ���� ���������
		if(!(str_empty($_POST['reference']) && str_empty($_POST['logo']) && str_empty($_POST['description_logo']))) {
			// �������� �����
			$checked_fields = check_fields(
			trim($_POST['reference']),
			trim($_POST['logo']),
			trim($_POST['description_logo']));
			if(is_array($checked_fields_str = $checked_fields)) {
				// ������������ ������ ��� ������ � ������ � ����
				if($formed_str = form_str($checked_fields)) {
					if(write_formed_str($formed_str)) {
						logo_sccss('������� ������� ��������!' . "\r\n" . $formed_str, $el);
					}
					else {
						logo_err('������ ��� ������ � ����!', $el);
					}
				}
				else {
					logo_err('������ ��� ������������ ������!', $el);
				}
			}
			else {
				logo_err($checked_fields_str, $el);
			}
		}
		else {
			logo_err('�� ��������� �� ���� ����!', $el);
		}
	}
	else {
		logo_err('������ ��� �������� ���������� �������!', $el);
	}
}

// ���� ����� ������� �������
if(isset($_GET['logodel'])) {
	// ���� ����� ��� �������� ������
	if(is_numeric($_GET['logodel'])) {
		if(logodel(intval($_GET['logodel']), $el)) {
			logo_sccss('������� ������� ������!', $el);
		}
		else {
			logo_err('������ ��� ��������!', $el);
		}
	}
	else {
		logo_err('������������ ��������!', $el);
	}
}
// ���������� ���������� ������ ������ �
// ��������� ����������� ������
ob_end_flush();
// +-------------------------------------------------------------------------------------------+
?>