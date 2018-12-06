#!/usr/bin/php -d max_execution_time=60 -q 
<?php
//http://www.mc35i-terminal.ru/lib/mc35i_atc_v0103.pdf
/**
 * CLI for working with a modem
 * Use --help flag
 *
 * @version 1.0.0
 * @author Dmitry Ponomarev <ponomarev.base@gmail.com>
 */
setlocale(LC_ALL, "en_US.utf8");
require_once dirname(__FILE__) . '/cli.php';

$argv = CLI::parseArgs();

if(empty($argv) || isset($argv['h']) || isset($argv['help'])) {
	render_help();
	exit;
}
elseif(isset($argv['v']) || isset($argv['version'])) {
	print "1.0.0\n";
	exit;
}

class ArgumentsException extends Exception {}

function render_help() {
	ob_start();
	require_once dirname(__FILE__) . '/help';
	print ob_get_clean();
}

require_once dirname(__FILE__) . '/modem.php';

$output = array();

try {
	
	if(isset($argv['D'])) {
		define('COMPORT', CLI::getStr('D'));
	}
	
	if(isset($argv['m']) || isset($argv['manufacturer'])) {
		$modem = new Modem(true);
	}
	else {
		$modem = new Modem;
	}

	if(isset($argv['c']) || isset($argv['check'])) {
		$output[] = $modem->check();
	}
	if(isset($argv['at'])) {
		$output[] = $modem->at(CLI::getStr('at'));
	}
	if(isset($argv['ussd'])) {
		$output[] = $modem->ussd(CLI::getStr('ussd'));
	}
	if(isset($argv['sms-da'], $argv['sms-text'])) {
		$output[] = $modem->sms(CLI::getStr('sms-da'), isset($argv['sms-text-encoded']) ? base64_decode(CLI::getStr('sms-text')) : CLI::getStr('sms-text'), isset($argv['sms-flash']));
	}

}
catch (ArgumentsException $e) {
	render_help();
	throw new Exception($e->getMessage());
}
catch (Exception $e) {
	$output[] = $e->getMessage();
	print implode('', $output);
	flush();
	// return var 255
	throw new Exception();
}

print implode('', $output);

// return var 0

