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
require_once '../engine/releases/config.php';
require_once '../engine/releases/lib.php';
require_once '../engine/releases/template.php';

// засекаем время начала выполнения сценария
$startTime = fulltime();

// <head>
$myClassName = '';
if(isset($_COOKIE['className'])) {
	$myClassName = 'class="' . $_COOKIE['className'] . '"';
}
$head_content  = '';
$head_content .= include_shortcut_icon('/img/favicon.ico');
$head_content .= include_stylesheet('../engine/common/styles.css', '../engine/releases/styles.css');
$head_content .= include_javascript('
<script type="text/javascript">
//<![CDATA[
	var engineReleasesLoad = false;
	var engineCommonLoad = false;
//]]>
</script>', '../engine/common/script.js', '../engine/releases/script.js');
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

<div class="copy">
Пользоваться программами и модулями можно абсолютно бесплатно при условиях:
<ul>
<li><span>Если вы обнаружили ошибки в исходном коде или в работе программ и модулей, постараетесь мне отписаться.</span></li>
<li><span>Вы не будете выдавать где-либо программы и модули как свою разработку.</span></li>
<li><span>Автор не дает никаких гарантий и не несет никакой ответственности за возможный ущерб и убытки любого рода.</span></li>
</ul>
</div>

<!-- Программа Банк -->
<div class="border">
<div class="section_title">
<script type="text/javascript">
//<![CDATA[
document.write('<span onclick="treechange(\'<?php print($idbuffer[] = $id = idgen()); ?>\', this);"><b class="tree">+</b><span style="cursor: pointer;"> Программа Банк v 1.0</span></span>');
//]]>
</script>
<noscript>Программа Банк v 1.0</noscript>
</div>
<div align="justify" id="<?php print($id); ?>">
Программа предназначена для расчета удорожания товара и ежемесячного платежа при условиях:
<ul>
<li><span>фиксированной процентной ставки по кредиту.</span></li>
<li><span>проценты начисляются ежемесячно на оставшуюся часть кредита (таким образом, в составе ежемесячного платежа<br /> уменьшается процентная часть и соответственно увеличивается часть идущая в погашение основного долга).</span></li>
<li><span>комиссия за обслуживание начисляется ежемесячно от первоначальной суммы кредита (т.е. является постоянной величиной).</span></li>
<li><span>погашение кредита осуществляется фиксированными, т.е. одинаковыми по размеру выплатами.</span></li>
<li><span>ежемесячный платеж включает в себя проценты за пользование кредитом, комиссию за расчетное обслуживание, часть основного долга по кредиту.</span></li>
</ul>
<br />
Формулы для расчета, используемые в программе:
<br />
&rho; = n &times; r / ( срок кредита в годах ),<br />
где r такая, что
<br />&sum; = ЕП &times; ( 1 / (1 + r) + 1 / (1 + r)&sup2; + . . . + 1 / (1 + r)<sup><small>n</small></sup> ),
<br />где<br />
&rho; - расчетная процентная ставка, в % годовых,<br />
ЕП - ежемесячный платеж,<br />
n - количество расчетных периодов (срок кредита),<br />
&sum; - сумма кредита.<br />
<ul>
<li><span>Программа Bank написана на JavaScript для ОС Windows.</span></li>
<li><span>Размер исполняемого файла 17Kb, расширение *.hta (HyperText Application).</span></li>
<li><span>Просмотреть исходный код программы можно, изменив расширение файла на *.txt.</span></li>
</ul>
</div>
<div class="download"><a href="../files/bank.zip">[ Скачать ]</a></div>
</div>
<!-- END Программа Банк --><br />

<!-- Модуль голосования -->
<a name="voting"></a>
<div class="border">
<div class="section_title">
<script type="text/javascript">
//<![CDATA[
document.write('<span onclick="treechange(\'<?php print($idbuffer[] = $id = idgen()); ?>\', this);"><b class="tree">+</b><span style="cursor: pointer;"> Модуль голосования v 1.0</span></span>');
//]]>
</script>
<noscript>Модуль голосования v 1.0</noscript>
</div>
<div align="justify" id="<?php print($id); ?>">
Требования:<ul>
   <li><span>PHP и расширение для формирования динамических изображений - GD.</span></li>
 </ul>
Возможности и особенности:
<ul>
   <li><span>Отображение результата в виде динамически создаваемого на PHP изображения гистограммы голосования.</span></li>
   <li><span>Защита от повторного голосования.</span></li>
   <li><span>Генерируемое изображение имеет прозрачный цвет фона и чересстрочный режим загрузки.</span></li>
   <li><span>Внешний вид изображения имеет множество настроек, например: размер выдаваемого изображения, расстояние между столбиками, размер шрифта, межстрочный интервал, и др. В результате их сочетания можно добиться нужного вам внешнего вида гистограммы.</span></li>
   <li><span>Весь код модуля снабжен подробными комментариями, все настройки находятся в файле конфигурации <code>config.php</code>.</span></li>
 </ul>
</div>
<div  class="download"><a href="../files/voting.zip">[ Скачать ]</a></div>
</div>
<!-- END Модуль голосования --><br />

<!-- Пакетный файл(batch file) для очистки папки temp в Windows -->
<a name="voting"></a>
<div class="border">
<div class="section_title">
<script type="text/javascript">
//<![CDATA[
document.write('<span onclick="treechange(\'<?php print($idbuffer[] = $id = idgen()); ?>\', this);"><b class="tree">+</b><span style="cursor: pointer;"> Пакетный файл (batch file) для очистки папки Temp в Windows</span></span>');
//]]>
</script>
<noscript>Пакетный файл (batch file) для очистки папки Temp в Windows</noscript>
</div>
<div align="justify" id="<?php print($id); ?>">
Требования:<ul>
   <li><span>Операционная система Windows.</span></li>
 </ul>
Возможности и особенности:
<ul>
   <li><span>Очистка содержимого временной папки Temp</span></li>
 </ul>
</div>
<div  class="download"><a href="../files/clearTmp.zip">[ Скачать ]</a></div>
</div>
<!-- END Модуль голосования --><br />

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
	<?php
		print("if(document.getElementById) {");
	for($i=0; $i<count($idbuffer); $i++) {
		print("document.getElementById('" . $idbuffer[$i] . "').style.display = 'none';\n");
	}
	print("}\n");
	?>
//]]>
</script>
</body>
</html>