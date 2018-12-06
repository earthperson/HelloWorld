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
require_once '../engine/contacts/config.php';
require_once '../engine/contacts/lib.php';
require_once '../engine/contacts/template.php';

// �������� ����� ������ ���������� ��������
$startTime = fulltime();

// +-------------------------------------------------------------------------------------------+
// | ���� �������� �����                                                                       |
// +-------------------------------------------------------------------------------------------+

$error_name = '';
$error_msg = '';
$error_mail = '';
$mailsend = 'not_set';
$fatal_contacts_errors = false;
$contacts_temp_file = tmpfile();
// ���� ����� �������� ������
if( isset($_POST['contacts_submit']) ) {
	// ���� �� ��������� ������ ��� �������� ���������� �������
	if( isset($_POST['name'], $_POST['mail'], $_POST['msg']) ) {
		// ���������, ��� ���� �� ���� ���� ���� ���������
		if(!(str_empty($_POST['name']) && str_empty($_POST['mail']) && str_empty($_POST['msg']))) {
			// ��������� ������ ����� ����� � ��������
			// ����������� ������ � ���� �������, ���� ��� ��������� ��� true ��� false
			$contacts_arr = contacts_test($_POST['name'], $_POST['mail'], $_POST['msg'], $contacts_temp_file);
			if($contacts_arr === true) {
				// ���������� �����
				if(mymail(TITLE,
				'���: ' . $_POST['name'] . "\n" .
				'E-mail: ' . $_POST['mail'] . "\n" .
				'���������: ' . $_POST['msg']))
				    $mailsend = 'send';
				else $mailsend = 'not_send';
				// ��������� �������� ��������
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

<div class="section_title" style="margin: 10px 30%;">��������� �� ���� ����� ���������� ���������:</div>

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
print('&nbsp;<img src="http://web.icq.com/whitepages/online?icq=370725770&amp;img=5" alt="" title="��� ������ - � ���� / �� � ����" border="0" width="18" height="18" />' . "\n");
?>
</div>
<!-- END ICQ -->
</li>
<li>
<!-- Form -->
<?php
// �������� ������, ���� ����
if ($contacts_errors = tmpfile_error_show($contacts_temp_file))
	print('<div class="error" onclick="this.style.display = \'none\';"><img src="../img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" />' . "\n" . $contacts_errors . "</div>\n");
if ($fatal_contacts_errors)
    print('<div class="error" onclick="this.style.display = \'none\';"><img src="../img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" />������ ��� ���������� �������, ���������� ��� ���.</div>' ."\n");
if((isset($_GET['mail']) && ($_GET['mail'] == 'not_send')))
    print('<div class="error" onclick="this.style.display = \'none\';"><img src="../img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" />��� �������� ����� ��������� ������, ���������� � �������������� �����.</div>' ."\n");
if((isset($_GET['mail']) && ($_GET['mail'] == 'send')) && (!$fatal_contacts_errors) && (!$contacts_errors))
    print('<div class="no_error" onclick="this.style.display = \'none\';">����� ������� ����������.</div>' ."\n");
?>
<div id="contactserror" class="error" style="display: none;" onclick="this.style.display = 'none'; focusFirst();"><img src="../img/warn.gif" width="16" height="16" alt="" style="position: relative; top: 4px;" /><span id="contactserrorinternal">&nbsp;</span></div>
<?php print(contacts_form($error_name, $error_mail, $error_msg)) ?>
<div><sup style="color: maroon;">*</sup><i style="font-size: 12px;"> - ����������� ��� ����������</i></div>
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