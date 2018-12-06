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
require_once '../engine/common/cache0.php';
require_once '../engine/config.php';
require_once '../engine/common/include_header.php';
require_once '../engine/common/lib.php';
require_once '../engine/common/template.php';

// ���������� ������ ��������
require_once '../engine/code/config.php';
require_once '../engine/code/lib.php';
require_once '../engine/code/template.php';

// �������� ����� ������ ���������� ��������
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
// �������� �������� �������� �� ������� $listPages �� ��������� �������� �����
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
// <!-- ����� ��������������, ���� ��������� JavaScript ��������� -->
print(noscript());
?>

<!-- ���� -->
<div class="selfmenu">
<span class="section_title">���������:&nbsp;&nbsp;&nbsp;</span>
<?php print(selfmenu($listPages)); ?>
</div>
<!-- END ���� -->
<div style="margin-bottom: 10px;"><span class="section_title">� ������� </span><span style="font-size: 12px;">����� ������������ ������ � ������� PHP � JavaScript, ��� ������� ����� ����������� �����.</span></div>
<?php
// ������� ������ ������ � ���������� php � ���� �������
$pagesarr = myscan_dir(CODE_FOLDER . '/php/');
print '<!-- PHP -->
<div class="section_title" style="color: black;">PHP (' . count($pagesarr) . ')</div>';
if(is_array($pagesarr)) {
	// ���������
	if(isset($_GET['pagephp'])) {
		if(is_numeric($_GET['pagephp'])) {
			$pagephp = intval($_GET['pagephp']);
		}
	}
	else {
		// ���� � GET ������� �� ������ ����� ��������, ������� ������
		$pagephp = 1;
	}
	// ����� ���-�� �������
	(PHP_LENGTH > 0) ? $countpages = ceil(count($pagesarr) / PHP_LENGTH) : $countpages = 0;
	if($countpages > 1) {
		// ��������� �� ���������
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
		// ��������� �� ���������
		if(isset($_GET['pagejs'])) {
			nav_page ($countpages, $pagephp, $_SERVER['PHP_SELF'] . '?pagejs=' . $_GET['pagejs'] . '&pagephp=', 10, '../img/pages.gif');
		}
		else {
			nav_page ($countpages, $pagephp, $_SERVER['PHP_SELF'] . '?pagephp=', 10, '../img/pages.gif');
		}
	}
}
print "<br /><hr /><br />\n";
// ������� ������ ������ � ���������� js � ���� �������
$pagesarr = myscan_dir(CODE_FOLDER . '/js/');
print '<!-- JavaScript -->
<div class="section_title" style="color: black;">JavaScript (' . count($pagesarr) . ')</div>';
if(is_array($pagesarr)) {
	// ���������
	if(isset($_GET['pagejs'])) {
		if(is_numeric($_GET['pagejs'])) {
			$pagejs = intval($_GET['pagejs']);
		}
	}
	else {
		// ���� � GET ������� �� ������ ����� ��������, ������� ������
		$pagejs = 1;
	}
	// ����� ���-�� �������
	(JS_LENGTH > 0) ? $countpages = ceil(count($pagesarr) / JS_LENGTH) : $countpages = 0;
	if($countpages > 1) {
		// ��������� �� ���������
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
		// ��������� �� ���������
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
<!-- �������� �������� ���������� �������. ������ 2.0 -->
<script type="text/javascript">
//<![CDATA[
if(engineCommonLoad) setInterval("timestamp('time')", 300);
//]]>
</script>
</body>
</html>