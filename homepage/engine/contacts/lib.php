<?php
/**
 * Проверяет правильность заполнения форм в поле отправки почты.
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
		fwrite($contacts_temp_file, 'Ошибка: Поле "Имя:" обязательно для заполнения!<br />' . "\n");
		$test = false;
	}
	//
	if(str_empty($msg)) {
		fwrite($contacts_temp_file, 'Ошибка: Поле "Сообщение:" обязательно для заполнения!<br />');
		$test = false;
	}
	//
	if(!str_valid_name($name)) {
		fwrite($contacts_temp_file, 'Ошибка: Некорректное имя! Имя может содержать любые буквенно-цифровые символы, знак подчеркивания и пробел.<br />' . "\n");
		$test = false;		
	}
	//
	if (strlen($name) < 2) {
		fwrite($contacts_temp_file, 'Ошибка: Имя слишком короткое! Минимум: два символа.<br />' . "\n");
		$test = false;	
	}
	//
	if(str_cutwcrlf($name, CONTACTS_MAX_NAME_LENGTH)) {
		fwrite($contacts_temp_file, 'Ошибка: Максимальная длина поля "Имя:" ' . CONTACTS_MAX_NAME_LENGTH . ' символов! Имя было укорочена до ' . CONTACTS_MAX_NAME_LENGTH . ' символов.<br />' . "\n");
		$test = false;	
	}
	//
	if(str_cutwcrlf($msg, CONTACTS_MAX_MSG_LENGTH)) {		
		fwrite($contacts_temp_file, 'Ошибка: Максимальная длина поля "Сообщение:" ' . CONTACTS_MAX_MSG_LENGTH . ' буквенно-цифровых символов символов (Символы переноса строки в это количество не входят)! Запись была укорочена до ' . CONTACTS_MAX_MSG_LENGTH . ' символов.<br />' . "\n");
		$test = false;	
	}
	//
	if(!str_empty($mail)) {
		if(!str_valid_mail($mail)) {
			fwrite($contacts_temp_file, 'Ошибка: E-mail имеет недопустимый формат.<br />' . "\n");
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