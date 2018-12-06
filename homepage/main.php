<?php
function callback($buffer) {
	return trim($buffer);
}
// �������� ����������� ������
ob_start("callback");

// �������������� ������
session_set_cookie_params(0); //������ ����� ����� ����
session_start();

// ���������� ����� ������
require_once 'engine/common/cache0.php';
require_once 'engine/config.php';
require_once 'engine/common/include_header.php';
require_once 'engine/common/lib.php';
require_once 'engine/common/template.php';
require_once 'engine/classes/ErrorLog.php';

// ���������� ������ ��������
require_once 'engine/main/config.php';
require_once 'engine/main/lib.php';
require_once 'engine/main/template.php';

// C������ ��������� ������ ��� ������ ��������� �� �������
$el = new ErrorLog();

// �������� ����� ������ ���������� �������
$startTime = fulltime();

// +-------------------------------------------------------------------------------------------+
// | ���� ����� �����������                    
// +-------------------------------------------------------------------------------------------+
/*
 if(!(isset($_SESSION['visitor']) && ($_SESSION['visitor'] == 'is_visited'))) {
 $visitorF = @fopen(VISITOR_DATA, 'a');
 if($visitorF) {
 $month = gmdate('F',  (time() + 3600 * 3));
 // ����������� �������� ������
 monthrename($month);
 fwrite($visitorF, gmdate('H:i:s d ', (time() + 3600 * 3)) . $month . gmdate(' Y\|', (time() + 3600 * 3)) . getenv('REMOTE_ADDR') . '|' . getenv('HTTP_USER_AGENT') . "\n");
 fclose($visitorF);
 $_SESSION['visitor'] = 'is_visited';
 }
 }
 */
// +-------------------------------------------------------------------------------------------+

// +-------------------------------------------------------------------------------------------+
// | ���� �����������               
// +-------------------------------------------------------------------------------------------+
// ������� ��������� ���� ������ ��� ����������� � ���������� ������, �� ������������� ���������
// ����� ���������� ������ ������� ��� ������������� ������� fclose()
$voting_temp_file = tmpfile();
if(isset($_POST['voting_submit'])) {
	if(isset($_POST['voting'])) {
		// ���� ���� � ��������� ����� �� ������ - �������� �������
		if(!file_exists(VOTING_FLUD)) {
			$createvotingfile = @fopen(VOTING_FLUD, 'w');
			if($createvotingfile) fclose($createvotingfile);
		}
		// �������� ���������� ������� ip � ������� �����������
		$votingfludarr = @file(VOTING_FLUD) or array();
		$votingfludarrcount = count($votingfludarr);
		for($i=0; $i<$votingfludarrcount; $i++) {
			$votingt = explode('|', $votingfludarr[$i]);
			if($votingt[1] + VOTING_FLUD_TIME <  time()) unset($votingfludarr[$i]);
		}
		$votingflud = @fopen(VOTING_FLUD, 'r+');
		if($votingflud) {
			// ������������� ���� �� ������
			flock($votingflud, LOCK_EX);
			ftruncate($votingflud, 0); //������� ��� ���������� �����
			rewind($votingflud);
			fwrite($votingflud, implode('', $votingfludarr));
			// ����� ���������� (��� �������� ��������� �������������)
			fflush($votingflud); //���������� ������ �� ����
			flock($votingflud, LOCK_UN);
			fclose($votingflud);
		}
		// ��������� ������, ��� ����, ���� �������� ����� ����� �������� ��������� �������
		// � ������� unset()
		sort($votingfludarr);

		// ����������, �������� �� ������ ������ ������, � ������ �����������, ��� �� ��������
		// ��������� ������ � ���������� $votingflud
		$votingfludmean = false;
		for($i=0; $i<count($votingfludarr); $i++) {
			$votingip = explode('|', $votingfludarr[$i]);
			if($votingip[0] == getenv('REMOTE_ADDR')) $votingfludmean = true;
		}

		// ���������� ip � ����� �����������, ���� ���� �� ��� ������ - �������� �������
		$votingflud = @fopen(VOTING_FLUD, 'a+');
		if($votingflud) {
			// ������������� ���� �� ������
			flock($votingflud, LOCK_EX);
			fwrite($votingflud, getenv('REMOTE_ADDR') . '|' . (string) time() . "\n");
			// ����� ���������� (��� �������� ��������� �������������)
			fflush($votingflud); //���������� ������ �� ����
			flock($votingflud, LOCK_UN);
			fclose($votingflud);
		}
		if(!$votingfludmean) {
			if(file_exists(VOTING_DATA)) {
				// ������� ���� �� ������ � ������
				$votingfile = @fopen(VOTING_DATA, 'r+');
				// ������������� ����
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
				// ����� ���������� (��� �������� ��������� �������������)
				fflush($votingflud); //���������� ������ �� ����
				flock($votingfile, LOCK_UN);
				// � ������� ���� (��� ������ ����������� �������������)
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
			// ��������� �������� ��������
			header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . votingGETstr());
			exit();
		}
		else {
			fwrite($voting_temp_file, '<div><img src="img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" /> �� ��� �������������, ���������� ����� ' . ceil(VOTING_FLUD_TIME / 60) . ' ���.</div>');
		}
	}
}
// +-------------------------------------------------------------------------------------------+

// +-------------------------------------------------------------------------------------------+
// | ���� ������� ���������                    
// +-------------------------------------------------------------------------------------------+
$error_name = '';
$error_msg = '';
$fatal_journal_errors = false;
$journal_temp_file = tmpfile();
// ���� ����� �������� ������
if( isset($_POST['journal_submit']) ) {
	// ���� �� ��������� ������ ��� �������� ���������� �������
	if( isset($_POST['name'], $_POST['msg'], $_POST['spam']) ) {
		// ���������, ��� ���� �� ���� ���� ���� ���������
		if(!(str_empty($_POST['name']) && str_empty($_POST['msg']))) {
			if((trim($_POST['name']) == ADMIN_NAME) && (md5(trim($_POST['msg'])) == ADMIN_PASSWD)) {
				${ADMIN_NAME} = ADMIN_PASSWD;
				is_admin(ADMIN_PASSWD, ADMIN_NAME);
				header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
				exit();
			}
			else {
				// ��������� ������ ����� ����� � ��������
				// ����������� ������ � ���� �������, ���� ��� ��������� ��� true ��� false
				$journal_arr = journal_test($_POST['name'], $_POST['msg'], $_POST['spam'], $journal_temp_file);
				if($journal_arr === true) {
					// ���� �������������, �������������� ����������� �������
					$journal_arr = journal_admin_test($_POST['name'], $_POST['msg']);
					// �����������
					$journal_str = str_format($journal_arr, true, 'monthrename');
					// ������� ��������� (������) ������, ���� ����� ���������� ������� ������ �������
					journal_delete_below_note();
					// ���������
					add($journal_str, 1, JOURNAL_DATA);
					// ��������� �������� ��������
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
// ���� ����� ������� ������, �������
if(isset($_GET['journaldel'])) {
	// ���� ����� ��� �������� ������
	if(is_numeric($_GET['journaldel'])) {
		del(intval($_GET['journaldel']), JOURNAL_DATA);
	}
	// ��������� �������� ��������
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	exit();
}
// +-------------------------------------------------------------------------------------------+

// +-------------------------------------------------------------------------------------------+
// | ���� ������ � �����                
// +-------------------------------------------------------------------------------------------+
// ���� ����� �������� ������
if(isset($_POST['description_submit'])) {
	// ���� �� ��������� ������ ��� �������� ���������� �������
	if(isset($_POST['description'])) {
		// ������� ������������ ��������, ���� ��������� magic_quotes_gpc ��������
		if(get_magic_quotes_gpc()) $newsstr = stripslashes($_POST['description']);
		else $newsstr = $_POST['description'];
		// ���������
		$descriptionhandle = @fopen(DESCRIPTION_DATA, 'w');
		if($descriptionhandle) {
			fwrite($descriptionhandle, $newsstr);
			fclose($descriptionhandle);
		}
	}
	// ��������� �������� ��������
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?dm=1');
	exit();
}
// +-------------------------------------------------------------------------------------------+

// +-------------------------------------------------------------------------------------------+
// | ���� �������� �����                 
// +-------------------------------------------------------------------------------------------+
// ���� ����� �������� ������
if(isset($_POST['news_submit'])) {
	// ���� �� ��������� ������ ��� �������� ���������� �������
	if(isset($_POST['news'])) {
		// ���������, ��� ���� �� ���� ���� ���� ���������
		if(!(str_empty($_POST['news']))) {
			// �����������
			$saying_str = str_format(array($_POST['news']), true, 'monthrename');
			// ���������
			add($saying_str, 1, NEWS_DATA);
			// ��������� � RSS-�����
			$month_n = date('F');
			// ����������� �������� ������
			monthrename($month_n);
			$timestamp_n = date('H:i d ') . $month_n . date(' Y');
			if(get_magic_quotes_gpc()) {
			    $_POST['news'] = stripslashes($_POST['news']);
			}
			addRSS(htmlspecialchars($_POST['news']), $timestamp_n, 'rss/news.xml');
		}
	}
	// ��������� �������� ��������
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	exit();
}
// ���� ����� ������� ������, �������
if(isset($_GET['newsdel'])) {
	// ���� ����� ��� �������� ������
	if(is_numeric($_GET['newsdel'])) {
		del(intval($_GET['newsdel']), NEWS_DATA);
		delRSS(intval($_GET['newsdel']), 'rss/news.xml');
	}
	// ��������� �������� ��������
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
/* ����������� � ����������� �� ������� ����� */
function writeGreeting() {
	var dateNow = new Date();
	var hh=parseInt(dateNow.getHours());
	if ( 4<=hh && hh<=11) {
		document.write('<h3>������ ����!</h3>'); 
	}
	else if (12<=hh && hh<=16) {
		document.write('<h3>������ ����!</h3>');
	}
	else if (17<=hh && hh<=23) {
		document.write('<h3>������ �����!</h3>');
	}
	else {
		document.write('<h3>������ ����!</h3>');
	}
}
/* END ����������� � ����������� �� ������� ����� */
//]]>
</script>", 'engine/common/script.js', 'engine/main/script.js');
// �������� �������� �������� �� ������� $listPages �� ��������� �������� �����
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
	title="������� �� ��������-��������">[ �������� ]</a></div>
<div id="clock"><?php
// <!-- ����� ��������������, ���� ��������� JavaScript ��������� -->
print(noscript());
?></div>
<!-- ����������� � ����������� �� ������� ����� -->
<script type="text/javascript">
//<![CDATA[
writeGreeting();
//]]>
</script>
<noscript>
<h3>������������!</h3>
</noscript>
<!-- END ����������� � ����������� �� ������� ����� -->

<?php
// �������� ��������������
if(is_admin(ADMIN_PASSWD, ADMIN_NAME)) {
	print('<div><a href="' . $_SERVER['PHP_SELF'] . '?admin=dstr">[ ����� ]</a></div>' . "\n");
}
?>

<!-- div class="main" -->
<div class="main">

<table width="100%">
	<tr>

		<!-- c1 -->
		<td width="33%">
		<div class="border"><!-- ���� -->
		<div class="menu">
		<div class="section_title">���������</div>
		<?php print(mainmenu($listPages)); ?> <!-- ���������� � ��������� -->
		<script type="text/javascript">
//<![CDATA[
if(engineMainLoad && (detectAgent() == 'MSIE' || detectAgent() == 'Gecko')) {
	document.write('<div><a href="#" class="favorite" title="�������� �������� � ��������� (��������)" onclick="addBookmark(); return false;">&nbsp;</a></div>');

}
//]]>
</script> <!-- END ���������� � ��������� --></div>
		<!-- END ���� --> <!-- ������� -->
		<div class="projects">
		<div class="section_title">����� �� dmitry-ponomarev.ru</div>
		<div class="submenulink"><span class="pr_sc">Test
		</span>������������ ��������.<a href="http://test.dmitry-ponomarev.ru"
			style="white-space: nowrap;" target="_blank">[ ������� ]</a>
		</div>
		<div class="submenulink"><span class="pr_sc">Temp
		</span>���������, ������� ������� ���.<a href="http://temp.dmitry-ponomarev.ru"
			style="white-space: nowrap;" target="_blank">[ ������� ]</a>
		</div>
		</div>
		<!-- END ������� --> <!-- ������� ������ -->
		<div class="links">
		<div class="section_title">������� ������</div>
		<div class="submenulink">��� �������� ������� �������� ��������-�����
		<a href="http://www.radioseven.se/"
			title="������ ��� ������ LYSSNA NU!" target="_blank">[ Radioseven ]</a>
		</div>
		</div>
		<!-- END ������� ������ --> <br />

		<!-- ����������� ����������� -->
		<div class="section_title">����������� �����������</div>
		<?php
		// ����� ��������� �� ������
		$el->showlog();
		// ���� � GET ������� �� ������ ����� ��������, ������� ������
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
		<div class="submenulink">����� � ��������� ������� �����������
		�������� ������������� ��������, ������� � ���������<span
			style="white-space: nowrap;"> (����� ���������: <?php print($mainlogosarr[0]); ?>).</span></div>
			<?php
			if(is_admin(ADMIN_PASSWD, ADMIN_NAME)) {
				print(logos_form());
				// ��������� �� ���������
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
					print '<div style="margin-top: 2px;"><span class="smallcaps">��� ��������: </span><a href="' . $_SERVER['PHP_SELF'] . '?logos=hidden' . logosGETstr(false) . '">[ ��������� ������� ]</a></div>' . "\n";
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
					print '<div style="padding: 4px;"><a href="' . $_SERVER['PHP_SELF'] . '?logos=show' . logosGETstr(false) . '">[ �������� ��� �������� ]</a></div>' . "\n";
				}
			}
			?> <!-- END ����������� ����������� -->
		<br />
		<!-- �������� ����� -->
		<div class="section_title">�������� �����</div>
		<script type="text/javascript">
//<![CDATA[
if(engineCommonLoad) {
	// ����� ��������� cookie, ����� ��������� ������� �� ���������
	document.cookie = 'cookiesenabled=yes';
	extractCookies();
	if(cookies['cookiesenabled'] == 'yes') {
		if(engineMainLoad) {
			<?php print(colorscheme()); ?>
		}
	}
	else document.write('<em>( ����� ���������� - ��������� cookies ���������! )</em>');
}
//]]>
</script>
</div>
<div style="margin-top: 14px;">
<script type="text/javascript" type="text/javascript" src="/orphus/orphus.js"></script>
<a href="http://orphus.ru" id="orphus" target="_blank"><img alt="Orphus system" src="/orphus/orphus.gif" border="0" width="121" height="21" /></a>
</div>

		<noscript><em>( ����� ���������� - ��������� JavaScript ���������! )</em></noscript>
		<!-- END �������� ����� --></td>
		<!-- END -->

		<!-- c2 -->
		<td>
		<div class="border"><!-- ������ � ����� -->
		<div class="greeting">
		<div class="section_title">������ � �����</div>
		<div style="font-size: 12px;"><?php
		if(is_admin(ADMIN_PASSWD, ADMIN_NAME)) {
			if(isset($_GET['dm'])) print('<div class="no_error" onclick="this.style.display = \'none\';">���� ������� ��������������!</div>' ."\n");
			print(description_form(description(DESCRIPTION_DATA)));
		}
		else print(description(DESCRIPTION_DATA));
		?></div>
		</div>
		<!-- END ������ � ����� --> <!-- ������� ����� -->
		<div class="news">
		<div class="section_title">������� �����</div>
		<div><a href="rss/news.xml" class="rss">[ RSS-����� ]</a></div>
		<?php
		if(!is_admin(ADMIN_PASSWD, ADMIN_NAME)) {
			// ���� � GET ������� �� ������ ����� ��������, ������� ������
			$numnews_page = 1;
			// ���� ���� ������ ������ �������� ����� ��������
			if(isset($_GET['newsarchive'])) {
				print('<div><span class="smallcaps">����� ��������: </span>');
				// ������ �������� ��� ������� ������ �� ����
				if($_GET['newsarchive'] == 'part') {
					print('<a href="' . $_SERVER['PHP_SELF'] . '?newsarchive=all' . newsGETstr() . '" style="white-space: nowrap;">[ ��� ������� ]</a></div>' . "\n");
					// ���������� � ������ ��������� �� ���������
					if(isset($_GET['newspage'])){
						if(is_numeric($_GET['newspage'])) {
							$numnews_page = $_GET['newspage'];
						}
					}
					// ���������� ���������� ������� � ������� ���������
					$count = @file(NEWS_DATA) or $count = array();
					$count = count($count);
					// ��������� �� ���������
					nav_page (
					ceil($count/NEWS_COUNT), $numnews_page,
					$_SERVER['PHP_SELF'] . "?newsarchive=part" . newsGETstr() . "&amp;newspage=",
					NEWS_NAV_LINKS_COUNT, 'img/pages.gif'
					);
					// ����������� ����� ��������
					print(news(NEWS_DATA, false, 0, $numnews_page));
				}
				// ����������� ��� ������
				else {
					print('<a href="' . $_SERVER['PHP_SELF'] . '?newsarchive=part' . newsGETstr() . '">[ ������������ ����� ]</a></div>' . "\n");
					print(news(NEWS_DATA, true, 0));
				}
			}
			// �����������  ����� ��������
			else print(news(NEWS_DATA, false, 1, 1, 1));
		}
		else {
			print(news_form());
			print(news(NEWS_DATA, true));
		}
		?></div>
		</div>
		<!-- END ������� ����� --> <!-- ����������� -->
		<div class="voting">
		<div class="border">
		<div class="download"><span style="font-weight: bold; color: red;">!</span>
		������� ������ ����������� ����� <a href="/main/releases.php#voting">[
		����� ]</a></div>
		<?php
		// �������� ������, ���� ����
		if ($voting_errors = tmpfile_error_show($voting_temp_file))
		print('<div class="error" onclick="this.style.display = \'none\';">' . "\n" . $voting_errors . "</div>\n");
		?>
		<div class="section_title">�����������</div>
		<img src="/engine/main/voting_image.php"
			width="<?php print(VOTING_IMG_WIDTH); ?>"
			height="<?php print(VOTING_IMG_HEIGHT); ?>" border="0" alt="" />
		<div class="votingform">
		<form action="<?php print($_SERVER['PHP_SELF'] . votingGETstr()); ?>"
			method="post">
		<div style="padding-bottom: 4px; font-size: 12px;">�����
		���-������������ ��� �������� ������?</div>
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
		?> <input type="submit" name="voting_submit" value="����������"
			style="border: 1px solid black;" /></div>
		<div style="clear: both;">&nbsp;</div>
		</form>
		</div>
		</div>
		</div>
		<!-- END ����������� --></td>
		<!-- END -->

		<!-- c3 -->
		<td width="335">
		<div class="border"><!-- ������ ��������� - ����� --> <?php
		// �������� ������, ���� ����
		if ($journal_errors = tmpfile_error_show($journal_temp_file))
		print('<div class="error" onclick="this.style.display = \'none\';"><img src="img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" />' . "\n" . $journal_errors . "</div>\n");
		if ($fatal_journal_errors)
		print('<div class="error" onclick="this.style.display = \'none\';"><img src="img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" />������ ��� ���������� �������, ���������� ��� ���.</div>' ."\n");
		?>
		<div id="journalerror" class="error" style="display: none;"
			onclick="this.style.display = 'none'; focusFirst();"><img
			src="img/warn.gif" width="16" height="16" alt=""
			style="position: relative; top: 4px;" /><span
			id="journalerrorinternal">&nbsp;</span></div>
		<noscript><img src="img/journal.gif" height="16" width="16" border="0"
			alt="" align="left" /></noscript>
		<div class="section_title">���� ��������</div>
		<?php
		// ������������� ����� � ��������
		print(journal_form($error_name, $error_msg));
		?>
		<div><sup style="color: maroon;">*</sup><i style="font-size: 12px;"> -
		����������� ��� ����������</i></div>
		<!-- END ������ ��������� - ����� --> <!-- ������ ��������� - ������ -->
		<div class="journalbox"><?php
		// ���� � GET ������� �� ������ ����� ��������, ������� ������
		$numjournal_page = 1;
		if(isset($_GET['page'])){
			if(is_numeric($_GET['page'])) {
				$numjournal_page = $_GET['page'];
			}
		}
		// ���������� ���������� ������� � ������� ���������
		$count = @file(JOURNAL_DATA) or $count = array();
		$count = count($count);
		// ��������� �� ���������
		if ($count > JOURNAL_NOTE_COUNT) {
			nav_page (
			ceil($count/JOURNAL_NOTE_COUNT), $numjournal_page,
	"{$_SERVER['PHP_SELF']}" . journalGETstr(),
			JOURNAL_NAV_LINKS_COUNT, 'img/pages.gif'
			);
		}
		?>
		<div class="journalnote"><?php
		// ������������� n-� �������� � �������� ������� ���������
		print(journal_note($numjournal_page));
		?></div>
		<div class="all_notes">����� �������: <?php print($count) ?></div>
		</div>
		<!-- END ������ ��������� - ������ --></div>
		</td>
		<!-- END -->

	</tr>
</table>
</div>
<!-- END <div class="main"> -->
		<?php
		// ���������� ���������� ������ ������ �
		// ��������� ����������� ������
		ob_end_flush();

		// �������� ����� ��������� ���������� ��������
		$endTime = fulltime();
		// �������� �� ��������� ��������� � �������� ��������������� ����� ���������� ��������
		$time = $endTime - $startTime;
		// ���������� ��� �����������
		info($time);
		?>

<!-- ������� -->
<div
	class="banner"><a href="http://php.net" target="_blank"><img
	src="/img/php5-power-micro.png" width="80" height="15" border="0"
	alt="" id="opyb1" onmouseover="setElementOpacity('opyb1', 1.0);"
	onmouseout="setElementOpacity('opyb1', 0.5);" /></a> <!-- webmoney attestation label#B8490950-5998-4A6C-B4B2-848745176EC0 begin -->
<a
	href="https://passport.webmoney.ru/asp/certview.asp?wmid=268232593705"
	target="_blank"><img src="/img/wmatst2.gif"
	title="����� ��������� �������� ������ WM �������������� 268232593705"
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
	src="/img/Suselinux-green.png" alt="��� �������� ��� ����������� linux"
	title="��� �������� ��� ����������� linux" width="" height=""
	id="opyb6" onmouseover="setElementOpacity('opyb6', 1.0);"
	onmouseout="setElementOpacity('opyb6', 0.5);" border="0" /></a> <!-- END openSUSE -->
</div>
<!-- END ������� -->
<!-- �������� �������� ���������� �������. ������ 2.0 -->
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
