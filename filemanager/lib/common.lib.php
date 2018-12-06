<?php
function databaseErrorHandler($message, $info) {
    if (!error_reporting()) return;
    print "SQL Error: $message<br><pre>"; 
    print_r($info);
    print "</pre>";
    exit();
}

if (!function_exists('json_encode'))
{
  function json_encode($a=false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}

function plain_text($source) {
	return strtr($source, array(
	            '<' => "&lt;",
	            '>' => "&gt;",
	            '"' => "&quot;",
	            "'" => "&#039;"
            ));
}

function ru2lat($str){
    return strtr($str,array("а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"jo","ж"=>"zh","з"=>"z","и"=>"i","й"=>"jj","к"=>"k","л"=>"l","м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"kh","ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"'","ы"=>"y","ь"=>"'","э"=>"eh","ю"=>"yu","я"=>"ya","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D","Е"=>"E","Ё"=>"JO","Ж"=>"ZH","З"=>"Z","И"=>"I","Й"=>"JJ","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"KH","Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'","Ы"=>"Y","Ь"=>"'","Э"=>"EH","Ю"=>"YU","Я"=>"YA","'"=>"*"));
}
?>