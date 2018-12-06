<?php
if(@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	// Set RPC response headers
	header("Content-Type: application/json");
	header("Content-Encoding: UTF-8");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	//require_once 'FirePHPCore/fb.php';
	require_once 'config.php';
	
	// Add library to the include path
	require_once "../lib/config.php"; 
	require_once "common.lib.php";
	require_once "DbSimple/lib/DbSimple/Generic.php";
	require_once "Tree.class.php";
	
	$db = DbSimple_Generic::connect("mysql://{$CONFIG['db']['login']}:{$CONFIG['db']['password']}@{$CONFIG['db']['host']}/{$CONFIG['db']['database']}?charset={$CONFIG['db']['charset']}");
	$db->setErrorHandler('databaseErrorHandler');
	
	$tree = new Tree($db);
	
	if(@$_GET['action'] == 'get_nodes') {
		print $tree->get_nodes($_GET['parent_id']);
		exit;
	}
	elseif(@$_POST['action'] == 'create') {
		$tree->create($_POST['parent_id'], $_POST['name']);
	}
	elseif(@$_POST['action'] == 'delete') {
		$tree->delete($_POST['id']);
	}
	elseif(@$_POST['action'] == 'rename') {
		$tree->rename($_POST['id'], $_POST['new_name']);
	}
	
	print $tree->ok ? '{result: "ok"}' : '{result: "failed"}';
}
// omit close tag