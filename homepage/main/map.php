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
require_once '../engine/google_map/config.php';
require_once '../engine/google_map/lib.php';
require_once '../engine/google_map/template.php';

// �������� ����� ������ ���������� ��������
$startTime = fulltime();

// <head>
$myClassName = '';
if(isset($_COOKIE['className'])) {
	$myClassName = 'class="' . $_COOKIE['className'] . '"';
}
$head_content  = '';
$head_content .= include_shortcut_icon('/img/favicon.ico');
$head_content .= include_stylesheet('../engine/common/styles.css', '../engine/google_map/styles.css');
$head_content .= include_javascript(file_get_contents('./gMapApi.js'), '
<script type="text/javascript">
//<![CDATA[
	var enginereserve2Load = false;
	var engineCommonLoad = false;
//]]>
</script>', '../engine/common/script.js', '../engine/google_map/script.js');
// �������� �������� �������� �� ������� $listPages �� ��������� �������� �����
if (isset($listPages[basename(__FILE__)])) {
	print(include_head(TITLE . ' | ' . $listPages[basename(__FILE__)], $head_content));
}
else {
	print(include_head(TITLE . ' | ?', $head_content));
}
// </head>
print('<body ' . $myClassName . ' onunload="GUnload()">' . "\n"); ?>
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

<div style="text-align: center; padding-bottom: 14px;">
   <noscript>��� ����������� ����� ���������� �������� ��������� JavaScript</noscript>
   <div style="padding-bottom: 14px;">
   <form action="#" method="post" onsubmit="showAddress(this.address.value); return false;">
   �����: <input type="text" name="address" id="address" style="width: 660px;" onfocus="this.style.borderColor = '#3300cc';" onblur="this.style.borderColor = 'black';" />
   <input type="submit" name="submit" value="��������" />
   </form>
   </div>
   <div id="map" class="gmap"></div>
   <div id="searchcontrol"></div>

</div>

</div><!-- END <div class="main"> -->
<?php
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
if(engineCommonLoad) {window.onload = initialize;}
//]]>
</script>
</body>
</html>
<?php
// ���������� ���������� ������ ������ �
// ��������� ����������� ������
ob_end_flush();
?>