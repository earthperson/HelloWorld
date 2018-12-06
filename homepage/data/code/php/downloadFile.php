downloadFile() �������, ������������ ���������� ����� ������� � ���������� �������
<?php
/**
 * ������� ��� ���������� ����� � ������� � ������������ �������,
 * ������� ��������������� ��������� ����������� ������� ���������� ��������
 *
 * @param string $realFilePath
 * @return bool
 * @copyright http://www.axofiber.org.ru/inside/active.site.htm
 */
function downloadFile($realFilePath) {
	// ������� ��������, ��� ���� ����������
	if(!file_exists($realFilePath)) {
		return false;
	}
	// ������� ����������� ���������� � �����
	$CLen = filesize($realFilePath);
	$filename = basename($realFilePath); // ������������� ���
	$file_extension = strtolower(substr(strrchr($filename, '.'), 1));
	// ������� �������� mime-�����
	$fileCType = 'application/octet-stream';
	$CTypes = array (
	'pdf' => 'application/pdf',
	'exe' => 'application/octet-stream',
	'zip' => 'application/x-zip-compressed',
	'rar' => 'application/x-rar-compressed',
	'doc' => 'application/msword',
	'xls' => 'application/vnd.ms-excel',
	'ppt' => 'application/vnd.ms-powerpoint',
	'gif' => 'image/gif',
	'png' => 'image/png',
	'jpe' => 'jpeg',
	'jpg' => 'image/jpg'
	);
	// ���� ���������� ���� � �������, �������� ��������������� mime ���,
	// ����� �������� �����
	if(isset($CTypes[$file_extension])) {
		$fileCType = $CTypes[$file_extension];
	}
	// ��������� HTTP-��������� ������
	// $_SERVER['HTTP_RANGE'] � ����� �����, c �������� ���� ����������� �������� ����������� �����.
	// ��������, ��� ��������� Range: bytes=range- ��� ������ ��������� ��� ���������� �������
	if(isset($_SERVER['HTTP_RANGE'])) {
		$matches = array();
		if(preg_match('/bytes=(\d+)-/', $_SERVER['HTTP_RANGE'], $matches)) {
			$rangePosition = intval($matches[1]);
			$newCLen = $CLen - $rangePosition;
			header ( 'HTTP/1.1 206 Partial content', true, 200 );
			header ( 'Status: 206 Partial content' );
			header ( 'Accept-Ranges: bytes');
			header ( 'Content-Range: bytes ' . $rangePosition . '-' . $CLen - 1 . '/' . $CLen);
			header ( 'Content-Length: ' . $newCLen );
			header ( 'Content-Disposition: attachment; filename="' . $filename . '"' );
			header ( 'Content-Description: File Transfer' );
			header ( 'Content-Type: ' . $fileCType );
			header ( 'Content-Transfer-Encoding: binary');
		}
		else {
			return false;
		}
	}
	else {
		header ( 'HTTP/1.1 200 OK', true, 200 );
		header ( 'Status: 200 OK' );
		header ( 'Accept-Ranges: bytes');
		header ( 'Content-Length: ' . $CLen );
		header ( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header ( 'Content-Description: File Transfer' );
		header ( 'Content-Type: ' . $fileCType );
		header ( 'Content-Transfer-Encoding: binary');
		$rangePosition = 0;
	}
	// Last-Modified - ���� ��������� ��������� �����������. ���� ��������� ������ ���
	// ����������� �������. Apache �������� ��� ���� ��������� ���� Date ��� �����������
	// ������������ �������, � ��� ����� ��� ������� ���������� SSI.
	header ( 'Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');// always modified
	// HTTP/1.1
	// Cache-control: no-cache - ���������� ���. �������� no-cache ���������� ������ ���
	// ������ ��������. ��� ������ ��������� HTTP/1.0 ��������� "Pragma: no-cache".
	header ( 'Cache-Control: no-store, no-cache, must-revalidate ');
	header ( 'Cache-Control: post-check=0, pre-check=0', false);
	// HTTP/1.0
	header ( 'Pragma: no-cache' );
	// ������ ���������� ������ �� ������� $rangePosition � ������ � ����� ���������� �����
	$handle = @fopen($realFilePath, 'rb');
	if ($handle) {
		fseek($handle, $rangePosition);
		while(!feof($handle) and !connection_status()) {
			print fread($handle, (1024 * 8));
		}
		return true;
	}
	else {
		return false;
	}
}
?>