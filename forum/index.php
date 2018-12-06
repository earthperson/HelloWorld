<?php
// ������ ����� ����� ��� ������ cookie ������� ������
session_set_cookie_params(0);
// ���������������� ������
session_start();
// ������ ����������� ������
ob_start();
// ���������� ����������� �����
require_once('engine/classes.php');
require_once('engine/config.php');
require_once('engine/lib/common.php');
require_once('engine/lib/topic.php');
require_once('engine/lib/request.php');
require_once('engine/lib/response.php');

cache0();

// ������� ��������� ������ ��� ������ ��������� �� �������
$el = new ErrorLog('logs/');
// ������� ��������� ������ ��� ������ � ��
$db = new DatabaseCommon('logs/', DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
?>
<html>
<head>
<title>�����</title>
<meta http-equiv="content-type" content="text/html; charset=windows-1251" />
<style type="text/css">
	@import "css/styles.css";
</style>
</head>
<body>
<?php
// ����� ��������� �� �������
print($el->showlog());
?>
<h3>�����</h3>
<?php
// +-------------------------------------------------------------------------------------------+
// | ���� �����������                                                                          |
// +-------------------------------------------------------------------------------------------+
if(isset($_REQUEST['registrationForm'])) {
	if(!str_empty($_REQUEST['login']) && !str_empty($_REQUEST['passwd']) && !str_empty($_REQUEST['name'])) {
		// ����� ������� � ������� registration �� ������� ���������� ������
		if(registrationConcurrence($_REQUEST['login'], $db, $el)) {
			// ���� ���� ����������
			$el->mylog("����� ��� ���������������!\r\n");
		}
		else {
			// ���� ���������� ��� �� ������� ����� ������
			if(registrationWrite($_REQUEST['login'], $_REQUEST['passwd'], $_REQUEST['name'], $db)) {
				$el->mysuccess("�������� �����������!\r\n");
			}
			else {
				$el->mylog("������ ��� ������ ��������������� ������!\r\n");
			}
		}
	}
}
// +-------------------------------------------------------------------------------------------+
// | ���� �����������                                                                          |
// +-------------------------------------------------------------------------------------------+
if(isset($_REQUEST['authorisationForm'])) {
	if(!str_empty($_REQUEST['login']) && !str_empty($_REQUEST['passwd'])) {
		if(!(($_REQUEST['login'] == ADMIN_LOGIN) && ($_REQUEST['passwd'] == ADMIN_PASSWD))) {
			// ����� ������� � ������� registration �� ������� ���������� ������ � ������
			// ���� ���� ���������� �������� �� �������� �����������, ��������� �������� ���������� ������
			if(authorisationConcurrence($_REQUEST['login'], $_REQUEST['passwd'], $db, $el)) {
				$el->mysuccess("�������� �����������!\r\n");
			}
			else {
				$el->mylog("������������ ����� ��� ������!\r\n");
			}
		}
		else {
			$_SESSION['admin'] = 'admin';
		}
	}
}
// +-------------------------------------------------------------------------------------------+
// | ���� ����������� ������                                                                   |
// +-------------------------------------------------------------------------------------------+
if(isset($_REQUEST['sess']) && isset($_REQUEST['sess']) == 'destroy') {
	// ������� ��� ���������� ������
	session_unset();
	// ��������� ������
	session_destroy();
}
// +-------------------------------------------------------------------------------------------+

if(!(isset($_SESSION['username']) || isset($_SESSION['admin']))) {
	if(isset($_REQUEST['registration']) && $_REQUEST['registration'] == 'show') {
		print authorisation_form(0);
		print registration_form();
	}
	else {
		print authorisation_form(1);
	}
}
else if(isset($_SESSION['username']) && !isset($_SESSION['admin'])){
	print '<div style="padding: 4px; color: navy;"><b>������, ' . ucfirst($_SESSION['username']) . '</b>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?sess=destroy">�����</a></div>' . "\n";
}
else {
	print '<div style="padding: 4px; color: navy;"><b>������, ' . ucfirst($_SESSION['admin']) . '</b>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?sess=destroy">�����</a></div>' . "\n";
}
if(!$db->dbConnectError) {
	// ����������� ������
	if(tables_install($db)) {
		// +-------------------------------------------------------------------------------------------+
		// | ���� ���                                                                                  |
		// +-------------------------------------------------------------------------------------------+
		if(!isset($_REQUEST['topicID']) && !isset($_REQUEST['requestID'])) {
			// ���� ����� �������� ����
			if(isset($_REQUEST['topicSbmt'])) {
				if(!topicWrite($_REQUEST['poster'], $_REQUEST['topicTitle'], $_REQUEST['requestTitle'], $_REQUEST['requestBody'], $db) and !isset($_GET['err'])) {
					$el->mylog("������ ��� ������ ����!\r\n", '&err=1');
				}
			}
			// ���� ����� ������� ����
			if(isset($_REQUEST['topicDel'])) {
				if(!topicDel($_REQUEST['topicDel'], $db) and !isset($_GET['err'])) {
					$el->mylog("������ ��� �������� ����!\r\n", '&err=1');
				}
			}
			// �������� �������� ��� ������
			topicLine();
			if(!showTopics($db) and !isset($_GET['err'])) {
				$el->mylog("������ ��� ������� ������� ��� ������!\r\n", '&err=1');
			}
			// �������� ����� ��� ���������� ����� ����
			if(isset($_SESSION['username']) && !isset($_SESSION['admin'])) {
				topicForm($_SESSION['username']);
			}
			else if(isset($_SESSION['admin'])) {
				topicForm($_SESSION['admin']);
			}
		}
		// +-------------------------------------------------------------------------------------------+
		// | ���� ��������                                                                             |
		// +-------------------------------------------------------------------------------------------+
		else if(!isset($_REQUEST['requestID'])) {
			// ���� ����� �������� ����� ������ � ����
			if(isset($_REQUEST['requestSbmt'])) {
				if(!requestWrite($_REQUEST['poster'], $_REQUEST['requestParent'], $_REQUEST['requestTitle'], $_REQUEST['requestBody'], $db) and !isset($_GET['err'])) {
					$el->mylog("������ ��� ������ ����!\r\n", '&err=1');
				}
			}
			// ���� ����� ������� ������
			if(isset($_REQUEST['requestDel'])) {
				if(!requestDel($_REQUEST['topicID'], $_REQUEST['requestDel'], $db) and !isset($_GET['err'])) {
					$el->mylog("������ ��� �������� �������!\r\n", '&err=1');
				}
			}
			requestLine();
			// �������� �������� ���������� �������� ����
			if(!showRequest($_REQUEST['topicID'], $db) and !isset($_GET['err'])) {
				$el->mylog("������ ��� ������� ������� ���������� �������� ����!\r\n", '&err=1');
			}
			// �������� ����� ��� ���������� ������ ������� ����
			if(isset($_SESSION['username']) && !isset($_SESSION['admin'])) {
				requestForm($_REQUEST['topicID'], $_SESSION['username']);
			}
			else if(isset($_SESSION['admin'])) {
				requestForm($_REQUEST['topicID'], $_SESSION['admin']);
			}
		}
		// +-------------------------------------------------------------------------------------------+
		// | ���� �������                                                                              |
		// +-------------------------------------------------------------------------------------------+
		else {
			// ���� ����� �������� ����� �����
			if(isset($_REQUEST['responseSbmt'])) {
				if(!responseWrite($_REQUEST['poster'], $_REQUEST['topicID'], $_REQUEST['responseParent'], $_REQUEST['responseBody'], $db) and !isset($_GET['err'])) {
					$el->mylog("������ ��� ������ ������!\r\n", '&err=1');
				}
			}
			// ���� ����� ������� �����
			if(isset($_REQUEST['responseDel'])) {
				if(!responseDel($_REQUEST['topicID'], $_REQUEST['requestID'], $_REQUEST['responseDel'], $db) and !isset($_GET['err'])) {
					$el->mylog("������ ��� �������� ������!\r\n", '&err=1');
				}
			}
			responseLine1($_REQUEST['topicID']);
			// �������� ���� ������� ����
			if(!showRequestBody($_REQUEST['requestID'], $db) and !isset($_GET['err'])) {
				$el->mylog("������ ��� ������� ���� ������� ����!\r\n", '&err=1');
			}
			responseLine2();
			// �������� �������� �������
			if(!showResponse($_REQUEST['topicID'], $_REQUEST['requestID'], $db) and !isset($_GET['err'])) {
				$el->mylog("������ ��� ������� ���� ������� ����!\r\n", '&err=1');
			}
			// �������� ����� ��� ���������� ������
			if(isset($_SESSION['username']) && !isset($_SESSION['admin'])) {
				responseForm($_REQUEST['topicID'], $_REQUEST['requestID'], $_SESSION['username']);
			}
			else if(isset($_SESSION['admin'])) {
				responseForm($_REQUEST['topicID'], $_REQUEST['requestID'], $_SESSION['admin']);
			}
		}
	}
	else {
		$db->dbErrors();
	}
	$db->dbClose();
}
else {
	$db->dbErrors();
}
?>
</body>
</html>
<?php
// ���������� ���������� ������ ������ � ��������� ����������� ������
ob_end_flush();
?>