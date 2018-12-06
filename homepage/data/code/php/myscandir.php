myscandir() �������� ��������������� ������ ������ �� ���������� ����
<?php
/**
 * �������� ������ ������ �� ���������� ����, ��������� �������� �� ���������������,
 * ���������� ��������������� (������������ �������� ����������, ��� ������� ������� ��������-�������� 
 * ����� ����� ��������� ��� ��������) �� ����� ������ ������, ��� true, ���� ������� ���� ��� false,
 * � ������ ������������� ������, ������� �� ������� �� ������������ natsort() - ��� ��������� �����
 * ��� �������� $arr
 *
 * @param string $dirname
 * @return mixed
 * @copyright http://dmitry-ponomarev.ru
 */
function myscandir($dirname) {
	if(is_dir($dirname)) {
		if($DH = opendir($dirname)) {
			while (false !== ($filename = readdir($DH))) {
				if($filename != '.' && $filename != '..') {
					if(is_file($dirname . $filename)) {
						$arr[] = $filename;
					}
				}
			}
			closedir($DH);
			if(isset($arr)) {
				function myscandir_cmp($a, $b) {
					return strnatcmp($a, $b);
				}
				usort($arr, 'myscandir_cmp');
				return $arr;
			}
			else {
				return true;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}
?>