ErrorLog ����� ��� ������ ��������� �� �������
<?php
/**
 * ����� ��� ������ ��������� �� ������� � ��������� ������� � ���� � ����������
 * ������ ��� �������� ��������� ��� ��������� ��������. ���� �� ������ boolean
 * �������� location ��������� �� ������ ������������ �� ������� ��������, ��������
 * additional_param ������������, ��� �������������� ������������ ������������
 * ��������, ���� ������ ����������. �������������� �������� log_dir ��������� ����������
 * ��� �������� ��������� ���� � ���������� ������.
 *  
 * .error {
 *    color: maroon;
 *    font-family: monospace;
 *    font-size: 12px;
 *    border: 1px solid maroon;
 *    background: #fcf;
 *    padding: 2px;
 *    margin: 1px;
 *    margin-bottom: 4px;
 *    text-align: justify;
 *    cursor: default;
 *    z-index: 50;
 * }
 * .no_error {
 *    color: #363;
 *    font-family: monospace;
 *    font-size: 12px;
 *    border: 1px solid #363;
 *    background: #cf9;
 *    padding: 2px;
 *    margin: 1px;
 *    margin-bottom: 4px;
 *    text-align: center;
 *    cursor: default;
 * }
 * <?php
 * C������ ��������� ������ ��� ������ ��������� �� �������
 * $el = new ErrorLog([log_dir]);
 * ......
 * if() {
 *     $el->mysuccess(success_msg, [additional_param, [location]]);
 * }
 * else {
 *     $el->mylog(error_msg, [additional_param, [location]]);
 * }
 * ......
 * ����� ��������� �� ������
 * $el->showlog()
 * ?>
 * @copyright http://dmitry-ponomarev.ru
 */
class ErrorLog {
	private
	$err_d      = array('none', ''),
	$success_d  = array('none', ''),
	$log_dir,
	$e          = 'e',
	$s          = 's';
	private function logs($msg, $arg, $param, $location) {
		// ������� ���� � ���������� ������ - log file
		$lf = tempnam($this->log_dir, $arg);
		$lfh = fopen($lf, 'w');
		if($lfh) {
			fwrite($lfh, $msg);
			fclose($lfh);
		}
		if($location) {
			header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $arg . '=' . urlencode($lf) . $param);
			exit();
		}
		else {
			return '?' . $arg . '=' . urlencode($lf) . $param;
		}
	}
	private function readlogfile($arg, $file) {
		if($arg == $this->e) {$this->err_d[0] = 'block';}
		if($arg == $this->s) {$this->success_d[0] = 'block';}
		$buff = file_get_contents($file);
		if(strlen($buff) > 0) {
			if($arg == $this->e) {
				$this->err_d[1] = $buff;
			}
			if($arg == $this->s) {
				$this->success_d[1] = $buff;
			}
		}
		unlink($file);
		return true;
	}
	public function mylog($msg, $param = '', $location = true) {
		return $this->logs($msg, $this->e, $param, $location);
	}
	public function mysuccess($msg, $param = '', $location = true) {
		return $this->logs($msg, $this->s, $param, $location);
	}
	public function showlog() {
		if(isset($_GET[$this->e])) {
			if (is_readable(urldecode($_GET[$this->e]))) {
				$this->readlogfile($this->e, urldecode($_GET[$this->e]));
			}
		}
		if(isset($_GET[$this->s])) {
			if (is_readable(urldecode($_GET[$this->s]))) {
				$this->readlogfile($this->s, urldecode($_GET[$this->s]));
			}
		}
		$str = "<!-- showlog -->\n" . '<div id="error_id" class="error" style="display: ' . $this->err_d[0] . '; text-align: left;" onclick="this.style.display = \'none\';">' . "\n" . '<a href="#" class="b_x">[ X ]&nbsp;</a>' . "\n" . '<div><pre id="error_idinternal">&nbsp;' . $this->err_d[1] . '</pre></div></div>' . "\n";

		$str .= "<!-- showlog -->\n" . '<div id="no_error_id" class="no_error" style="display: ' . $this->success_d[0] . '; text-align: left;" onclick="this.style.display = \'none\';">' . "\n" . '<a href="#" class="b_x">[ X ]&nbsp;</a>' . "\n" . '<div><pre>&nbsp;' . $this->success_d[1] . '</pre></div></div>' . "\n";
		print $str;
	}
	public function __construct($dir = './') {
		$this->log_dir = $dir;
	}
}
?>