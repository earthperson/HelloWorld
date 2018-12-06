<?php
class PLib_Common {
    public static function serialize($sql, $primary_key = null, $field = null) {
	    $sql = trim($sql);
		$res = mysql_query($sql);
		$arr = array();
		if(is_resource($res) and mysql_num_rows($res)) {
			while($rec = mysql_fetch_assoc($res)) {
				if(!is_null($primary_key)) {
					$arr[$rec[$primary_key]] = !is_null($field) ? $rec[$field] : $rec;
				}
				else {
					$arr[] = !is_null($field) ? $rec[$field] : $rec;
				}
			}
			mysql_free_result($res);
			if(preg_match("%LIMIT\s+1$%si", $sql)) {
			    return isset($arr[0]) ? $arr[0] : array();
			}
			else {
			    return $arr;
			}
		}
		else {
			return array();
		}
	}
	
	public static function xml_output($source) {
        if(is_array($source)) {
            foreach ($source as &$item) {
                $item = htmlspecialchars($item, ENT_QUOTES);
            }
            return $source;
        }
        else {
            return htmlspecialchars($source, ENT_QUOTES);
        }
    }
	
    public static function insert_after_key(&$input, $key, $haystack) {
        if(!(is_array($haystack) && is_array($input))) {
            return false;
        }
        $offset = 0;
        foreach ($input as $k => $v) {
            if($k === $key) { break; }
            $offset++;
        }
        $offset++;
        $input = array_merge(
                    array_slice($input,0,$offset,true),
                    $haystack,
                    array_slice($input,$offset,null,true)
                 );
    }
    
    public static function get_language_depended_field($rec, $base_field, $lng = null) {
		
	}
	
    public static function image($absolute_path, $alt = '', $absolute_default_path = '', $max_width = 0, $attributes = '', $host = false) {
		$alt = plain_text($alt);
		$server_path = $_SERVER['DOCUMENT_ROOT'].$absolute_path;
		$server_default_path = $_SERVER['DOCUMENT_ROOT'].$absolute_default_path;
		if(is_file($server_path)) {
			list($width, $height) = @getimagesize($server_path);
			if((int)$max_width > 0 && $width > $max_width) {
				$height = floor($height / ($width / $max_width));
				$width = $max_width;
			} 
			return "<img src='" . ($host ? "http://{$_SERVER['HTTP_HOST']}" : '') . "{$absolute_path}' alt='{$alt}' title='{$alt}' width='{$width}' height='{$height}' {$attributes} />";
		}
		else {
			if(!empty($absolute_default_path) && is_file($server_default_path)) {
				list($width, $height) = @getimagesize($server_default_path);
					if((int)$max_width > 0 && $width > $max_width) {
					$height = floor($height / ($width / $max_width));
					$width = $max_width;
				} 
				return "<img src='" . ($host ? "http://{$_SERVER['HTTP_HOST']}" : '') . "{$absolute_default_path}' alt='{$alt}' title='{$alt}' width='{$width}' height='{$height}' {$attributes} />";
			}
			else {
				return '';
			}
		}
	}
	
	public static function date_fmt($datetime, $time = true) {
		$datetime = (string)$datetime;
		$months = array(1=>'Янв','Фев','Мар','Апр','Мая','Июн','Июл','Авг','Сен','Окт','Ноя','Дек');
		if(preg_match('/^(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/', $datetime, $a)) {
			$str = "{$a[3]}-".@$months[(int)$a[2]]."-{$a[1]}";
			if($time) {
				$str .= " {$a[4]}:{$a[5]}:{$a[6]}";
			}
			return $str;
		}
		else {
			return $datetime;
		}
	}
	
    public static function ru2lat($str){
    	return strtr($str,array("а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"jo","ж"=>"zh","з"=>"z","и"=>"i","й"=>"jj","к"=>"k","л"=>"l","м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"kh","ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"'","ы"=>"y","ь"=>"'","э"=>"eh","ю"=>"yu","я"=>"ya","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D","Е"=>"E","Ё"=>"JO","Ж"=>"ZH","З"=>"Z","И"=>"I","Й"=>"JJ","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"KH","Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'","Ы"=>"Y","Ь"=>"'","Э"=>"EH","Ю"=>"YU","Я"=>"YA","'"=>"*"));
    }
	
    public static function currency_fmt($price, $unit=true, $currency='rur') {
        $price = (float) $price;
        $n = number_format($price, 2, '.', ' ');
        if($currency == 'rur') {
            $cfmt = $n.($unit ? ' руб.' : '');
        }
        elseif($currency == 'usd') {
            $cfmt = ($unit ? '$' : '').$n;
        }
        elseif($currency == 'eur') {
            $cfmt = ($unit ? '&euro;' : '').$n;
        }    
        else {
            $cfmt = "$n";
        }
        return "<span class='currency-fmt'>{$cfmt}</span>";
    }
	
    public static function translate($key, $lng = null) {
        return isset($GLOBALS['DICTIONARY'][$key][$lng]) ? $GLOBALS['DICTIONARY'][$key][$lng] : '???';
	}
	
    public static function get_query_string($ignore_key='', $append_ampersand_after = true) {
        $str = '';
        if(is_string($ignore_key)) {
        	$ignore_key = array($ignore_key);
        }
        foreach ($_GET as $key => $value) {
            if(in_array($key, $ignore_key)) { continue; } 
            if($key == 'action') { continue; }
            if(!empty($str)) $str .= '&amp;';
        	$str .= self::safe_get_input($key) . '=' . self::safe_get_input($value);
        	//$str .= urlencode($key) . '=' . urlencode($value);
        }
        return $str . (!empty($str) && $append_ampersand_after ? '&amp;' : '');
    }
    
    private static function safe_get_input($str) {
        return preg_match('%[^a-z0-9\. -]+%si', $str) ? '' : trim($str);
    }
}
?>