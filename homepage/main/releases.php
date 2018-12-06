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
require_once '../engine/releases/config.php';
require_once '../engine/releases/lib.php';
require_once '../engine/releases/template.php';

// �������� ����� ������ ���������� ��������
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

<div class="copy">
������������ ����������� � �������� ����� ��������� ��������� ��� ��������:
<ul>
<li><span>���� �� ���������� ������ � �������� ���� ��� � ������ �������� � �������, ������������ ��� ����������.</span></li>
<li><span>�� �� ������ �������� ���-���� ��������� � ������ ��� ���� ����������.</span></li>
<li><span>����� �� ���� ������� �������� � �� ����� ������� ��������������� �� ��������� ����� � ������ ������ ����.</span></li>
</ul>
</div>

<!-- ��������� ���� -->
<div class="border">
<div class="section_title">
<script type="text/javascript">
//<![CDATA[
document.write('<span onclick="treechange(\'<?php print($idbuffer[] = $id = idgen()); ?>\', this);"><b class="tree">+</b><span style="cursor: pointer;"> ��������� ���� v 1.0</span></span>');
//]]>
</script>
<noscript>��������� ���� v 1.0</noscript>
</div>
<div align="justify" id="<?php print($id); ?>">
��������� ������������� ��� ������� ���������� ������ � ������������ ������� ��� ��������:
<ul>
<li><span>������������� ���������� ������ �� �������.</span></li>
<li><span>�������� ����������� ���������� �� ���������� ����� ������� (����� �������, � ������� ������������ �������<br /> ����������� ���������� ����� � �������������� ������������� ����� ������ � ��������� ��������� �����).</span></li>
<li><span>�������� �� ������������ ����������� ���������� �� �������������� ����� ������� (�.�. �������� ���������� ���������).</span></li>
<li><span>��������� ������� �������������� ��������������, �.�. ����������� �� ������� ���������.</span></li>
<li><span>����������� ������ �������� � ���� �������� �� ����������� ��������, �������� �� ��������� ������������, ����� ��������� ����� �� �������.</span></li>
</ul>
<br />
������� ��� �������, ������������ � ���������:
<br />
&rho; = n &times; r / ( ���� ������� � ����� ),<br />
��� r �����, ���
<br />&sum; = �� &times; ( 1 / (1 + r) + 1 / (1 + r)&sup2; + . . . + 1 / (1 + r)<sup><small>n</small></sup> ),
<br />���<br />
&rho; - ��������� ���������� ������, � % �������,<br />
�� - ����������� ������,<br />
n - ���������� ��������� �������� (���� �������),<br />
&sum; - ����� �������.<br />
<ul>
<li><span>��������� Bank �������� �� JavaScript ��� �� Windows.</span></li>
<li><span>������ ������������ ����� 17Kb, ���������� *.hta (HyperText Application).</span></li>
<li><span>����������� �������� ��� ��������� �����, ������� ���������� ����� �� *.txt.</span></li>
</ul>
</div>
<div class="download"><a href="../files/bank.zip">[ ������� ]</a></div>
</div>
<!-- END ��������� ���� --><br />

<!-- ������ ����������� -->
<a name="voting"></a>
<div class="border">
<div class="section_title">
<script type="text/javascript">
//<![CDATA[
document.write('<span onclick="treechange(\'<?php print($idbuffer[] = $id = idgen()); ?>\', this);"><b class="tree">+</b><span style="cursor: pointer;"> ������ ����������� v 1.0</span></span>');
//]]>
</script>
<noscript>������ ����������� v 1.0</noscript>
</div>
<div align="justify" id="<?php print($id); ?>">
����������:<ul>
   <li><span>PHP � ���������� ��� ������������ ������������ ����������� - GD.</span></li>
 </ul>
����������� � �����������:
<ul>
   <li><span>����������� ���������� � ���� ����������� ������������ �� PHP ����������� ����������� �����������.</span></li>
   <li><span>������ �� ���������� �����������.</span></li>
   <li><span>������������ ����������� ����� ���������� ���� ���� � ������������� ����� ��������.</span></li>
   <li><span>������� ��� ����������� ����� ��������� ��������, ��������: ������ ����������� �����������, ���������� ����� ����������, ������ ������, ����������� ��������, � ��. � ���������� �� ��������� ����� �������� ������� ��� �������� ���� �����������.</span></li>
   <li><span>���� ��� ������ ������� ���������� �������������, ��� ��������� ��������� � ����� ������������ <code>config.php</code>.</span></li>
 </ul>
</div>
<div  class="download"><a href="../files/voting.zip">[ ������� ]</a></div>
</div>
<!-- END ������ ����������� --><br />

<!-- �������� ����(batch file) ��� ������� ����� temp � Windows -->
<a name="voting"></a>
<div class="border">
<div class="section_title">
<script type="text/javascript">
//<![CDATA[
document.write('<span onclick="treechange(\'<?php print($idbuffer[] = $id = idgen()); ?>\', this);"><b class="tree">+</b><span style="cursor: pointer;"> �������� ���� (batch file) ��� ������� ����� Temp � Windows</span></span>');
//]]>
</script>
<noscript>�������� ���� (batch file) ��� ������� ����� Temp � Windows</noscript>
</div>
<div align="justify" id="<?php print($id); ?>">
����������:<ul>
   <li><span>������������ ������� Windows.</span></li>
 </ul>
����������� � �����������:
<ul>
   <li><span>������� ����������� ��������� ����� Temp</span></li>
 </ul>
</div>
<div  class="download"><a href="../files/clearTmp.zip">[ ������� ]</a></div>
</div>
<!-- END ������ ����������� --><br />

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