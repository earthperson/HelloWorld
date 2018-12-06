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
require_once '../engine/common/cache0.php';
require_once '../engine/config.php';
require_once '../engine/common/include_header.php';
require_once '../engine/common/lib.php';
require_once '../engine/common/template.php';

// подключаем модули страницы
require_once '../engine/reserve2/config.php';
require_once '../engine/reserve2/lib.php';
require_once '../engine/reserve2/template.php';

// засекаем время начала выполнения сценария
$startTime = fulltime();

// <head>
$myClassName = '';
if(isset($_COOKIE['className'])) {
	$myClassName = 'class="' . $_COOKIE['className'] . '"';
}
$head_content  = '';
$head_content .= include_shortcut_icon('/img/favicon.ico');
$head_content .= include_stylesheet('../engine/common/styles.css', '../engine/reserve/styles.css');
$head_content .= include_javascript('
<script type="text/javascript">
//<![CDATA[
	var engineReserveLoad = false;
	var engineCommonLoad = false;
//]]>
</script>', '../engine/common/script.js', '../engine/reserve/script.js');
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

<!-- Меню -->
<div class="selfmenu">
<span class="section_title">Навигация:&nbsp;&nbsp;&nbsp;</span>
<?php print(selfmenu($listPages)); ?>
</div>
<!-- END Меню -->

<div>
<img src="/img/desktop.png" alt="" width="" height="" />
</div>

</div><!-- END <div class="main"> -->
<?php
// отправляем содержимое буфера вывода и 
// выключаем буферизацию вывода
ob_end_flush();

// засекаем время окончания выполнения сценария
$endTime = fulltime();
// отнимаем от конечного начальное и получаем приблизительное время выполнения сценария
$time = $endTime - $startTime;
// информация для размышления
info($time);
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