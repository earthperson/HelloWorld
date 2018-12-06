cookie_check() Функция проверки возможности записи cookie клиентом
<?php
/**
 * cookie_check($param = '')
 * Проверяет включены ли cookie, где $param необходимо устанавливать,
 * если cookie отключены, а на странице используются параметры, передаваемые методом GET.
 * Для того, чтобы их не потерять, вследствии перезагрузки страницы при отключенных cookie,
 * до использования функции используйте следующий код:
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
		// устанавливаем cookie с именем 'test'
		setcookie('cookie', 'test');
		header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?cookie=test' . $param);
		exit;
	}
	else {
		if( !isset($_COOKIE['cookie']) ) {
			//cookie выключены
			return false;
		}
		else {
			//cookie включены
			return true;
		}
	}
}
?>