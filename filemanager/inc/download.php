<?php
require_once 'config.php';

// Add library to the include path
require_once "../lib/config.php"; 
require_once "common.lib.php";
require_once "DbSimple/lib/DbSimple/Generic.php";

$db = DbSimple_Generic::connect("mysql://{$CONFIG['db']['login']}:{$CONFIG['db']['password']}@{$CONFIG['db']['host']}/{$CONFIG['db']['database']}?charset={$CONFIG['db']['charset']}");
//$db->setErrorHandler('databaseErrorHandler');

function download_file($id) {
	global $db;
	$rec = $db->selectRow("SELECT * FROM journal WHERE id = ?d", (int)$id);
	$name = ru2lat(preg_replace("%[\r\n]+%", ' ', $rec['name']));
    $file = "{$_SERVER['DOCUMENT_ROOT']}/var/{$rec['file']}";
    
    if(!is_file($file)) {
        return false;
    }
    
    header('HTTP/1.0 200 OK');
    header('Status: 200 OK');
    header('Accept-Ranges: bytes');
    header('Content-Length: ' . filesize($file));
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header('Content-Type: application/*');

    $handle = @fopen($file, 'rb');
    if ($handle) {
        while(!feof($handle) and !connection_status()) {
            print fread($handle, (1024 * 8));
        }
        return true;
    }
    else {
        return false;
    }
}

if(!download_file($_GET['id'])) {
	header("HTTP/1.0 404 Not Found");
	print 'Not Found' . str_repeat(' ', 1024);
}
// omit close tag