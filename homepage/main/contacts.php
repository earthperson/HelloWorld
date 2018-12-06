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
require_once '../engine/contacts/config.php';
require_once '../engine/contacts/lib.php';
require_once '../engine/contacts/template.php';

// засекаем время начала выполнения сценария
$startTime = fulltime();

// +-------------------------------------------------------------------------------------------+
// | Блок отправки почты                                                                       |
// +-------------------------------------------------------------------------------------------+

$error_name = '';
$error_msg = '';
$error_mail = '';
$mailsend = 'not_set';
$fatal_contacts_errors = false;
$contacts_temp_file = tmpfile();
// если нужно добавить запись
if( isset($_POST['contacts_submit']) ) {
	// если не произошла ошибка при передаче параметров скрипту
	if( isset($_POST['name'], $_POST['mail'], $_POST['msg']) ) {
		// проверяем, что хотя бы одно поле было заполнено
		if(!(str_empty($_POST['name']) && str_empty($_POST['mail']) && str_empty($_POST['msg']))) {
			// проверяем данные полей формы и получаем
			// проверенные данные в виде массива, если они ошибочные или true или false
			$contacts_arr = contacts_test($_POST['name'], $_POST['mail'], $_POST['msg'], $contacts_temp_file);
			if($contacts_arr === true) {
				// отправляем почту
				if(mymail(TITLE,
				'Имя: ' . $_POST['name'] . "\n" .
				'E-mail: ' . $_POST['mail'] . "\n" .
				'Сообщение: ' . $_POST['msg']))
				    $mailsend = 'send';
				else $mailsend = 'not_send';
				// загружаем страницу повторно
				header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?mail=' . $mailsend);
				exit();
			}
			elseif($contacts_arr == false)
			    $fatal_contacts_errors = true;
			else {
				$error_name = $contacts_arr[0];
				$error_mail = $contacts_arr[1];
				$error_msg = $contacts_arr[2];
			}			
		}
	}
	else
	    $fatal_contacts_errorscontacts_errors = true;
}

// +-------------------------------------------------------------------------------------------+

// <head>
$myClassName = '';
if(isset($_COOKIE['className'])) {
	$myClassName = 'class="' . $_COOKIE['className'] . '"';
}
$head_content  = '';
$head_content .= include_shortcut_icon('/img/favicon.ico');
$head_content .= include_stylesheet('../engine/common/styles.css', '../engine/contacts/styles.css');
$head_content .= include_javascript('
<script type="text/javascript">
//<![CDATA[
	var engineContactsLoad = false;
	var engineCommonLoad = false;
//]]>
</script>', '../engine/common/script.js', '../engine/contacts/script.js');
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

<div class="section_title" style="margin: 10px 30%;">Связаться со мной можно следующими способами:</div>

<div class="border" style="margin: 0 5% 5%; text-align: center;">
<div style="text-align: left; margin: auto; width: 300px;">
<ul>
<li>
<!-- ICQ -->
<div class="icq">
<?php
$icq = "370-725-770";
print("ICQ#&nbsp;");
for($i=0; $i < strlen($icq); $i++) {
	if($icq[$i] == '-')
	    print("-");
	else {
		if(($i % 2) == 0)
		    print("$icq[$i]");
		else
		    print('<span style="position: relative; top: 2px;">' . $icq[$i] . '</span>');
	}
}
print('&nbsp;<img src="http://web.icq.com/whitepages/online?icq=370725770&amp;img=5" alt="" title="мой статус - в сети / не в сети" border="0" width="18" height="18" />' . "\n");
?>
</div>
<!-- END ICQ -->
</li>
<li>
<!-- Form -->
<?php
// показать ошибки, если были
if ($contacts_errors = tmpfile_error_show($contacts_temp_file))
	print('<div class="error" onclick="this.style.display = \'none\';"><img src="../img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" />' . "\n" . $contacts_errors . "</div>\n");
if ($fatal_contacts_errors)
    print('<div class="error" onclick="this.style.display = \'none\';"><img src="../img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" />Ошибка при выполнении скрипта, попробуйте еще раз.</div>' ."\n");
if((isset($_GET['mail']) && ($_GET['mail'] == 'not_send')))
    print('<div class="error" onclick="this.style.display = \'none\';"><img src="../img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" />При отправке почты произошла ошибка, обратитесь к администратору сайта.</div>' ."\n");
if((isset($_GET['mail']) && ($_GET['mail'] == 'send')) && (!$fatal_contacts_errors) && (!$contacts_errors))
    print('<div class="no_error" onclick="this.style.display = \'none\';">Почта успешно отправлена.</div>' ."\n");
?>
<div id="contactserror" class="error" style="display: none;" onclick="this.style.display = 'none'; focusFirst();"><img src="../img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" /><span id="contactserrorinternal">&nbsp;</span></div>
<?php print(contacts_form($error_name, $error_mail, $error_msg)) ?>
<div><sup style="color: maroon;">*</sup><i style="font-size: 12px;"> - Обязательно для заполнения</i></div>
<!-- END Form -->
</li>
</ul>
</div>
<div>
<img src="http://maps.google.com/staticmap?size=512x512&maptype=roadmap&markers=59.841252,30.253169,bluea&zoom=10&key=ABQIAAAANd8_Sgxk6EeWkmDDIpY3xhQvZYCPnxYmJAI6NgOvF1n0C8L_4BTL5EVJnqaKA0AqbSUhE619HoWG4g" alt="" width="512" height="512" />
</div>
</div><!-- END <div class="border"> -->

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