<?php
/**
 * ������ �� ����������� ������
 *
 * http://www.php.su/articles/?cat=protocols&page=012
 */
// Expires - ������ ���� ��������� ����� �������� ���������. ������� �� � �������
// ���������� ������ ��� ��� ������ ��������.
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");// Date in the past

// Last-Modified - ���� ��������� ��������� �����������. ���� ��������� ������ ���
// ����������� �������. Apache �������� ��� ���� ��������� ���� Date ��� �����������
// ������������ �������, � ��� ����� ��� ������� ���������� SSI.
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified

// HTTP/1.1
// Cache-control: no-cache - ���������� ���. �������� no-cache ���������� ������ ���
// ������ ��������. ��� ������ ��������� HTTP/1.0 ��������� "Pragma: no-cache".
header("Cache-Control: no-store, no-cache, must-revalidate ");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");
?>