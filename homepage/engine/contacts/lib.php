<?php
/**
 * ��������� ������������ ���������� ���� � ���� �������� �����.
 *
 * @param string $name
 * @param string $msg
 * @param resource $contacts_temp_file
 * @return mixed
 */
 function contacts_test($name, $mail, $msg, $contacts_temp_file) {
    if($contacts_temp_file) {
	$test = true;
	$name = trim($name);
	$mail = trim($mail);
	$msg = trim($msg);
	if(str_empty($name)) {
		fwrite($contacts_temp_file, '������: ���� "���:" ����������� ��� ����������!<br />' . "\n");
		$test = false;
	}
	//
	if(str_empty($msg)) {
		fwrite($contacts_temp_file, '������: ���� "���������:" ����������� ��� ����������!<br />');
		$test = false;
	}
	//
	if(!str_valid_name($name)) {
		fwrite($contacts_temp_file, '������: ������������ ���! ��� ����� ��������� ����� ��������-�������� �������, ���� ������������� � ������.<br />' . "\n");
		$test = false;		
	}
	//
	if (strlen($name) < 2) {
		fwrite($contacts_temp_file, '������: ��� ������� ��������! �������: ��� �������.<br />' . "\n");
		$test = false;	
	}
	//
	if(str_cutwcrlf($name, CONTACTS_MAX_NAME_LENGTH)) {
		fwrite($contacts_temp_file, '������: ������������ ����� ���� "���:" ' . CONTACTS_MAX_NAME_LENGTH . ' ��������! ��� ���� ��������� �� ' . CONTACTS_MAX_NAME_LENGTH . ' ��������.<br />' . "\n");
		$test = false;	
	}
	//
	if(str_cutwcrlf($msg, CONTACTS_MAX_MSG_LENGTH)) {		
		fwrite($contacts_temp_file, '������: ������������ ����� ���� "���������:" ' . CONTACTS_MAX_MSG_LENGTH . ' ��������-�������� �������� �������� (������� �������� ������ � ��� ���������� �� ������)! ������ ���� ��������� �� ' . CONTACTS_MAX_MSG_LENGTH . ' ��������.<br />' . "\n");
		$test = false;	
	}
	//
	if(!str_empty($mail)) {
		if(!str_valid_mail($mail)) {
			fwrite($contacts_temp_file, '������: E-mail ����� ������������ ������.<br />' . "\n");
			$test = false;
		}
	}
	
	if($test) return true;
	else return array($name, $mail, $msg);
    }
    else
        return false;
}
?>