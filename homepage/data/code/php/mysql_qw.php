mysql_qw() Простейшая функция для работы с placeholder-ами
<?php
// Простейшая функция для работы с placeholder-ами.
// @copyright Dmitry Koterov http://dklab.ru
// result-set mysql_qw($connection_id, $query, $arg1, $arg2, ...)
//  - или -
// result-set mysql_qw($query, $arg1, $arg2, ...)
// Функция выполняет запрос к MySQL через соединение, заданное как
// $connection_id (если не указано, то через последнее открытое).
// Параметр $query может содержать подстановочные знаки ?,
// вместо которых будут подставлены соответствующие значения
// аргументов $arg1, $arg2 и т.д. (по порядку), экранированные и
// заключенные в апострофы.
function mysql_qw() {
  // Получаем все аргументы функции.
  $args = func_get_args();
  // Если первый параметр имеет тип "ресурс", то это ID соединения.
  $conn = null;
  if (is_resource($args[0])) $conn = array_shift($args);
  // Формируем запрос по шаблону.
  $query = call_user_func_array("mysql_make_qw", $args);
  // Вызываем SQL-функцию.
  return $conn!==null? mysql_query($query, $conn) : mysql_query($query);
}

// string mysql_make_qw($query, $arg1, $arg2, ...)
// Данная функция формирует SQL-запрос по шаблону $query, 
// содержащему placeholder-ы.
function mysql_make_qw() {
  $args = func_get_args();
  // Получаем в $tmpl ССЫЛКУ на шаблон запроса.
  $tmpl =& $args[0];
  $tmpl = str_replace("%", "%%", $tmpl);
  $tmpl = str_replace("?", "%s", $tmpl);
  // После этого $args[0] также окажется измененным.
  // Теперь экранируем все аргументы, кроме первого.
  foreach ($args as $i=>$v) {
    if (!$i) continue;        // это шаблон
    if (is_int($v)) continue; // целые числа не нужно экранировать
    $args[$i] = "'".mysql_escape_string($v)."'";
  }
  // На всякий случай запорняем 20 последних аргументов недопустимыми
  // значениями, чтобы в случае, если число "?" превышает количество
  // параметров, выдавалась ошибка SQL-запроса (поможет при отладке).
  for ($i=$c=count($args)-1; $i<$c+20; $i++) 
    $args[$i+1] = "UNKNOWN_PLACEHOLDER_$i";
  // Формируем SQL-запрос.
  return call_user_func_array("sprintf", $args);
}
?>