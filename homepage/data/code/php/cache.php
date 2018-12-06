cache0(), cache_period() ������� ���������� ���-������� ��������.
<?php
/**
 * �������� ���������� ��� �������������� �����������
 *  
 * @return null;
 * @copyright ����� �� "PHP5 ���������� �������������" ���. 778
 */
function cache0() {
	// Last-Modified - ���� ���������� ��������� �����������. ���� ��������� ������ ���
	// ����������� �������. Apache �������� ��� ���� ��������� ���� Date ��� �����������
	// ������������ �������, � ��� ����� ��� ������� ���������� SSI.
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	
	// Expires - ������ ���� ��������� ����� �������� ���������. ������� �� � �������
	// ���������� ������ ��� ��� ������ ��������.
	header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");// Date in the past	

	// HTTP/1.1
	// Cache-control: no-cache - ���������� ���. �������� no-cache ���������� ������ ���
	// ������ ��������. ��� ������ ��������� HTTP/1.0 ��������� "Pragma: no-cache".
	header("Cache-Control: no-store, no-cache, must-revalidate ");
	header("Cache-Control: post-check=0, pre-check=0", false);

	// HTTP/1.0
	header("Pragma: no-cache");
	return null;
}
/**
 * �������� ���������� ��� ������������� �����������
 * 
 * @param int $seconds
 * @return null;
 * @copyright ����� �� "PHP5 ���������� �������������" ���. 779
 */
function cache_period($seconds = 86400) {
	// ����� ��������� ����������� ������������ ��� ��������� ����� ����������� �����
	$lastModified = filemtime(__FILE__) + date('Z');
	header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModified) . " GMT");
	
	// Expires - ������ ���� ��������� ����� �������� ���������.
	$expires = time() + $seconds;
	header("Expires: " . gmdate("D, d M Y H:i:s", $expires) . " GMT");
    
	// �������� ���-������ � ����������� ������������� $seconds ������
	header("Cache-Control: max-age=" . $seconds);
	return null;
}
?>