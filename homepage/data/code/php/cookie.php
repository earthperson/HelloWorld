cookie_check() ������� �������� ����������� ������ cookie ��������
<?php
/**
 * cookie_check($param = '')
 * ��������� �������� �� cookie, ��� $param ���������� �������������,
 * ���� cookie ���������, � �� �������� ������������ ���������, ������������ ������� GET.
 * ��� ����, ����� �� �� ��������, ���������� ������������ �������� ��� ����������� cookie,
 * �� ������������� ������� ����������� ��������� ���:
 * 
 * $GETstr = '';
 * foreach($_GET as $key => $value) {
 *     $GETstr .= '&' . $key . '=' . $value;
 * }
 * if(cookie_check($GETstr)) {
 *     ....
 * }
 * 
 * @param string $param
 * @return bool
 * @copyright http://dmitry-ponomarev.ru
 */
function cookie_check($param = '') {
	if( !isset($_GET['cookie']) && !isset($_COOKIE['cookie']) ) {
		// ������������� cookie � ������ 'test'
		setcookie('cookie', 'test');
		header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?cookie=test' . $param);
		exit;
	}
	else {
		if( !isset($_COOKIE['cookie']) ) {
			//cookie ���������
			return false;
		}
		else {
			//cookie ��������
			return true;
		}
	}
}
?>