<?php
error_reporting(E_ALL | E_STRICT);
require_once 'config.inc.php';

set_time_limit($CONFIG['general']['time_limit']);

@mysql_connect($CONFIG['db']['host'], $CONFIG['db']['login'], $CONFIG['db']['password']) or die();
mysql_select_db($CONFIG['db']['base']) or die();
mysql_query("SET CHARACTER SET {$CONFIG['db']['charset']}");

require_once 'PLib/Common.class.php';
require_once 'lib/DbManager.class.php';

try {
    $dbmanager = new DbManager;
    if(@$_POST['action'] == 'Import') {
        $dbmanager->import($_POST['new_project']);
    }
    elseif(@$_GET['action'] == 'Export') {
        $dbmanager->export($_GET['project']);
    }
}
catch(Exception $e) {
    setcookie('action-status', 'failed');
    header("Location: http://{$_SERVER['HTTP_HOST']}");
    exit();
}
?>