<?php
/**
 * �������� ��������� ������ ��������� �����
 *
 * @return string
 */
function saying($file) {
	// �������� ���������� ����� � ������ 
	$arr = @file($file) or $arr = array();
	// ����� �� �������, ���� ���� ������
	if (count($arr) == 0) return false;
	// ������������� ��������� �����
	mt_srand(floatval(microtime()) * 100000000);
	// � ������� ������ � ����������� � ���� ������
	$str = trim($arr[ mt_rand(0, count($arr)-1) ]);
	// �������� �� ������ ��������� ������
	$arr = explode('|', $str);
	$str = '';
	if( isset($arr[0]) && ($arr[0] != '') ) $str .= '<div class="phrase"><q>' . $arr[0] . "</q></div>\n";
	if( isset($arr[1]) && ($arr[1] != '') ) $str .= '<div class="author">' . $arr[1] . "</div>\n";
	if( isset($arr[2]) && ($arr[2] != '') ) $str .= '<div class="description">' . $arr[2] . "</div>\n";
	// � ���������� ����������������� ������
	return $str;
}

/**
 * �������� ��� ������ ��������� �����
 *
 * @return string
 */
function admin_saying($file) {
	// �������� ���������� ����� � ������ 
	$arr = @file($file) or $arr = array();
	// ����� �� �������, ���� ���� ������
	if (count($arr) == 0) return false;
	// ������� � ������� �������� ������� (������ � �������) ������ ������� [ Delete ]
	for($i = 0; $i < count($arr); $i++) {
		$arr[$i] = '<a href="' . $_SERVER['PHP_SELF'] .  '?del=' . $i . '">[ Delete ]</a> ' . $arr[$i] . "<br />\n";
	}
	// ��������� ��� �������� ������� � ������
	$str = implode('', $arr);
	// � ��������� ����������������� ������
	return $str;
}
?>