ru2lat() lat2ru() ������� �������� ������ � ��������� � ��������� � ������� (��. ����� ����� StringCommon)
<?php
/**
  * ������� �������� ������ � ��������� � ��������
  *
  * @param string $str
  * @return string
  * @copyright http://dmitry-ponomarev.ru
  */
function ru2lat($str) {
	$rus = array('�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�');
	$lat = array('yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA');
	$str = str_replace($rus, $lat, $str);
	$str = strtr($str, "��������������������������������������������������", "ABVGDEZIJKLMNOPRSTUFH#I'Eabvgdezijklmnoprstufh#i'e");
	return ($str);
}
/**
  * ������� �������� ������ � ��������� � ���������
  *
  * @param string $str
  * @return string
  * @copyright http://dmitry-ponomarev.ru
  */
function lat2ru($str) {
	$rus = array('�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�');
	$lat = array('yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA');
	$str = str_replace($lat, $rus, $str);
	$str = strtr($str, "ABVGDEZIJKLMNOPRSTUFH#I'Eabvgdezijklmnoprstufh#i'e", "��������������������������������������������������");
	return ($str);
}
?>