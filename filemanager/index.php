<?php
/*  Copyright (c) 2009  Dmitry Ponomarev (E-mail : ponomarev.base@gmail.com) */

require_once 'inc/config.php';

header("Content-Type: text/html; charset=utf-8");

// Add library to the include path
require_once "lib/config.php"; 
require_once "common.lib.php";
require_once "DbSimple/lib/DbSimple/Generic.php";

$db = DbSimple_Generic::connect("mysql://{$CONFIG['db']['login']}:{$CONFIG['db']['password']}@{$CONFIG['db']['host']}/{$CONFIG['db']['database']}?charset={$CONFIG['db']['charset']}");
$db->setErrorHandler('databaseErrorHandler');

if(isset($_POST['action'], $_POST['parent_id']) && $_POST['action'] == 'upload') {
	if($_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file = md5(uniqid(rand(), true));
        if(move_uploaded_file($_FILES['file']['tmp_name'], "{$_SERVER['DOCUMENT_ROOT']}/var/{$file}")) {
            $db->query("INSERT INTO
            				journal
            			SET
            				parent_id = ?d,
            				file = ?,
            				name = ?",
            			(int)$_POST['parent_id'],
            			$file,
            			basename($_FILES['file']['name'])
            );
        }
    }
    header("Location: http://{$_SERVER['HTTP_HOST']}");
    exit;
}

require_once "Tree.class.php";
$tree = new Tree($db);
$TPLD = array();
$TPLD['title'] = 'File Manager as abstract task';
$TPLD['stat_tree'] = $tree->get_stat();

require_once 'inc/template.php';
?>
