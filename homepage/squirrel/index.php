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

// засекаем время начала выполнения сценария
$startTime = fulltime();

// <head>
$myClassName = '';
if(isset($_COOKIE['className'])) {
	$myClassName = 'class="' . $_COOKIE['className'] . '"';
}
$head_content  = '';
$head_content .= include_shortcut_icon('/img/favicon.ico');
$head_content .= include_stylesheet('../engine/common/styles.css');
$head_content .= include_javascript('
<script type="text/javascript">
//<![CDATA[
	var engineCommonLoad = false;
//]]>
</script>', '../engine/common/script.js');
print(include_head(TITLE . ' | Страница для Светы, чтобы она посмотрела про белку из ледникового периода ))))', $head_content));
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

<div class="section_title">Страница для Светы, чтобы она посмотрела про белку из ледникового периода ))))</div>
<div style="text-align: center;padding: 20px;">

<div class="section_title">Клип 1</div>
<div>
<object type="application/x-shockwave-flash" data="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" height="300" width="400">
<param name="bgcolor" value="#F5F5F5" />
<param name="allowFullScreen" value="true" />
<param name="allowScriptAccess" value="always" />
<param name="movie" value="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" />
<param name="FlashVars" value="way=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/gone_nutty.flv&amp;swf=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf&amp;w=400&amp;h=300&amp;pic=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/gone_nutty.png&amp;autoplay=0&amp;tools=1&amp;skin=white&amp;volume=70&amp;q=&amp;comment=" />
</object>
</div>

<div class="section_title" style="padding-top: 10px;">Клип 2</div>
<div>
<object type="application/x-shockwave-flash" data="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" height="300" width="400">
<param name="bgcolor" value="#F5F5F5" />
<param name="allowFullScreen" value="true" />
<param name="allowScriptAccess" value="always" />
<param name="movie" value="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" />
<param name="FlashVars" value="way=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/no_time_for_nuts.flv&amp;swf=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf&amp;w=400&amp;h=300&amp;pic=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/no_time_for_nuts.png&amp;autoplay=0&amp;tools=1&amp;skin=white&amp;volume=70&amp;q=&amp;comment=" />
</object>
</div>

<div class="section_title" style="padding-top: 10px;">Клип 3</div>
<div>
<object type="application/x-shockwave-flash" data="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" height="300" width="400">
<param name="bgcolor" value="#F5F5F5" />
<param name="allowFullScreen" value="true" />
<param name="allowScriptAccess" value="always" />
<param name="movie" value="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" />
<param name="FlashVars" value="way=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/clip3.flv&amp;swf=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf&amp;w=400&amp;h=300&amp;pic=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/clip3.png&amp;autoplay=0&amp;tools=1&amp;skin=white&amp;volume=70&amp;q=&amp;comment=" />
</object>
</div>

<div class="section_title" style="padding-top: 10px;">Клип 4</div>
<div>
<object type="application/x-shockwave-flash" data="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" height="300" width="400">
<param name="bgcolor" value="#F5F5F5" />
<param name="allowFullScreen" value="true" />
<param name="allowScriptAccess" value="always" />
<param name="movie" value="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" />
<param name="FlashVars" value="way=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/clip4.flv&amp;swf=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf&amp;w=400&amp;h=300&amp;pic=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/clip4.png&amp;autoplay=0&amp;tools=1&amp;skin=white&amp;volume=70&amp;q=&amp;comment=" />
</object>
</div>

<div class="section_title" style="padding-top: 10px;">Клип 5</div>
<div>
<object type="application/x-shockwave-flash" data="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" height="300" width="400">
<param name="bgcolor" value="#F5F5F5" />
<param name="allowFullScreen" value="true" />
<param name="allowScriptAccess" value="always" />
<param name="movie" value="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" />
<param name="FlashVars" value="way=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/clip5.flv&amp;swf=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf&amp;w=400&amp;h=300&amp;pic=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/clip5.png&amp;autoplay=0&amp;tools=1&amp;skin=white&amp;volume=70&amp;q=&amp;comment=" />
</object>
</div>

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