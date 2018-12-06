<?php
/**
 * Service API gateway
 *
 * @version 1.0.0
 * @author Dmitry Ponomarev <ponomarev.base@gmail.com>
 */

setlocale(LC_ALL, "en_US.utf8");
require_once dirname(__FILE__) . '/../conf/conf.php';

Log::setLevel(0);
define('SMS_TEXT_MAX_BYTES', 140);
define('DAEMON', '/home/ponomarev/smsgate/api/daemon.php');

// Ответы шлюза
define('RESPONSE_ERROR_EMPTY_REQUEST', 					'ERROR 1: Empty request');
define('RESPONSE_ERROR_INCORRECT_HASH', 				'ERROR 2: Incorrect hash');
define('RESPONSE_ERROR_UNKNOWN_COMMAND', 				'ERROR 3: Unknown command');
define('RESPONSE_ERROR_MISSING_MANDATORY_PARAMETERS', 	'ERROR 4: Missing mandatory parameters');
define('RESPONSE_ERROR_USER_NOT_FOUND_IN_DB', 			'ERROR 5: User not found in database');
define('RESPONSE_ERROR_SMS_TEXT_IS_TOO_LONG', 			'ERROR 6: Sms text is too long (max %d bytes)');
define('RESPONSE_OK_SMS_WAS_ADDED_TO_POOL', 			'OK 1: Sms was added to pool with id=%d');
define('RESPONSE_OK_SMS_IS_IN_THE_QUEUE_FOR_DELIVERY', 	'OK 2: Sms with id=%d is in the queue for delivery');
define('RESPONSE_OK_SMS_SENT', 							'OK 3: Sms with id=%d sent');
define('RESPONSE_ERROR_SMS_DOES_NOT_EXIST', 			'ERROR 7: Sms with id=%d does not exist');

$REQUEST = file_get_contents('php://input');
$REQUEST = JSON::decode($REQUEST);
$RESPONSE = '';

// Чистим request
$REQUEST = array_map(function($v) {return trim($v);}, $REQUEST);

class ApiException extends Exception {}

try {
	// Проверка вводных данных
	if(empty($REQUEST)) {
		throw new ApiException(get_msg('RESPONSE_ERROR_EMPTY_REQUEST'));
	}
	
	DB::connect();

	// Для подписи ответа шлюза необходим id пользователя, сразу убедимся, что он есть
	if(isset($REQUEST['user_id'])) {
		if(!($user = DB::fetchRow("SELECT `u`.`user_id`, `u`.`password` FROM `users` `u` WHERE `u`.`user_id`=? LIMIT 1", (int)$REQUEST['user_id']))) {
			unset($user);
			throw new ApiException(get_msg('RESPONSE_ERROR_USER_NOT_FOUND_IN_DB'));
		}
	}

	if(!in_array($REQUEST['cmd'], array('send_sms', 'get_sms_status'))) {
		throw new ApiException(get_msg('RESPONSE_ERROR_UNKNOWN_COMMAND'));
	}

	if($REQUEST['cmd'] == 'send_sms') {
		if(!isset($REQUEST['user_id'], $REQUEST['cmd'], $REQUEST['da'], $REQUEST['text'], $REQUEST['hash'])) {
			throw new ApiException(get_msg('RESPONSE_ERROR_MISSING_MANDATORY_PARAMETERS'));
		}
	}
	elseif($REQUEST['cmd'] == 'get_sms_status') {
		if(!isset($REQUEST['user_id'], $REQUEST['cmd'], $REQUEST['sms_id'], $REQUEST['hash'])) {
			throw new ApiException(get_msg('RESPONSE_ERROR_MISSING_MANDATORY_PARAMETERS'));
		}
	}

	$_request = $REQUEST;
	unset($_request['hash']);
	unset($_request['textEncoded']);
	$hash = md5(implode('&', $_request) . '&' . $user['password']);
	if($hash != $REQUEST['hash']) {
		throw new ApiException(get_msg('RESPONSE_ERROR_INCORRECT_HASH'));
	}

	if(isset($REQUEST['textEncoded'])) {
		$REQUEST['text'] = base64_decode($REQUEST['text']);
	}
	if(strlen($REQUEST['text']) > SMS_TEXT_MAX_BYTES) {
		throw new ApiException(get_msg('RESPONSE_ERROR_SMS_TEXT_IS_TOO_LONG', SMS_TEXT_MAX_BYTES, array('bytes' => strlen($REQUEST['text']))));
	}

	// Обработка комманды send_sms
	if($REQUEST['cmd'] == 'send_sms') {
		$last_id = DB::insert('sms', array(
			'user_id' 	=> $REQUEST['user_id'],
			'da'	  	=> $REQUEST['da'],
			'text'	  	=> $REQUEST['text'],
			'created'	=> DB::expr('NOW()')	
		));

		// Не дожидаемся запуска демона по крону, вызываем вручную
		exec(DAEMON);

		$RESPONSE = get_msg('RESPONSE_OK_SMS_WAS_ADDED_TO_POOL', $last_id, array('sms_id' => $last_id));
	}
	
	// Обработка комманды get_sms_status
	elseif($REQUEST['cmd'] == 'get_sms_status') {
		$sms_id = (int)$REQUEST['sms_id'];
		$status = DB::fetchOne("SELECT `s`.`sent` FROM `sms` `s` WHERE `s`.`sms_id`=?", $sms_id);
		if(DB::numRows() > 0) {
			$RESPONSE = get_msg('RESPONSE_OK_' . ($status ? 'SMS_SENT' : 'SMS_IS_IN_THE_QUEUE_FOR_DELIVERY'), $sms_id, array('sms_id' => $sms_id, 'delivery_status' => $status));
		}
		else {
			throw new ApiException(get_msg('RESPONSE_ERROR_SMS_DOES_NOT_EXIST', $sms_id));
		}
	}

}
catch(ApiException $e) {
	$RESPONSE = $e->getMessage();
}
catch(Exception $e) {
	$RESPONSE = get_msg('RESPONSE_ERROR_', null, null, $e->getMessage());
}

print $RESPONSE;
flush();

/**
 * Формирует ответ шлюза в виде строки в формате json.
 * Добавляет подпись(hash) ответа.
 * 
 * @param string $constant
 * @param string|array $format_args
 * @param array $body
 * @param string $description
 * @return string
 */
function get_msg($constant, $format_args = null, $body = null, $description = null) {
	global $user;
	$ret = array();
	$arr = explode('_', $constant);
	$ret['status'] = isset($arr[1]) ? $arr[1] : 'FATAL_ERROR';
	$ret['description'] = $description ? $description : sprintf(constant($constant), $format_args);
	if($body !== null) {
		$ret['body'] = $body;
	}
	if(isset($user)) {
		$ret['hash'] = md5($ret['status'] . '&' . $ret['description'] . ($body ? '&' . implode('&', $body) : '') . '&' . $user['password']);
	}
	return JSON::encode($ret);
}