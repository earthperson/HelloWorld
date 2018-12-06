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

// �������� ����� ������ ���������� ��������
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
print(include_head(TITLE . ' | �������� ��� �����, ����� ��� ���������� ��� ����� �� ����������� ������� ))))', $head_content));
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

<div class="section_title">�������� ��� �����, ����� ��� ���������� ��� ����� �� ����������� ������� ))))</div>
<div style="text-align: center;padding: 20px;">

<div class="section_title">���� 1</div>
<div>
<object type="application/x-shockwave-flash" data="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" height="300" width="400">
<param name="bgcolor" value="#F5F5F5" />
<param name="allowFullScreen" value="true" />
<param name="allowScriptAccess" value="always" />
<param name="movie" value="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" />
<param name="FlashVars" value="way=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/gone_nutty.flv&amp;swf=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf&amp;w=400&amp;h=300&amp;pic=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/gone_nutty.png&amp;autoplay=0&amp;tools=1&amp;skin=white&amp;volume=70&amp;q=&amp;comment=" />
</object>
</div>

<div class="section_title" style="padding-top: 10px;">���� 2</div>
<div>
<object type="application/x-shockwave-flash" data="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" height="300" width="400">
<param name="bgcolor" value="#F5F5F5" />
<param name="allowFullScreen" value="true" />
<param name="allowScriptAccess" value="always" />
<param name="movie" value="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" />
<param name="FlashVars" value="way=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/no_time_for_nuts.flv&amp;swf=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf&amp;w=400&amp;h=300&amp;pic=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/no_time_for_nuts.png&amp;autoplay=0&amp;tools=1&amp;skin=white&amp;volume=70&amp;q=&amp;comment=" />
</object>
</div>

<div class="section_title" style="padding-top: 10px;">���� 3</div>
<div>
<object type="application/x-shockwave-flash" data="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" height="300" width="400">
<param name="bgcolor" value="#F5F5F5" />
<param name="allowFullScreen" value="true" />
<param name="allowScriptAccess" value="always" />
<param name="movie" value="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" />
<param name="FlashVars" value="way=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/clip3.flv&amp;swf=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf&amp;w=400&amp;h=300&amp;pic=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/clip3.png&amp;autoplay=0&amp;tools=1&amp;skin=white&amp;volume=70&amp;q=&amp;comment=" />
</object>
</div>

<div class="section_title" style="padding-top: 10px;">���� 4</div>
<div>
<object type="application/x-shockwave-flash" data="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" height="300" width="400">
<param name="bgcolor" value="#F5F5F5" />
<param name="allowFullScreen" value="true" />
<param name="allowScriptAccess" value="always" />
<param name="movie" value="http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf" />
<param name="FlashVars" value="way=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/clip4.flv&amp;swf=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/uflvplayer.swf&amp;w=400&amp;h=300&amp;pic=http://<?=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])?>/source/clip4.png&amp;autoplay=0&amp;tools=1&amp;skin=white&amp;volume=70&amp;q=&amp;comment=" />
</object>
</div>

<div class="section_title" style="padding-top: 10px;">���� 5</div>
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
if(engineCommonLoad) {window.onload = focusFirst;}
//]]>
</script>
</body>
</html>