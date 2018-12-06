<?php
ini_set('display_errors', 0);

if(@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    // Set RPC response headers
    header("Content-Type: text/html");
    header("Content-Encoding: UTF-8");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    
    require_once '../../conf/conf.php';
    Log::setLevel(0);
    
    DB::connect();
    
    $password = DB::fetchOne("SELECT `u`.`password` FROM `users` `u` WHERE `u`.`user_id`=? LIMIT 1", (int)$_REQUEST['user_id']);
    
	foreach($_REQUEST as &$item) {
		$item = trim($item);
	}
	
	print md5(implode('&', $_REQUEST).'&'.$password);
	flush();
}