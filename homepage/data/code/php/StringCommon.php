StringCommon ����� ��� ������ �� ��������
<?php
class StringCommon {
	/**
     * str_empty($str)
     *
     * ���������, ��� ������ �� ������, �.�. �������� ����� �������
     * �������� �� �������� ������� ������������ [ \t\n\r\v]
     * \t - ���������, \n - ������� �� ����� ������, \r - ������� �������,
     * \v - ������������ ���������
     *
     * @param string $str
     * @return bool
     */
	public function str_empty($str) {
		$str = trim($str);
		if(strlen($str) > 0) return false;
		else return true;
	}
	/**
     * str_valid_name($str)
     *
     * ���������, ��� ������ �������� ������ ����� ��������-�������� �������,
     * ���� ������������� � ������.
     *
     * @param string $str
     * @return bool
     */
	public function str_valid_name($str) {
		$str = trim($str);
		if(preg_match('/^[a-z�-��-߸][\w�-��-߸ -]*$/i', $str)) {
			return true;
		}
		else return false;
	}
	/**
     * str_valid_mail($str)
     *
     * ���������, e-mail
     *
     * @param string $str
     * @return bool
     */
	public function str_valid_mail($str) {
		$str = trim($str);
		// ���. 480 ������� ����������� �� JavaScript
		if(preg_match('/[^@]+@(\w+\.)+\w+/', $str)) {
			return true;
		}
		else return false;
	}
	/**
     * str_valid_passwd($str)
     *
     * ���������, ������
     *
     * @param string $str
     * @return bool
     */
	public function str_valid_passwd($str) {
		$str = trim($str);
		if(preg_match('/^[\w�-��-߸ -]+$/i', $str)) {
			return true;
		}
		else return false;
	}
	/**
     * str_lencrlf(&$str, $maxlength)
     *
     * ������� ��� �������� ����� ������ �� ��������� ������� �������� ������, 
     * ���� ����� ������, ���������� ������ ����������, ������ �������,
     * ��������� ������ ���������� ������� ���������� true � �������� �� ������
     * ����������� ������, � ��������� ������ ���������� false.
     *
     * @param string $str
     * @param int $maxlength
     * @return bool
     */
	public function str_lencrlf(&$str, $maxlength = null) {
		// ������������ ���-�� �������� �������� ������ ���������,
		// ����� ��� ���� �������� ������� ��������� ��� UNIX
		$CR_count = substr_count($str, "\r");
		$LF_count = substr_count($str, "\n");
		$CRLF_count = $CR_count + $LF_count;
		if ( strlen($str) > ($maxlength + $CRLF_count) ) {
			// �������� \r[\n] �� ' (��� ����, ����� ��� ���� �������� ������� ��������� ��� UNIX)
			$string = trim($str);
			$string = preg_replace('/[\r\n]+/', "'", $str);
			// ��������� ������ �� ��������� �� CRLF � �������� �� � ���� �������
			$arr = explode("'", $string);
			// �������� ������ ��� �������� �����������
			$string = implode('', $arr);
			// ����������� � ������������ � ��������
			$string = substr($string, 0, $maxlength);
			// ��������������� �� ������� $arr ������� �������� ������
			for($i=0, $j=0, $newstr = ''; strlen($newstr) < strlen($string); $i++) {
				$buffer = substr($string, $j, strlen($arr[$i]));
				$newstr .= $buffer;
				$newarr[$i] = $buffer;
				$j += strlen($arr[$i]);
			}
			$newstr = implode("\r\n", $newarr);
			// ���� ������ ���� ���������
			if (strlen($newstr) != strlen($str)) {
				$str = $newstr;
				return true;
			}
		}
		return false;
	}
	/**
     * monthrename(&$monthname)
     * ����������� �������� ������
     *
     * @param string $monthname
     */
	public function monthrename(&$monthname) {
		$search = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		$replace = array('������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������');
		$monthname = str_replace($search, $replace, $monthname);
	}
	/**
     * str_format($arr, $param = false)
     *
     * ����������� ������, ���������� � �������� ��������� ������� (������ ����������), � ������
     * ��� ������ � ���� �� ������ - ������, ��� ������� �������� ������ �������� �� '
     * � ��� ����������� html ������� ��������. � ������ ������ ����������� ������� ����
     * � ������� H:m d F Y, ���� ������ �������� true, ����� ����� �������������
     * 
     * @param array $arr
     * @param bool $param
     * @return string
     */
	public function str_format($arr, $param = false, $num_CRLF = 8) {
		for($i=0, $n = count($arr); $i < $n; $i++) {
			$arr[$i] = trim($arr[$i]);
			$arr[$i] = str_replace('|', '&brvbar;', $arr[$i]);
			$arr[$i] = preg_replace('/\r{2,}/', "\r", $arr[$i]);
			$arr[$i] = preg_replace('/\n{2,}/', "\n", $arr[$i]);
			// ���� (1) ������������, ����� �� ��������� ����������� �������� ���������� ���������
			// ����� � �������, �� ���������� ���������� ����� ��� ���������� ���������
			// (������:
			// spam
			// ...
			// spam)
			// ������ ����� ��������� ��������� \n
			$LF_count = substr_count($arr[$i], "\n");
			// ���� ������ ���������, �� �������� ��������
			if($LF_count >= $num_CRLF) {
				// Windows
				$arr[$i] = str_replace("\r\n", ' ', $arr[$i]);
				// Unix
				$arr[$i] = str_replace("\n", ' ', $arr[$i]);
			}
			// END (1)
		}
		$str = implode('|', $arr);
		// ����������� ��������� ������� � �������, �.�. ��� ����� �������������� � ����������������� ������,
		// � �������� ��������� ����� �������� ������
		$str = str_replace("'", '"', $str);
		// ����������� ����������� ������� � html ��������, �� ������� ������������� ������� (ENT_QUOTES).
		$str = htmlspecialchars($str, ENT_QUOTES);
		// ������� ������������ ��������, ���� ��������� magic_quotes_gpc ��������
		if(get_magic_quotes_gpc()) $str = stripslashes($str);
		// �������� \r\n �� '
		$str = preg_replace('/[\r\n]+/', "'", $str);

		if($param) {
			$month = gmdate('F',  (time() + 3600 * 3));
			// ����������� �������� ������
			$this->monthrename($month);
			$timestamp = gmdate('H:i:s d ', (time() + 3600 * 3)) . $month . gmdate(' Y',  (time() + 3600 * 3)) . '|';
		}
		$str = $timestamp . $str . "\n";
		return $str;
	}
	public function str_deformat($str, $class = 'undefined', $wrap = 29) {
		// �������������� ������ �� ��������� �����
		$arr = explode('|', $str);
		$str = '';
		for($i = 0, $n = count($arr); $i<$n; $i++) {
			if($this->str_empty($arr[$i])) {$arr[$i] = '&nbsp;';}
			// �������� ���������� ��� html ��������, ����� ���
			// �� ����������� �������� wordwrap, �� ������� ��� ������� (ENT_QUOTES).
			$arr[$i] = html_entity_decode($arr[$i], ENT_QUOTES);
			// �������� ������� ������� ������ �������� �������� ������
			// (� ��������������� ������ ������������ ��������� ������� ')
			$arr[$i] = wordwrap($arr[$i], $wrap, "'", 1);
			// �������� ������� � html ��������, �� ��������� ������� �� �������������
			// (ENT_COMPAT)
			$arr[$i] = htmlspecialchars($arr[$i], ENT_COMPAT);
			// �������������� ������ �������� ������
			$arr[$i] = str_replace("'", '<br />', $arr[$i]);
			$str .= '<div class="' . $class . '">' . $arr[$i] . "</div>\n";
		}
		return $str;
	}
	/**
	 * ����� ������������ ������� �������������� sfb441
	 * http://www.sfb441.uni-tuebingen.de/b1/rus/translit.html
	 *
	 * @param unknown_type $arg
	 * @return unknown
	 */
	private function private_engine_mb($arg) {
		$rus = '�������������������������������������Ũ��������������������������';
		// ����������� ������ � ������ array str_split ( string string [, int split_length] )
		$rus = str_split($rus);
		$lat = array('a', 'b', 'v', 'g', 'd', 'e', 'oh', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'x', 'c', 'ch', 'sh', 'w', 'qh', 'y', 'q', 'eh', 'ju', 'ja', 'A', 'B', 'V', 'G', 'D', 'E', 'OH', 'ZH', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'X', 'C', 'CH', 'SH', 'W', 'QH', 'Y', 'Q', 'EH', 'JU', 'JA');
		// �������� ����� ������ array array_combine ( array keys, array values )
		if ($arg === 0) {
			return array_combine($rus, $lat);
		}
		else {
			return array_combine($lat, $rus);
		}
	}
	/**
	 * ������� �������� ������ � ��������� � ��������
	 *
	 * @param string $str
	 * @return string
	 */
	public function ru2lat($str) {
		$rus = array('�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�');
		$lat = array('yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA');
		$str = str_replace($rus, $lat, $str);
		$str = strtr($str, "��������������������������������������������������", "ABVGDEZIJKLMNOPRSTUFH#I'Eabvgdezijklmnoprstufh#i'e");
		return ($str);
	}
	/**
	 * ������� �������� ������ � ��������� � ���������
	 *
	 * @param string $str
	 * @return string
	 */
	public function lat2ru($str) {
		$rus = array('�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�');
		$lat = array('yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA');
		$str = str_replace($lat, $rus, $str);
		$str = strtr($str, "ABVGDEZIJKLMNOPRSTUFH#I'Eabvgdezijklmnoprstufh#i'e", "��������������������������������������������������");
		return ($str);
	}
	/**
	 * ������� �������� ������ � ��������� � �������� sfb441
	 *
	 * @param string $str
	 * @return string
	 */
	public function ru2lat_mb($str) {
		$arr = $this->private_engine_mb(0);
		// ����������� �������, � ������ ������ ������� ����������
		// � ����� ����������� � ����� ���������: string strtr ( string str, array replace_pairs )
		return strtr($str, $arr);
	}
	/**
	 * ������� �������� ������ � ��������� � ��������� sfb441
	 *
	 * @param string $str
	 * @return string
	 */
	public function lat2ru_mb($str) {
		$arr = $this->private_engine_mb(1);
		// ����������� �������, � ������ ������ ������� ����������
		// � ����� ����������� � ����� ���������: string strtr ( string str, array replace_pairs )
		return strtr($str, $arr);
	}
	/**
     * ������� ��������� n-���� � ������ � ������� ��� ������ ����
     *
     * @param int $size
     * @return int
     */
	public function filesize2nat($size) {
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
	/**
	 * ���������� ������ '���' / '����' � ����������� �� ����������� ���������
	 *
	 * @param int $number
	 * @return string
	 */
	public function quantity_ending($number) {
		$number = intval($number);
		// ��������� �����
		$n = $number % 10;
		// ��� ��������� �����
		$m = $number % 100;
		// ���� ������������� �� 2,3,4, �� �� �� 12,13,14 ���������� ������ '����'
		if((($n == 2) || ($n == 3) || ($n == 4))
		&& (($m != 12) || ($m != 13) || ($m != 14))) {
			return '����';
		}
		// ����� ���������� ������ '���'
		else {
			return '���';
		}
	}
}
?>