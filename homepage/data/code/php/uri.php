������ ��������� URI RFC3986
<?php
/**
 * ������ ��������� URI RFC3986
 * ^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?
 *  12            3  4          5       6  7        8 9
 * http://www.dmitry-ponomarev.ru/main/code.php?uri.php=show#anchor
 * 0. http://www.dmitry-ponomarev.ru/main/code.php?uri.php=show#anchor
 * 1. http:
 * 2. http                      - �����
 * 3. //www.dmitry-ponomarev.ru
 * 4. www.dmitry-ponomarev.ru    - ��������
 * 5. /main/code.php            - ����
 * 6. ?uri.php=show
 * 7. uri.php=show              - ������
 * 8. #anchor
 * 9. anchor                    - ��������
 * @copyright http://ru.wikipedia.org/wiki/URI
 * @copyright http://tools.ietf.org/html/rfc3986#page-50
 */
$pattern = '%^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?%';
$subject = 'http://www.dmitry-ponomarev.ru/main/code.php?uri.php=show#anchor';
$matches = array();
preg_match($pattern, $subject, $matches);
foreach($matches as $k => $v)  {
	print $k . '. ' . $v . "<br />\n";
}
?>