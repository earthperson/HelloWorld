<?php
/*DB db.php section*/
define('DB_POOL_LENGTH', 1000);
define('DB_PCONNECT', false);
define('DB_AUTOCOMMIT',false);
define('DB_HOST', '127.0.0.1');
define('DB_USER', '');
define('DB_PASSWD', '');
define('DB_DATABASE', '');
define('DB_PORT', '3306');
define('DB_QUERY_CONNECT', "SET NAMES 'utf8'");
define('DB_SSL', false);
define('DB_DEBUG', false);
require_once(dirname(__FILE__) . "/../lib/lib/autoload.php");
Log::addLevel(Log::LEVEL_DEBUG);
Log::setOutput(Log::OUTPUT_SCREEN)



?>
