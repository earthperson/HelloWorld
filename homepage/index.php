<?php
function callback($buffer) {
	return trim($buffer);
}
// включаем буферизацию вывода
ob_start("callback");

// инициализируем сессию
session_set_cookie_params(0); //Задаем время жизни куки
session_start();

// подключаем общие модули
//require_once 'engine/common/cache0.php';
require_once 'engine/config.php';
require_once 'engine/common/include_header.php';
require_once 'engine/common/lib.php';
require_once 'engine/common/template.php';

// подключаем модули страницы
require_once 'engine/index/config.php';
require_once 'engine/index/lib.php';
require_once 'engine/index/template.php';


// +-------------------------------------------------------------------------------------------+
// | Блок отображения заставки                                                                 |
// +-------------------------------------------------------------------------------------------+

// если была нажата кнопка OK
if(isset($_POST['thesubmit'])) {
	// галочка поставлена - записать cookie
	if(isset($_POST['checkbox'])) {
		setcookie('screensaver', 'no', time() + 0x12CC0300);
	}
	// удалить cookie
	else {
		setcookie('screensaver', '', time() - 3600);
	}
	// перезагружаем страницу
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?refresh=yes');
	// убеждаемся, что последующий код не выполняется при перенаправлении
	exit();
}

// если установлена строка cookie с именем screensaver и значением no
// то перейти на страницу /main.php
if(isset($_COOKIE['screensaver']) && $_COOKIE['screensaver'] === 'no') {
	// исключения
	if(!
	// если переход со страницы /main.php
	( (isset($_GET['show'])    && $_GET['show']    === 'yes')  ||
	// если страница перезагружалась функцией cookie_check()
	(  isset($_GET['cookie'])  && $_GET['cookie']  === 'test') ||
	// если была нажата кнопка OK
	isset($_POST['thesubmit'])                              ||
	// если страница перезагружалась после обработки нажатия кнопки OK
	(  isset($_GET['refresh']) && $_GET['refresh'] === 'yes') )) {
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/main.php');
		// убеждаемся, что последующий код не выполняется при перенаправлении
		exit();
	}
	// перенаправление не было выполнено, определяем переменную $checked,
	// которая указывает будет ли отмечено галочкой поле checkbox
	$checked = 'checked="checked" ';
}
else
$checked = '';

// +-------------------------------------------------------------------------------------------+

// +-------------------------------------------------------------------------------------------+
// | Блок добавления цитат, если администратор                                                 |
// +-------------------------------------------------------------------------------------------+

// если нужно добавить запись
if(isset($_POST['add'])) {
	// если не произошла ошибка при передаче параметров скрипту
	if(isset($_POST['phrase'], $_POST['description'], $_POST['author'])) {
		// проверяем, что хотя бы одно поле было заполнено
		if(!(str_empty($_POST['phrase']) && str_empty($_POST['description']) && str_empty($_POST['author']))) {
			// форматируем
			$saying_str = str_format(array($_POST['phrase'], $_POST['author'], $_POST['description']));
			// добавляем
			add($saying_str, 1, SAYING_DATA);
		}
	}
	// загружаем страницу повторно
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	exit();
}

// если нужно удалить запись, удаляем
if(isset($_GET['del'])) {
	// если число или числовая строка
	if(is_numeric($_GET['del'])) {
		del(intval($_GET['del']), SAYING_DATA);
	}
	// загружаем страницу повторно
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	exit();
}

// +-------------------------------------------------------------------------------------------+



// <head>
$myClassName = '';
if(isset($_COOKIE['className'])) {
	$myClassName = 'class="' . $_COOKIE['className'] . '"';
}
$head_content  = '';
if(!is_admin(ADMIN_PASSWD, ADMIN_NAME))
$head_content .= "\t" . '<meta http-equiv="refresh" content="' . SCREEN_TIME . '; url=http://' . $_SERVER['HTTP_HOST'] . '/main.php" />' . "\n";
$head_content .= include_shortcut_icon('/img/favicon.ico');
$head_content .= include_stylesheet('engine/common/styles.css', 'engine/index/styles.css');
$head_content .= include_javascript('
<script type="text/javascript">
//<![CDATA[
	var engineIndexLoad = false;
	var engineCommonLoad = false;
//]]>
</script>', 'engine/common/script.js', 'engine/index/script.js');
// получаем название страницы из массива $listPages на основании названия файла
if (isset($listPages[basename(__FILE__)])) {
	print(include_head(TITLE . ' | ' . $listPages[basename(__FILE__)], $head_content));
}
else {
	print(include_head(TITLE . ' | ?', $head_content));
}
// </head>
print('<body ' . $myClassName . '>' . "\n"); ?>
<div class="main">

<?php
// <!-- Вывод предупреждения, если поддержка JavaScript отключена -->
print(noscript());
?>

<!-- TITLE -->
<div><h1><?php print(TITLE); ?></h1></div>
<!-- End TITLE -->

<!-- [ Вы смотрите заставку, нажмите или подождите пять секунд ]-->
<div>
<a href="main.php" class="index" title="Добро пожаловать!" id="scroll">[ Вы смотрите заставку, нажмите или подождите пять секунд ) ]</a>
<script type="text/javascript">
//<![CDATA[
var length = document.getElementById('scroll').firstChild.length;
if(engineIndexLoad) setInterval("scroll( ')', 'scroll', 12, -2, length )", 300);
//]]>
</script>
</div>
<!-- END [ Вы смотрите заставку, нажмите или подождите пять секунд ]-->

<!-- Цитаты известных людей (saying) -->
<div class="saying">  
<?php
if(!is_admin(ADMIN_PASSWD, ADMIN_NAME))
print(saying(SAYING_DATA));
else {
	print(admin_saying(SAYING_DATA));
	print(saying_form());
}
?>
</div>
<!-- END Цитаты известных людей (saying) -->

<!-- Постараться не показывать заставку в следующий раз. -->
<div class="cookie">
<?php
$getstr = '';
foreach($_GET as $key => $value) {
	$getstr .= '&' . $key . '=' . $value;
}
if(cookie_check($getstr)) {
	// с javascript
	$str = '<script type="text/javascript">
//<![CDATA[' . "\n"
	. 'if(engineIndexLoad) {' . "\n"
	. 'extractCookies();' . "\n"
	. 'document.write(\'<form name="indexform" id="indexform" action="' . $_SERVER['PHP_SELF'] . '" method="post">\');' . "\n"
	. 'document.write(\'<input type="checkbox" name="checkbox1" ' . $checked . 'onclick="screensaver();" />\');' . "\n"
	. 'document.write(\'&nbsp;Постараться не показывать заставку в следующий раз.\');' . "\n"
	. 'document.write("</form>"); }' . "\n"
	. "//]]>\n</script>\n";
	// noscript
	$str .= '<noscript><form name="indexform" action="' . $_SERVER['PHP_SELF'] . '" method="post">' ."\n"
	. '<input type="checkbox" name="checkbox" value="1" ' . $checked . '/>'. "\n"
	. '<input type="submit" name="thesubmit" value="Ok" class="mybutton" title="Запомнить выбор" />' . "\n"
	. 'Постараться не показывать заставку в следующий раз.' . "\n"
	. '</form></noscript>' . "\n";
}
else {
	$str = '<form name="indexform" action="' . $_SERVER['PHP_SELF'] . '" method="post">' ."\n"
	. '<input type="checkbox" disabled="disabled" />' . "\n"
	. 'Постараться не показывать заставку в следующий раз.<br />' . "\n"
	. '<em>( Опция недоступна - поддержка cookies отключена! )</em>' . "\n"
	. '</form>' ."\n";
}
print($str);
?>
</div>
<!-- END Постараться не показывать заставку в следующий раз. -->

</div><!-- <div class="main"> -->
<?php
// отправляем содержимое буфера вывода и
// выключаем буферизацию вывода
ob_end_flush();
?>
<!-- Домашняя страница Пономарева Дмитрия. Версия 2.0 -->
<script type="text/javascript">
//<![CDATA[
if(engineCommonLoad) setInterval("timestamp('time')", 300);
if(engineCommonLoad) {window.onload = focusFirst;}
//]]>
</script>
</body>
</html>