<?php
if(@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    error_reporting(E_ALL | E_STRICT);
    //date_default_timezone_set('UTC');
    putenv("TZ=UTC");
    
    require_once 'config.inc.php';
    require_once 'lib/emulator.lib.php';
    set_time_limit($CONFIG['general']['time_limit']);
    header("Content-type: application/json; charset={$CONFIG['general']['charset']}");
    
    @mysql_connect($CONFIG['db']['host'], $CONFIG['db']['login'], $CONFIG['db']['password']) or die(json_encode(array('error'=>mysql_error())));
    mysql_select_db($CONFIG['db']['base']) or die(json_encode(array('error'=>mysql_error())));
    mysql_query("SET CHARACTER SET {$CONFIG['db']['charset']}");
    
    require_once '../PLib/Common.class.php';
    require_once 'lib/Processor.class.php';
    $error = '';
    try {
        $proc = new Processor(@$_POST['project']);
        if(@$_POST['action'] == 'Reset') {
            $proc->reset();
        }
        elseif(@$_POST['action'] == 'Start') {
            $proc->action();
        }
    }
    catch(Exception $e) {
        $error = $e->getMessage();
    }
    print json_encode(array('progress'=>$proc->progress(), 'error'=>$error));
}
// omit close tag