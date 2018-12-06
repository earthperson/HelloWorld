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
require_once '../engine/code/config.php';
require_once '../engine/code/lib.php';
require_once '../engine/code/template.php';

// засекаем время начала выполнения сценария
$startTime = fulltime();

// <head>
$myClassName = '';
if(isset($_COOKIE['className'])) {
	$myClassName = 'class="' . $_COOKIE['className'] . '"';
}
$head_content  = '';
$head_content .= include_shortcut_icon('/img/favicon.ico');
$head_content .= include_stylesheet('/engine/common/styles.css', '/engine/code/styles.css');
$head_content .= include_javascript('
<script type="text/javascript">
//<![CDATA[
	var engineCodeLoad = false;
	var engineCommonLoad = false;
//]]>
</script>', '../engine/common/script.js', '../engine/code/script.js');
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
<div style="margin-bottom: 10px;"><span class="section_title">О разделе </span><span style="font-size: 12px;">Здесь представлены классы и функции PHP и JavaScript, для решения часто возникающих задач.</span></div>
<?php
// получим список файлов в директории php в виде массива
$pagesarr = myscan_dir(CODE_FOLDER . '/php/');
print '<!-- PHP -->
<div class="section_title" style="color: black;">PHP (' . count($pagesarr) . ')</div>';
if(is_array($pagesarr)) {
	// навигация
	if(isset($_GET['pagephp'])) {
		if(is_numeric($_GET['pagephp'])) {
			$pagephp = intval($_GET['pagephp']);
		}
	}
	else {
		// если в GET запросе не указан номер страницы, выводим первую
		$pagephp = 1;
	}
	// общее кол-во страниц
	(PHP_LENGTH > 0) ? $countpages = ceil(count($pagesarr) / PHP_LENGTH) : $countpages = 0;
	if($countpages > 1) {
		// навигация по страницам
		if(isset($_GET['pagejs'])) {
			nav_page ($countpages, $pagephp, $_SERVER['PHP_SELF'] . '?pagejs=' . $_GET['pagejs'] . '&pagephp=', 10, '../img/pages.gif');
		}
		else {
			nav_page ($countpages, $pagephp, $_SERVER['PHP_SELF'] . '?pagephp=', 10, '../img/pages.gif');
		}
	}
	$begin = ($pagephp - 1) * PHP_LENGTH;
	(($begin + PHP_LENGTH) < count($pagesarr)) ? $end = $begin + PHP_LENGTH : $end = count($pagesarr);
	for($i = $begin; $i < $end; $i++) {
		$index = str_replace('.', '_', $pagesarr[$i]);
		if ( isset($_GET[$index]) and ($_GET[$index] == 'show') ) {
			code(CODE_FOLDER . '/php/' . $pagesarr[$i], true);
		}
		else {
			code(CODE_FOLDER . '/php/' . $pagesarr[$i], false);
		}
	}
	if($countpages > 1) {
		// навигация по страницам
		if(isset($_GET['pagejs'])) {
			nav_page ($countpages, $pagephp, $_SERVER['PHP_SELF'] . '?pagejs=' . $_GET['pagejs'] . '&pagephp=', 10, '../img/pages.gif');
		}
		else {
			nav_page ($countpages, $pagephp, $_SERVER['PHP_SELF'] . '?pagephp=', 10, '../img/pages.gif');
		}
	}
}
print "<br /><hr /><br />\n";
// получим список файлов в директории js в виде массива
$pagesarr = myscan_dir(CODE_FOLDER . '/js/');
print '<!-- JavaScript -->
<div class="section_title" style="color: black;">JavaScript (' . count($pagesarr) . ')</div>';
if(is_array($pagesarr)) {
	// навигация
	if(isset($_GET['pagejs'])) {
		if(is_numeric($_GET['pagejs'])) {
			$pagejs = intval($_GET['pagejs']);
		}
	}
	else {
		// если в GET запросе не указан номер страницы, выводим первую
		$pagejs = 1;
	}
	// общее кол-во страниц
	(JS_LENGTH > 0) ? $countpages = ceil(count($pagesarr) / JS_LENGTH) : $countpages = 0;
	if($countpages > 1) {
		// навигация по страницам
		if(isset($_GET['pagephp'])) {
			nav_page ($countpages, $pagejs, $_SERVER['PHP_SELF'] . '?pagephp=' . $_GET['pagephp'] . '&pagejs=', 10, '../img/pages.gif');
		}
		else {
			nav_page ($countpages, $pagejs, $_SERVER['PHP_SELF'] . '?pagejs=', 10, '../img/pages.gif');
		}
	}
	$begin = ($pagejs - 1) * JS_LENGTH;
	(($begin + JS_LENGTH) < count($pagesarr)) ? $end = $begin + JS_LENGTH : $end = count($pagesarr);
	for($i = $begin; $i < $end; $i++) {
		$index = str_replace('.', '_', $pagesarr[$i]);
		if ( isset($_GET[$index]) and ($_GET[$index] == 'show') ) {
			code(CODE_FOLDER . '/js/' . $pagesarr[$i], true);
		}
		else {
			code(CODE_FOLDER . '/js/' . $pagesarr[$i], false);
		}
	}
	if($countpages > 1) {
		// навигация по страницам
		if(isset($_GET['pagephp'])) {
			nav_page ($countpages, $pagejs, $_SERVER['PHP_SELF'] . '?pagephp=' . $_GET['pagephp'] . '&pagejs=', 10, '../img/pages.gif');
		}
		else {
			nav_page ($countpages, $pagejs, $_SERVER['PHP_SELF'] . '?pagejs=', 10, '../img/pages.gif');
		}
	}
}
?>
<br /><br />
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
//]]>
</script>
</body>
</html>