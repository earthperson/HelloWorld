Разбор структуры URI RFC3986
<?php
/**
 * Разбор структуры URI RFC3986
 * ^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?
 *  12            3  4          5       6  7        8 9
 * http://www.dmitry-ponomarev.ru/main/code.php?uri.php=show#anchor
 * 0. http://www.dmitry-ponomarev.ru/main/code.php?uri.php=show#anchor
 * 1. http:
 * 2. http                      - схема
 * 3. //www.dmitry-ponomarev.ru
 * 4. www.dmitry-ponomarev.ru    - источник
 * 5. /main/code.php            - путь
 * 6. ?uri.php=show
 * 7. uri.php=show              - запрос
 * 8. #anchor
 * 9. anchor                    - фрагмент
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