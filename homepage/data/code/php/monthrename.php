monthrename() ����������� �������� ������ (��. ����� ����� StringCommon)
<?php
/**
  * monthrename(&$monthname)
  * ����������� �������� ������
  *
  * @param string $monthname
  * @copyright http://dmitry-ponomarev.ru
  */
function monthrename(&$monthname) {
	$search = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$replace = array('������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������');
	$monthname = str_replace($search, $replace, $monthname);
}
?>