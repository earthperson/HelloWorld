<?php 
// Ignore user aborts and allow the script
// to run forever
ignore_user_abort(true);
set_time_limit(0);
// Turn off output buffering
ini_set('output_buffering', 'off');
// Turn off PHP output compression
ini_set('zlib.output_compression', false);
// Implicitly flush the buffer(s)
ini_set('implicit_flush', true);
ob_implicit_flush(true);
class Loader {
	
	public $cursor = array(
		0   => "—\r",
		45  => "/\r",
		90  => "|\r",
		135 => "\\\r",
		180 => "—\r",
		225 => "/\r",
		270 => "|\r",
		315 => "\\\r",
	);
	
	public $position = 0;
	
	public function render() {
		$this->position += 45;
		if($this->position > 315) {
			$this->position = 0;
		}
		echo $this->cursor[$this->position];
		flush();
	}
	
	public function __destruct() {
		echo "\r";
		flush();
	}
}
$loader = new Loader();
while (true) {
	$loader->render();
	usleep(100000);
}
