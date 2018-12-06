filesize2nat() ������� ��� �������� n-���� � ������ � ������� ��� ������ ���� (��. ����� ����� StringCommon)
<?php
/**
 * ������� ��������� n-���� � ������ � ������� ��� ������ ����
 *
 * @param int $size
 * @return int
 * @copyright http://dmitry-ponomarev.ru
 */
function filesize2nat($size) {
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
?>