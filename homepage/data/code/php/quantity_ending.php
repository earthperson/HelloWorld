quantity_ending() ���������� ������ &quot;���&quot; / &quot;����&quot; � ����������� �� ����������� ��������� (��. ����� ����� StringCommon)
<?php
/**
 * ���������� ������ '���' / '����' � ����������� �� ����������� ���������
 * @param int $number
 * @return string
 */
function quantity_ending($number) {
	$number = intval($number);
	// ��������� �����
	$n = $number % 10;
	// ��� ��������� �����
	$m = $number % 100;
	// ���� ������������� �� 2,3,4, �� �� �� 12,13,14 ���������� ������ '����'
	if((($n == 2) || ($n == 3) || ($n == 4))
	&& (($m != 12) || ($m != 13) || ($m != 14))) {
		return '����';
	}
	// ����� ���������� ������ '���'
	else {
		return '���';
	}
}
?>