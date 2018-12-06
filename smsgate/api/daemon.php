#!/usr/bin/php -d max_execution_time=60 -q
<?php
/**
 * Service API daemon
 *
 * @version 1.0.0
 * @author Dmitry Ponomarev <ponomarev.base@gmail.com>
 */

setlocale(LC_ALL, "en_US.utf8");
require_once dirname(__FILE__) . '/../conf/conf.php';

define('LOCKER_DIR', dirname(__FILE__) . '/../locks');
define('DAEMON_MAX_ITERATIONS', 10);
define('IMODEM', '/home/ponomarev/smsgate/cli/imodem.php');
define('BALANCE_USSD_COMMAND', '*105#');
define('BALANCE_MIN_THRESHOLD', 2000);
define('SMS_PACKAGE', 10); // сколько sms будет отправлено до следующей проверки баланса
define('SMS_COST', 1); // коэффициент - стоимость sms в рублях
define('MAIL_TO', 'ponomarev.base@gmail.com');
define('MAIL_TO_NAME', 'Dmitry Ponomarev');
define('MAIL_FROM', 'ponomarev.base@gmail.com');
define('MAIL_FROM_NAME', 'SMSGateway');
define('FLASH_MESSAGE', true);

//@todo email если разница между created и сейчас большая и флаг sent=0

// Проверяем, что этот демон еще не запущен
if(!Locker::fileLock('daemon')) {
	exit;
}

DB::connect();

$i = 0;
do {
	// Отсылаем sms-ки из очереди
	if($sms_package = DB::fetchAll("SELECT * FROM `sms` `s` WHERE `s`.`sent`=0 ORDER BY `s`.`created` DESC")) {
		foreach($sms_package as $sms) {
			if(!($modem = get_modem())) {
				exit;
			}
			
			$ssh_command = IMODEM;
			$ssh_command .= " -D=".escapeshellarg($modem['port']);
			$ssh_command .= " --sms-da=".escapeshellarg($sms['da']);
			$ssh_command .= " --sms-text='".base64_encode($sms['text'])."'";
			$ssh_command .= " --sms-text-encoded";
			$ssh_command .= FLASH_MESSAGE ? " --sms-flash" : '';
			$exec_command = sprintf("ssh ".escapeshellarg($modem['host'])." '%s'", $ssh_command);
			
			$output = array();
			$return_var = 0;
			exec($exec_command, $output, $return_var);
			
			Log::debug($exec_command);
			Log::debug($output);
			Log::debug($return_var);
			
			if($return_var === 0) {
				DB::update('sms', array(
					'sent' 		=> 1,
					'modem_id' 	=> $modem['modem_id'],
					'modified' 	=> DB::expr('NOW()'),
				), '`sms_id`=' . (int)$sms['sms_id']);
				DB::update('modems', array('sms_package' => ++$modem['sms_package']), '`modem_id`=' . (int)$modem['modem_id']);
			}
		}
	}
	else {
		exit;
	}
	$i++;
}
while(
	$i < DAEMON_MAX_ITERATIONS
);

/**
 * Функция пытается вернуть модем
 * 
 * @return mixed array|bool
 */
function get_modem() {
	$modems = DB::fetchAll("SELECT * FROM `modems` `m` WHERE 1 AND `m`.`disabled`=0 ORDER BY RAND()");
	foreach($modems as $modem) {
		
		// Пингуем модем
		$ssh_command = IMODEM;
		$ssh_command .= " -D=".escapeshellarg($modem['port']);
		$ssh_command .= " --check";
		$exec_command = sprintf("ssh ".escapeshellarg($modem['host'])." '%s'", $ssh_command);
		$output = array();
		$return_var = 0;
		exec($exec_command, $output, $return_var);
		
		Log::debug($output);
		
		if(($return_var === 0) AND (strpos(implode('', $output), 'OK') !== false)) {
			
			// Проверяем баланс, если нужно
			if(((int)$modem['balance'] === -1) or ((float)($modem['balance'] + $modem['sms_package'] * SMS_COST) < BALANCE_MIN_THRESHOLD) or ($modem['sms_package'] >= SMS_PACKAGE)) {
				if($modem['sms_package'] >= SMS_PACKAGE) {
					DB::update('modems', array('sms_package' => 0), '`modem_id`=' . (int)$modem['modem_id']);
				}
				$ssh_command = IMODEM;
				$ssh_command .= " -D=".escapeshellarg($modem['port']);
				$ssh_command .= " --ussd=".BALANCE_USSD_COMMAND;
				$exec_command = sprintf("ssh ".escapeshellarg($modem['host'])." '%s'", $ssh_command);
				$output = array();
				$return_var = 0;
				exec($exec_command, $output, $return_var);
				
				Log::debug($output);
				
				if($return_var === 0) {
					preg_match('/^.*?(\d+(?:\.?\d+))/', implode('', $output), $m);
					$balance = (float)@$m[1];
					
					DB::update('modems', array('balance' => $balance), '`modem_id`=' . (int)$modem['modem_id']);
					if($balance <= BALANCE_MIN_THRESHOLD) {
						Mail::send(MAIL_TO, MAIL_TO_NAME, MAIL_FROM, MAIL_FROM_NAME, 'SMSGateway: Нулевой баланс у модема ' . $modem['modem_id']);
						continue;
					}
					else {
						// Возвращаем модем
						return $modem;
					}
				}
			}
			else {
				// Возвращаем модем
				return $modem;
			}
		}
	}
	
	// Модем вернуть не удалось
	return false;
}
