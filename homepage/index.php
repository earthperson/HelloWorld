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
//require_once 'engine/common/cache0.php';
require_once 'engine/config.php';
require_once 'engine/common/include_header.php';
require_once 'engine/common/lib.php';
require_once 'engine/common/template.php';

// ���������� ������ ��������
require_once 'engine/index/config.php';
require_once 'engine/index/lib.php';
require_once 'engine/index/template.php';


// +-------------------------------------------------------------------------------------------+
// | ���� ����������� ��������                                                                 |
// +-------------------------------------------------------------------------------------------+

// ���� ���� ������ ������ OK
if(isset($_POST['thesubmit'])) {
	// ������� ���������� - �������� cookie
	if(isset($_POST['checkbox'])) {
		setcookie('screensaver', 'no', time() + 0x12CC0300);
	}
	// ������� cookie
	else {
		setcookie('screensaver', '', time() - 3600);
	}
	// ������������� ��������
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?refresh=yes');
	// ����������, ��� ����������� ��� �� ����������� ��� ���������������
	exit();
}

// ���� ����������� ������ cookie � ������ screensaver � ��������� no
// �� ������� �� �������� /main.php
if(isset($_COOKIE['screensaver']) && $_COOKIE['screensaver'] === 'no') {
	// ����������
	if(!
	// ���� ������� �� �������� /main.php
	( (isset($_GET['show'])    && $_GET['show']    === 'yes')  ||
	// ���� �������� ��������������� �������� cookie_check()
	(  isset($_GET['cookie'])  && $_GET['cookie']  === 'test') ||
	// ���� ���� ������ ������ OK
	isset($_POST['thesubmit'])                              ||
	// ���� �������� ��������������� ����� ��������� ������� ������ OK
	(  isset($_GET['refresh']) && $_GET['refresh'] === 'yes') )) {
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/main.php');
		// ����������, ��� ����������� ��� �� ����������� ��� ���������������
		exit();
	}
	// ��������������� �� ���� ���������, ���������� ���������� $checked,
	// ������� ��������� ����� �� �������� �������� ���� checkbox
	$checked = 'checked="checked" ';
}
else
$checked = '';

// +-------------------------------------------------------------------------------------------+

// +-------------------------------------------------------------------------------------------+
// | ���� ���������� �����, ���� �������������                                                 |
// +-------------------------------------------------------------------------------------------+

// ���� ����� �������� ������
if(isset($_POST['add'])) {
	// ���� �� ��������� ������ ��� �������� ���������� �������
	if(isset($_POST['phrase'], $_POST['description'], $_POST['author'])) {
		// ���������, ��� ���� �� ���� ���� ���� ���������
		if(!(str_empty($_POST['phrase']) && str_empty($_POST['description']) && str_empty($_POST['author']))) {
			// �����������
			$saying_str = str_format(array($_POST['phrase'], $_POST['author'], $_POST['description']));
			// ���������
			add($saying_str, 1, SAYING_DATA);
		}
	}
	// ��������� �������� ��������
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	exit();
}

// ���� ����� ������� ������, �������
if(isset($_GET['del'])) {
	// ���� ����� ��� �������� ������
	if(is_numeric($_GET['del'])) {
		del(intval($_GET['del']), SAYING_DATA);
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

<!-- TITLE -->
<div><h1><?php print(TITLE); ?></h1></div>
<!-- End TITLE -->

<!-- [ �� �������� ��������, ������� ��� ��������� ���� ������ ]-->
<div>
<a href="main.php" class="index" title="����� ����������!" id="scroll">[ �� �������� ��������, ������� ��� ��������� ���� ������ ) ]</a>
<script type="text/javascript">
//<![CDATA[
var length = document.getElementById('scroll').firstChild.length;
if(engineIndexLoad) setInterval("scroll( ')', 'scroll', 12, -2, length )", 300);
//]]>
</script>
</div>
<!-- END [ �� �������� ��������, ������� ��� ��������� ���� ������ ]-->

<!-- ������ ��������� ����� (saying) -->
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
<!-- END ������ ��������� ����� (saying) -->

<!-- ����������� �� ���������� �������� � ��������� ���. -->
<div class="cookie">
<?php
$getstr = '';
foreach($_GET as $key => $value) {
	$getstr .= '&' . $key . '=' . $value;
}
if(cookie_check($getstr)) {
	// � javascript
	$str = '<script type="text/javascript">
//<![CDATA[' . "\n"
	. 'if(engineIndexLoad) {' . "\n"
	. 'extractCookies();' . "\n"
	. 'document.write(\'<form name="indexform" id="indexform" action="' . $_SERVER['PHP_SELF'] . '" method="post">\');' . "\n"
	. 'document.write(\'<input type="checkbox" name="checkbox1" ' . $checked . 'onclick="screensaver();" />\');' . "\n"
	. 'document.write(\'&nbsp;����������� �� ���������� �������� � ��������� ���.\');' . "\n"
	. 'document.write("</form>"); }' . "\n"
	. "//]]>\n</script>\n";
	// noscript
	$str .= '<noscript><form name="indexform" action="' . $_SERVER['PHP_SELF'] . '" method="post">' ."\n"
	. '<input type="checkbox" name="checkbox" value="1" ' . $checked . '/>'. "\n"
	. '<input type="submit" name="thesubmit" value="Ok" class="mybutton" title="��������� �����" />' . "\n"
	. '����������� �� ���������� �������� � ��������� ���.' . "\n"
	. '</form></noscript>' . "\n";
}
else {
	$str = '<form name="indexform" action="' . $_SERVER['PHP_SELF'] . '" method="post">' ."\n"
	. '<input type="checkbox" disabled="disabled" />' . "\n"
	. '����������� �� ���������� �������� � ��������� ���.<br />' . "\n"
	. '<em>( ����� ���������� - ��������� cookies ���������! )</em>' . "\n"
	. '</form>' ."\n";
}
print($str);
?>
</div>
<!-- END ����������� �� ���������� �������� � ��������� ���. -->

</div><!-- <div class="main"> -->
<?php
// ���������� ���������� ������ ������ �
// ��������� ����������� ������
ob_end_flush();
?>
<!-- �������� �������� ���������� �������. ������ 2.0 -->
<script type="text/javascript">
//<![CDATA[
if(engineCommonLoad) setInterval("timestamp('time')", 300);
if(engineCommonLoad) {window.onload = focusFirst;}
//]]>
</script>
</body>
</html>