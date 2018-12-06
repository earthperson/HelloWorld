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
require_once 'engine/common/cache0.php';
require_once 'engine/config.php';
require_once 'engine/common/include_header.php';
require_once 'engine/common/lib.php';
require_once 'engine/common/template.php';
require_once 'engine/classes/ErrorLog.php';

// подключаем модули страницы
require_once 'engine/main/config.php';
require_once 'engine/main/lib.php';
require_once 'engine/main/template.php';

// Cоздаем экземпляр класса для вывода сообщения об ошибках
$el = new ErrorLog();

// засекаем время начала выполнения сценари
$startTime = fulltime();

// +-------------------------------------------------------------------------------------------+
// | Блок учета посетителей                    
// +-------------------------------------------------------------------------------------------+
/*
 if(!(isset($_SESSION['visitor']) && ($_SESSION['visitor'] == 'is_visited'))) {
 $visitorF = @fopen(VISITOR_DATA, 'a');
 if($visitorF) {
 $month = gmdate('F',  (time() + 3600 * 3));
 // русификация названия месяца
 monthrename($month);
 fwrite($visitorF, gmdate('H:i:s d ', (time() + 3600 * 3)) . $month . gmdate(' Y\|', (time() + 3600 * 3)) . getenv('REMOTE_ADDR') . '|' . getenv('HTTP_USER_AGENT') . "\n");
 fclose($visitorF);
 $_SESSION['visitor'] = 'is_visited';
 }
 }
 */
// +-------------------------------------------------------------------------------------------+

// +-------------------------------------------------------------------------------------------+
// | Блок голосования               
// +-------------------------------------------------------------------------------------------+
// создаем временный файл ошибок при голосовании с уникальным именем, он автоматически удаляется
// после завершении работы скрипта или использования функции fclose()
$voting_temp_file = tmpfile();
if(isset($_POST['voting_submit'])) {
	if(isset($_POST['voting'])) {
		// если файл с контролем флуда не создан - пытаемся создать
		if(!file_exists(VOTING_FLUD)) {
			$createvotingfile = @fopen(VOTING_FLUD, 'w');
			if($createvotingfile) fclose($createvotingfile);
		}
		// удаление устаревших записей ip и времени голосования
		$votingfludarr = @file(VOTING_FLUD) or array();
		$votingfludarrcount = count($votingfludarr);
		for($i=0; $i<$votingfludarrcount; $i++) {
			$votingt = explode('|', $votingfludarr[$i]);
			if($votingt[1] + VOTING_FLUD_TIME <  time()) unset($votingfludarr[$i]);
		}
		$votingflud = @fopen(VOTING_FLUD, 'r+');
		if($votingflud) {
			// заблокировали файл на запись
			flock($votingflud, LOCK_EX);
			ftruncate($votingflud, 0); //очищаем все содержимое файла
			rewind($votingflud);
			fwrite($votingflud, implode('', $votingfludarr));
			// сняли блокировку (при закрытии снимается автоматически)
			fflush($votingflud); //сбрасываем буферы на диск
			flock($votingflud, LOCK_UN);
			fclose($votingflud);
		}
		// сортируем массив, для того, чобы сбросить ключи после удаления элементов массива
		// с помощью unset()
		sort($votingfludarr);

		// определяем, является ли данная запись флудом, в начале предположим, что не является
		// результат будует в переменной $votingflud
		$votingfludmean = false;
		for($i=0; $i<count($votingfludarr); $i++) {
			$votingip = explode('|', $votingfludarr[$i]);
			if($votingip[0] == getenv('REMOTE_ADDR')) $votingfludmean = true;
		}

		// записываем ip и время голосования, если файл не был создан - пытаемся создать
		$votingflud = @fopen(VOTING_FLUD, 'a+');
		if($votingflud) {
			// заблокировали файл на запись
			flock($votingflud, LOCK_EX);
			fwrite($votingflud, getenv('REMOTE_ADDR') . '|' . (string) time() . "\n");
			// сняли блокировку (при закрытии снимается автоматически)
			fflush($votingflud); //сбрасываем буферы на диск
			flock($votingflud, LOCK_UN);
			fclose($votingflud);
		}
		if(!$votingfludmean) {
			if(file_exists(VOTING_DATA)) {
				// открыли файл на чтение и запись
				$votingfile = @fopen(VOTING_DATA, 'r+');
				// заблокировали файл
				flock($votingfile, LOCK_EX);
				if($votingfile) {
					$userArgString = fread($votingfile, filesize(VOTING_DATA));
					$userArg = explode('|', $userArgString);
					if (count($userArg) < count($legendarr)) $userArg = array_fill(0, count($legendarr), 0);
					for($i=0; $i<count($userArg); $i++) {
						if($_POST['voting'] == (string) ($i + 1) ) $userArg[$i]++;
					}
				}
				rewind($votingfile);
				fwrite($votingfile, implode('|', $userArg));
				// сняли блокировку (при закрытии снимается автоматически)
				fflush($votingflud); //сбрасываем буферы на диск
				flock($votingfile, LOCK_UN);
				// и закрыли файл (при выходе закрывается автоматически)
				fclose($votingfile);
			}
			else {
				$votingfile = @fopen(VOTING_DATA, 'w');
				if($votingfile) {
					$userArg = array_fill(0, count($legendarr), 0);
					for($i=0; $i<count($userArg); $i++) {
						if($_POST['voting'] == (string) ($i + 1) ) $userArg[$i]++;
					}
					fwrite($votingfile, implode('|', $userArg));
					fclose($votingfile);
				}
			}
			// загружаем страницу повторно
			header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . votingGETstr());
			exit();
		}
		else {
			fwrite($voting_temp_file, '<div><img src="img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" /> Вы уже проголосовали, попробуйте через ' . ceil(VOTING_FLUD_TIME / 60) . ' мин.</div>');
		}
	}
}
// +-------------------------------------------------------------------------------------------+

// +-------------------------------------------------------------------------------------------+
// | Блок журнала посещений                    
// +-------------------------------------------------------------------------------------------+
$error_name = '';
$error_msg = '';
$fatal_journal_errors = false;
$journal_temp_file = tmpfile();
// если нужно добавить запись
if( isset($_POST['journal_submit']) ) {
	// если не произошла ошибка при передаче параметров скрипту
	if( isset($_POST['name'], $_POST['msg'], $_POST['spam']) ) {
		// проверяем, что хотя бы одно поле было заполнено
		if(!(str_empty($_POST['name']) && str_empty($_POST['msg']))) {
			if((trim($_POST['name']) == ADMIN_NAME) && (md5(trim($_POST['msg'])) == ADMIN_PASSWD)) {
				${ADMIN_NAME} = ADMIN_PASSWD;
				is_admin(ADMIN_PASSWD, ADMIN_NAME);
				header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
				exit();
			}
			else {
				// проверяем данные полей формы и получаем
				// проверенные данные в виде массива, если они ошибочные или true или false
				$journal_arr = journal_test($_POST['name'], $_POST['msg'], $_POST['spam'], $journal_temp_file);
				if($journal_arr === true) {
					// если администратор, преформатируем специальным образом
					$journal_arr = journal_admin_test($_POST['name'], $_POST['msg']);
					// форматируем
					$journal_str = str_format($journal_arr, true, 'monthrename');
					// удаляем последнюю (нижнюю) запись, если общее количество записей больше предела
					journal_delete_below_note();
					// добавляем
					add($journal_str, 1, JOURNAL_DATA);
					// загружаем страницу повторно
					header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
					exit();
				}
				elseif($journal_arr == false)
				$fatal_journal_errors = true;
				else {
					$error_name = $journal_arr[0];
					$error_msg = $journal_arr[1];
				}
			}
		}
	}
	else
	$fatal_journal_errors = true;
}
// если нужно удалить запись, удаляем
if(isset($_GET['journaldel'])) {
	// если число или числовая строка
	if(is_numeric($_GET['journaldel'])) {
		del(intval($_GET['journaldel']), JOURNAL_DATA);
	}
	// загружаем страницу повторно
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	exit();
}
// +-------------------------------------------------------------------------------------------+

// +-------------------------------------------------------------------------------------------+
// | Блок кратко о сайте                
// +-------------------------------------------------------------------------------------------+
// если нужно добавить запись
if(isset($_POST['description_submit'])) {
	// если не произошла ошибка при передаче параметров скрипту
	if(isset($_POST['description'])) {
		// удаляем экранирующие бэкслэши, если директива magic_quotes_gpc включена
		if(get_magic_quotes_gpc()) $newsstr = stripslashes($_POST['description']);
		else $newsstr = $_POST['description'];
		// добавляем
		$descriptionhandle = @fopen(DESCRIPTION_DATA, 'w');
		if($descriptionhandle) {
			fwrite($descriptionhandle, $newsstr);
			fclose($descriptionhandle);
		}
	}
	// загружаем страницу повторно
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?dm=1');
	exit();
}
// +-------------------------------------------------------------------------------------------+

// +-------------------------------------------------------------------------------------------+
// | Блок новостей сайта                 
// +-------------------------------------------------------------------------------------------+
// если нужно добавить запись
if(isset($_POST['news_submit'])) {
	// если не произошла ошибка при передаче параметров скрипту
	if(isset($_POST['news'])) {
		// проверяем, что хотя бы одно поле было заполнено
		if(!(str_empty($_POST['news']))) {
			// форматируем
			$saying_str = str_format(array($_POST['news']), true, 'monthrename');
			// добавляем
			add($saying_str, 1, NEWS_DATA);
			// добавляем в RSS-канал
			$month_n = date('F');
			// русификация названия месяца
			monthrename($month_n);
			$timestamp_n = date('H:i d ') . $month_n . date(' Y');
			if(get_magic_quotes_gpc()) {
			    $_POST['news'] = stripslashes($_POST['news']);
			}
			addRSS(htmlspecialchars($_POST['news']), $timestamp_n, 'rss/news.xml');
		}
	}
	// загружаем страницу повторно
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	exit();
}
// если нужно удалить запись, удаляем
if(isset($_GET['newsdel'])) {
	// если число или числовая строка
	if(is_numeric($_GET['newsdel'])) {
		del(intval($_GET['newsdel']), NEWS_DATA);
		delRSS(intval($_GET['newsdel']), 'rss/news.xml');
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
$head_content  = "\t" . '<link rel="alternate" type="application/rss+xml" title="RSS" href="http://' . $_SERVER['HTTP_HOST'] . '/rss/news.xml" />' . "\n";
$head_content .= include_shortcut_icon('/img/favicon.ico');
$head_content .= include_stylesheet('engine/common/styles.css', 'engine/main/styles.css');
$head_content .= include_javascript("
<script type=\"text/javascript\">
//<![CDATA[
var engineCommonLoad = false;
var engineMainLoad = false;
if(document.images) {
   var favorite = new Image(); favorite.src = 'img/favorite_on.gif';
   var home = new Image(); home.src = 'img/home_on.gif';
   var page = new Image(); page.src = 'img/page_on.gif';
}
/* Приветствие в зависимости от времени суток */
function writeGreeting() {
	var dateNow = new Date();
	var hh=parseInt(dateNow.getHours());
	if ( 4<=hh && hh<=11) {
		document.write('<h3>Доброе утро!</h3>'); 
	}
	else if (12<=hh && hh<=16) {
		document.write('<h3>Добрый день!</h3>');
	}
	else if (17<=hh && hh<=23) {
		document.write('<h3>Добрый вечер!</h3>');
	}
	else {
		document.write('<h3>Доброй ночи!</h3>');
	}
}
/* END Приветствие в зависимости от времени суток */
//]]>
</script>", 'engine/common/script.js', 'engine/main/script.js');
// получаем название страницы из массива $listPages на основании названия файла
if (isset($listPages[basename(__FILE__)])) {
	print(include_head(TITLE . ' | ' . $listPages[basename(__FILE__)], $head_content));
}
else {
	print(include_head(TITLE . ' | ?', $head_content));
}
// </head>
print('<body ' . $myClassName . '>' . "\n"); ?>
<!--LiveInternet counter-->
<script type="text/javascript"><!--
document.write("<img src='http://counter.yadro.ru/hit?r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' width=1 height=1 alt=''>")//--></script>
<!--/LiveInternet-->
<div class="scrs"><a href="/index.php?show=yes"
	title="Перейти на страницу-заставку">[ Заставка ]</a></div>
<div id="clock"><?php
// <!-- Вывод предупреждения, если поддержка JavaScript отключена -->
print(noscript());
?></div>
<!-- Приветствие в зависимости от времени суток -->
<script type="text/javascript">
//<![CDATA[
writeGreeting();
//]]>
</script>
<noscript>
<h3>Здравствуйте!</h3>
</noscript>
<!-- END Приветствие в зависимости от времени суток -->

<?php
// Удаление администратора
if(is_admin(ADMIN_PASSWD, ADMIN_NAME)) {
	print('<div><a href="' . $_SERVER['PHP_SELF'] . '?admin=dstr">[ Выход ]</a></div>' . "\n");
}
?>

<!-- div class="main" -->
<div class="main">

<table width="100%">
	<tr>

		<!-- c1 -->
		<td width="33%">
		<div class="border"><!-- Меню -->
		<div class="menu">
		<div class="section_title">Навигация</div>
		<?php print(mainmenu($listPages)); ?> <!-- Добавление в избранное -->
		<script type="text/javascript">
//<![CDATA[
if(engineMainLoad && (detectAgent() == 'MSIE' || detectAgent() == 'Gecko')) {
	document.write('<div><a href="#" class="favorite" title="Добавить страницу в избранное (закладки)" onclick="addBookmark(); return false;">&nbsp;</a></div>');

}
//]]>
</script> <!-- END Добавление в избранное --></div>
		<!-- END Меню --> <!-- Проекты -->
		<div class="projects">
		<div class="section_title">Сайты на dmitry-ponomarev.ru</div>
		<div class="submenulink"><span class="pr_sc">Test
		</span>Тестирование проектов.<a href="http://test.dmitry-ponomarev.ru"
			style="white-space: nowrap;" target="_blank">[ Перейти ]</a>
		</div>
		<div class="submenulink"><span class="pr_sc">Temp
		</span>Страничка, которая заменит эту.<a href="http://temp.dmitry-ponomarev.ru"
			style="white-space: nowrap;" target="_blank">[ Перейти ]</a>
		</div>
		</div>
		<!-- END Проекты --> <!-- Внешние ссылки -->
		<div class="links">
		<div class="section_title">Внешние ссылки</div>
		<div class="submenulink">Мне нравится слушать шведское Интернет-радио
		<a href="http://www.radioseven.se/"
			title="Нажать там кнопку LYSSNA NU!" target="_blank">[ Radioseven ]</a>
		</div>
		</div>
		<!-- END Внешние ссылки --> <br />

		<!-- Программное обеспечение -->
		<div class="section_title">Программное обеспечение</div>
		<?php
		// вывод сообщения об ошибке
		$el->showlog();
		// если в GET запросе не указан номер страницы, выводим первую
		$numlogos_page = 1;
		if(isset($_GET['logospage'])) {
			if(is_numeric($_GET['logospage'])) {
				$numlogos_page = intval($_GET['logospage']);
			}
		}
		$logosArr = parse_ini_file('data/logotypes.ini', true);
		if(isset($_GET['logos']) && ($_GET['logos'] == 'show') || is_admin(ADMIN_PASSWD, ADMIN_NAME)) {
			$mainlogosarr = mainlogosFull($logosArr);
			$mainlogosarr[1] = logos($mainlogosarr[1], $numlogos_page);
		}
		else {
			$mainlogosarr = mainlogos($logosArr);
		}
		$logosArrCount = count(parse_ini_file(LOGOS_DATA, true)) - 1;
		?>
		<div class="submenulink">Здесь в случайном порядке загружаются
		логотипы замечательных программ, которые я использую<span
			style="white-space: nowrap;"> (всего логотипов: <?php print($mainlogosarr[0]); ?>).</span></div>
			<?php
			if(is_admin(ADMIN_PASSWD, ADMIN_NAME)) {
				print(logos_form());
				// навигация по страницам
				if ($logosArrCount > LOGOS_COUNT) {
					nav_page (
					ceil($logosArrCount / LOGOS_COUNT), $numlogos_page,
					$_SERVER['PHP_SELF'] . logosGETstr(true),
					LOGOS_NAV_LINKS_COUNT, 'img/pages.gif'
					);
				}
				print($mainlogosarr[1]);
			}
			else {
				if(isset($_GET['logos']) && ($_GET['logos'] == 'show')) {
					print '<div style="margin-top: 2px;"><span class="smallcaps">Все логотипы: </span><a href="' . $_SERVER['PHP_SELF'] . '?logos=hidden' . logosGETstr(false) . '">[ Случайный логотип ]</a></div>' . "\n";
					if ($logosArrCount > LOGOS_COUNT) {
						nav_page (
						ceil($logosArrCount / LOGOS_COUNT), $numlogos_page,
						$_SERVER['PHP_SELF'] . logosGETstr(true),
						LOGOS_NAV_LINKS_COUNT, 'img/pages.gif'
						);
					}
				}
				print($mainlogosarr[1]);
				if(!(isset($_GET['logos']) && ($_GET['logos'] == 'show'))) {
					print '<div style="padding: 4px;"><a href="' . $_SERVER['PHP_SELF'] . '?logos=show' . logosGETstr(false) . '">[ Показать все логотипы ]</a></div>' . "\n";
				}
			}
			?> <!-- END Программное обеспечение -->
		<br />
		<!-- Цветовая схема -->
		<div class="section_title">Цветовая схема</div>
		<script type="text/javascript">
//<![CDATA[
if(engineCommonLoad) {
	// сразу установим cookie, чтобы проверить наличие их поддержки
	document.cookie = 'cookiesenabled=yes';
	extractCookies();
	if(cookies['cookiesenabled'] == 'yes') {
		if(engineMainLoad) {
			<?php print(colorscheme()); ?>
		}
	}
	else document.write('<em>( Опция недоступна - поддержка cookies отключена! )</em>');
}
//]]>
</script>
</div>
<div style="margin-top: 14px;">
<script type="text/javascript" type="text/javascript" src="/orphus/orphus.js"></script>
<a href="http://orphus.ru" id="orphus" target="_blank"><img alt="Orphus system" src="/orphus/orphus.gif" border="0" width="121" height="21" /></a>
</div>

		<noscript><em>( Опция недоступна - поддержка JavaScript отключена! )</em></noscript>
		<!-- END Цветовая схема --></td>
		<!-- END -->

		<!-- c2 -->
		<td>
		<div class="border"><!-- Кратко о сайте -->
		<div class="greeting">
		<div class="section_title">Кратко о сайте</div>
		<div style="font-size: 12px;"><?php
		if(is_admin(ADMIN_PASSWD, ADMIN_NAME)) {
			if(isset($_GET['dm'])) print('<div class="no_error" onclick="this.style.display = \'none\';">Файл успешно отредактирован!</div>' ."\n");
			print(description_form(description(DESCRIPTION_DATA)));
		}
		else print(description(DESCRIPTION_DATA));
		?></div>
		</div>
		<!-- END Кратко о сайте --> <!-- Новости сайта -->
		<div class="news">
		<div class="section_title">Новости сайта</div>
		<div><a href="rss/news.xml" class="rss">[ RSS-канал ]</a></div>
		<?php
		if(!is_admin(ADMIN_PASSWD, ADMIN_NAME)) {
			// если в GET запросе не указан номер страницы, выводим первую
			$numnews_page = 1;
			// если была нажата ссылка показать архив новостей
			if(isset($_GET['newsarchive'])) {
				print('<div><span class="smallcaps">Архив новостей: </span>');
				// ссылка показать все новости нажата не была
				if($_GET['newsarchive'] == 'part') {
					print('<a href="' . $_SERVER['PHP_SELF'] . '?newsarchive=all' . newsGETstr() . '" style="white-space: nowrap;">[ Все новости ]</a></div>' . "\n");
					// подготовка к выводу навигации по страницам
					if(isset($_GET['newspage'])){
						if(is_numeric($_GET['newspage'])) {
							$numnews_page = $_GET['newspage'];
						}
					}
					// подсчитаем количество записей в журнале посещений
					$count = @file(NEWS_DATA) or $count = array();
					$count = count($count);
					// навигация по страницам
					nav_page (
					ceil($count/NEWS_COUNT), $numnews_page,
					$_SERVER['PHP_SELF'] . "?newsarchive=part" . newsGETstr() . "&amp;newspage=",
					NEWS_NAV_LINKS_COUNT, 'img/pages.gif'
					);
					// распечатать часть новостей
					print(news(NEWS_DATA, false, 0, $numnews_page));
				}
				// распечатать все новост
				else {
					print('<a href="' . $_SERVER['PHP_SELF'] . '?newsarchive=part' . newsGETstr() . '">[ Постраничный вывод ]</a></div>' . "\n");
					print(news(NEWS_DATA, true, 0));
				}
			}
			// распечатать  часть новостей
			else print(news(NEWS_DATA, false, 1, 1, 1));
		}
		else {
			print(news_form());
			print(news(NEWS_DATA, true));
		}
		?></div>
		</div>
		<!-- END Новости сайта --> <!-- Голосование -->
		<div class="voting">
		<div class="border">
		<div class="download"><span style="font-weight: bold; color: red;">!</span>
		Скачать модуль голосования можно <a href="/main/releases.php#voting">[
		здесь ]</a></div>
		<?php
		// показать ошибки, если были
		if ($voting_errors = tmpfile_error_show($voting_temp_file))
		print('<div class="error" onclick="this.style.display = \'none\';">' . "\n" . $voting_errors . "</div>\n");
		?>
		<div class="section_title">Голосование</div>
		<img src="/engine/main/voting_image.php"
			width="<?php print(VOTING_IMG_WIDTH); ?>"
			height="<?php print(VOTING_IMG_HEIGHT); ?>" border="0" alt="" />
		<div class="votingform">
		<form action="<?php print($_SERVER['PHP_SELF'] . votingGETstr()); ?>"
			method="post">
		<div style="padding-bottom: 4px; font-size: 12px;">Какой
		веб-обозреватель вам нравится больше?</div>
		<div style="float: left;"><?php
		for($i=0; $i<count($legendarr); $i++) {
			$j = $i + 1;
			print('<div><input type="radio" name="voting" id="voting' . $j . '" value="' . $j . '" /><label for="voting' . $j . '">' . $legendarr[$i] . '</label></div>' . "\n");
		}
		?></div>
		<div><?php
		for($i=1; $i<count($legendarr); $i++) {
			print('<br />');
		}
		?> <input type="submit" name="voting_submit" value="Голосовать"
			style="border: 1px solid black;" /></div>
		<div style="clear: both;">&nbsp;</div>
		</form>
		</div>
		</div>
		</div>
		<!-- END Голосование --></td>
		<!-- END -->

		<!-- c3 -->
		<td width="335">
		<div class="border"><!-- Журнал посещений - форма --> <?php
		// показать ошибки, если были
		if ($journal_errors = tmpfile_error_show($journal_temp_file))
		print('<div class="error" onclick="this.style.display = \'none\';"><img src="img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" />' . "\n" . $journal_errors . "</div>\n");
		if ($fatal_journal_errors)
		print('<div class="error" onclick="this.style.display = \'none\';"><img src="img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" />Ошибка при выполнении скрипта, попробуйте еще раз.</div>' ."\n");
		?>
		<div id="journalerror" class="error" style="display: none;"
			onclick="this.style.display = 'none'; focusFirst();"><img
			src="img/warn.gif" width="16" height="16" alt=""
			style="position: relative; top: 4px;" /><span
			id="journalerrorinternal">&nbsp;</span></div>
		<noscript><img src="img/journal.gif" height="16" width="16" border="0"
			alt="" align="left" /></noscript>
		<div class="section_title">Мини гостевая</div>
		<?php
		// распечатываем форму с журналом
		print(journal_form($error_name, $error_msg));
		?>
		<div><sup style="color: maroon;">*</sup><i style="font-size: 12px;"> -
		Обязательно для заполнения</i></div>
		<!-- END Журнал посещений - форма --> <!-- Журнал посещений - записи -->
		<div class="journalbox"><?php
		// если в GET запросе не указан номер страницы, выводим первую
		$numjournal_page = 1;
		if(isset($_GET['page'])){
			if(is_numeric($_GET['page'])) {
				$numjournal_page = $_GET['page'];
			}
		}
		// подсчитаем количество записей в журнале посещений
		$count = @file(JOURNAL_DATA) or $count = array();
		$count = count($count);
		// навигация по страницам
		if ($count > JOURNAL_NOTE_COUNT) {
			nav_page (
			ceil($count/JOURNAL_NOTE_COUNT), $numjournal_page,
	"{$_SERVER['PHP_SELF']}" . journalGETstr(),
			JOURNAL_NAV_LINKS_COUNT, 'img/pages.gif'
			);
		}
		?>
		<div class="journalnote"><?php
		// распечатываем n-ю страницу с записями журнала посещений
		print(journal_note($numjournal_page));
		?></div>
		<div class="all_notes">Всего записей: <?php print($count) ?></div>
		</div>
		<!-- END Журнал посещений - записи --></div>
		</td>
		<!-- END -->

	</tr>
</table>
</div>
<!-- END <div class="main"> -->
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

<!-- Баннеры -->
<div
	class="banner"><a href="http://php.net" target="_blank"><img
	src="/img/php5-power-micro.png" width="80" height="15" border="0"
	alt="" id="opyb1" onmouseover="setElementOpacity('opyb1', 1.0);"
	onmouseout="setElementOpacity('opyb1', 0.5);" /></a> <!-- webmoney attestation label#B8490950-5998-4A6C-B4B2-848745176EC0 begin -->
<a
	href="https://passport.webmoney.ru/asp/certview.asp?wmid=268232593705"
	target="_blank"><img src="/img/wmatst2.gif"
	title="Здесь находится аттестат нашего WM идентификатора 268232593705"
	width="80" height="15" border="0" alt="" id="opyb2"
	onmouseover="setElementOpacity('opyb2', 1.0);"
	onmouseout="setElementOpacity('opyb2', 0.5);" /></a> <!-- webmoney attestation label#B8490950-5998-4A6C-B4B2-848745176EC0 end -->
<a href="http://validator.w3.org/check?uri=referer"><img
	src="/img/valid-xhtml10.small.png" alt=""
	title="Valid XHTML 1.0 Transitional" height="15" width="80" border="0"
	id="opyb3" onmouseover="setElementOpacity('opyb3', 1.0);"
	onmouseout="setElementOpacity('opyb3', 0.5);" /></a> <a
	href="http://jigsaw.w3.org/css-validator/check/referer"><img
	style="border: 0; width: 80px; height: 15px"
	src="/img/valid-css.small.png" alt="" title="Valid CSS!" id="opyb4"
	onmouseover="setElementOpacity('opyb4', 1.0);"
	onmouseout="setElementOpacity('opyb4', 0.5);" /></a> 
	
<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='http://counter.yadro.ru/hit?t25.4;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet: number of visitors for today is"+
" shown' "+
"border=0 width=88 height=15><\/a>")//--></script><!--/LiveInternet-->


<!-- openSUSE --> <a href="http://opensuse.org" target="_blank"><img
	src="/img/Suselinux-green.png" alt="Мне нравится это дистрибутив linux"
	title="Мне нравится это дистрибутив linux" width="" height=""
	id="opyb6" onmouseover="setElementOpacity('opyb6', 1.0);"
	onmouseout="setElementOpacity('opyb6', 0.5);" border="0" /></a> <!-- END openSUSE -->
</div>
<!-- END Баннеры -->
<!-- Домашняя страница Пономарева Дмитрия. Версия 2.0 -->
<script type="text/javascript">
//<![CDATA[
if(engineCommonLoad) setInterval("timestamp('time')", 300);
if(engineMainLoad) {
	setInterval("journalnumcharsfunc(<?php print(JOURNAL_MAX_MSG_LENGTH); ?>)", 100);
	setElementOpacity('opyb1', 0.5);
	setElementOpacity('opyb2', 0.5);
	setElementOpacity('opyb3', 0.5);
	setElementOpacity('opyb4', 0.5);
	setElementOpacity('opyb5', 0.5);
        setElementOpacity('opyb6', 0.5);
}
//]]>
</script>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-8040968-1");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>
