<?php
/**
 * Базовый набор методов для начала работы с БД MySQL
 * $db = new DatabaseCommon([logdir, [server, [username, [passwd, [db_name]]]]]);
 * if(!$db->dbConnectError) {
 *     if($db->dbQuery('....')) {
 *            .... 
 *     }
 *     else {
 *         $db->dbErrors();
 *     }     
 *       $db->dbClose();
 * }
 * else {
 *     $db->dbErrors();
 * }
 * @copyright http://dmitryponomarev.ru
 */
class DatabaseCommon {
    const
    DB_HOST   = 'localhost',
    DB_USER   = 'dmitry',
    DB_PASSWD = 'passwd',
    DB_NAME   = 'forum';
    public $dbLink, $dbResult, $dbConnectError = false, $log_dir;
    private $temp;

    /**
     * Подключиться к серверу MySQL, выбрать БД
     * 
     * @return resource
     *
     */
    public function dbConnect($host, $user, $passwd, $name) {
        // Попытка соединиться с сервером MySQL
        if(!($this->dbLink = @mysql_connect($host, $user, $passwd))) {
            if($this->temp) {
                fwrite($this->temp, "<em>Невозможно подключиться к серверу MySQL!<br />\n" . mysql_errno() . ': ' . mysql_error() . "<br /></em>\n");
                error_log("<em>Невозможно подключиться к серверу MySQL!<br />\n" . mysql_errno() . ': ' . mysql_error() . "<br /></em>\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/' . $this->log_dir . 'db.log');
            }
            $this->dbConnectError = true;
        }
        // Соединились, теперь выбираем БД
        if(!@mysql_query("USE $name", $this->dbLink)) {
            if($this->temp) {
                fwrite($this->temp, "<em>Ошибка в выборе БД!<br />\n" . mysql_errno() . ': ' . mysql_error() . "<br /></em>\n");
                error_log("<em>Ошибка в выборе БД!<br />\n" . mysql_errno() . ': ' . mysql_error() . "<br /></em>\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/' . $this->log_dir . 'db.log');
            }
            $this->dbConnectError = true;
        }
    }

    /**
     * Закрыть соединение с сервером MySQL
     *
     * @return bool
     */
    public function dbClose() {
        if(!mysql_close($this->dbLink)) {
            if($this->temp) {
                fwrite($this->temp, "<em>Ошибка при попытке закрыть соединение с сервером MySQL!<br />\n" . mysql_errno() . ': ' . mysql_error() . "<br /></em>\n");
                error_log("<em>Ошибка при попытке закрыть соединение с сервером MySQL!<br />\n" . mysql_errno() . ': ' . mysql_error() . "<br /></em>\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/' . $this->log_dir . 'db.log');
            }
            return false;
        }
        else {return true;}
    }

    /**
     * Выполняет запрос к БД
     * 
     * @return mixed
     */
    public function dbQuery($query) {
        if(!($this->dbResult = @mysql_query($query, $this->dbLink))) {
            if($this->temp) {
                fwrite($this->temp, "<em>Невозможно выполнит запрос!<br />\n" . mysql_errno() . ': ' . mysql_error() . "<br /></em>\n");
                error_log("<em>Невозможно выполнит запрос!<br />\n" . mysql_errno() . ': ' . mysql_error() . "<br /></em>\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/' . $this->log_dir . 'db.log');
            }
            return false;
        }
        return $this->dbResult;
    }

    /**
    * Функция считывает содержимое временного файла с ошибками, возвращает его содержимое в виде строки,
    * и удаляет временный файл. Если файл пустой возвращает false, в противном случае true
    *
    * @return bool
    */
    public function dbErrors() {
        if($this->temp) {
            rewind($this->temp);
            fpassthru($this->temp);
            fclose($this->temp);
            return true;
        }
        else {return false;}
    }

    public function __construct($log_dir = './', $host = self::DB_HOST, $user = self::DB_USER, $passwd = self::DB_PASSWD, $name = self::DB_NAME) {
        // создаем временный файл ошибок с уникальным именем, он автоматически удаляется
        // после завершении работы скрипта или использования функции fclose();
        $this->temp = tmpfile();
        $this->log_dir = $log_dir;
        self::dbConnect($host, $user, $passwd, $name);
    }
}

/**
 * Класс для вывода сообщения об ошибках с временной записью в файл с
 * уникальным именем для передачи сообщения при перезагрузке страницы
 * 
 * .error {
 *    color: maroon;
 *    font-family: monospace;
 *    font-size: 12px;
 *    border: 1px solid maroon;
 *    background: #fcf;
 *    padding: 2px;
 *    margin: 1px;
 *    margin-bottom: 4px;
 *    text-align: justify;
 *    cursor: default;
 *    z-index: 50;
 * }
 * .no_error {
 *    color: #363;
 *    font-family: monospace;
 *    font-size: 12px;
 *    border: 1px solid #363;
 *    background: #cf9;
 *    padding: 2px;
 *    margin: 1px;
 *    margin-bottom: 4px;
 *    text-align: center;
 *    cursor: default;
 * }
 * <?php
 * Cоздаем экземпляр класса для вывода сообщения об ошибках
 * $el = new ErrorLog('logs/');
 * ......
 * if() {
 *     $el->mysuccess('success');
 * }
 * else {
 *     $el->mylog('error');
 * }
 * ......
 * вывод сообщения об ошибке
 * $el->showlog()
 * ?>
 * @copyright http://dmitryponomarev.ru
 */
class ErrorLog {
    private
    $err_d      = array('none', ''),
    $success_d  = array('none', ''),
    $log_dir,
    $e          = 'e',
    $s          = 's';
    private function logs($msg, $arg, $param, $location) {
        // создать файл с уникальным именем - log file
        $lf = tempnam($this->log_dir, $arg);
        $lfh = fopen($lf, 'w');
        if($lfh) {
            fwrite($lfh, $msg);
            fclose($lfh);
        }
        if($location) {
            header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $arg . '=' . urlencode($lf) . $param);
            exit();
        }
        else {
            return '?' . $arg . '=' . urlencode($lf) . $param;
        }
    }
    private function readlogfile($arg, $file) {
        if($arg == $this->e) {$this->err_d[0] = 'block';}
        if($arg == $this->s) {$this->success_d[0] = 'block';}
        $buff = file_get_contents($file);
        if(strlen($buff) > 0) {
            if($arg == $this->e) {
                $this->err_d[1] = $buff;
            }
            if($arg == $this->s) {
                $this->success_d[1] = $buff;
            }
        }
        unlink($file);
        return true;
    }
    public function mylog($msg, $param = '', $location = true) {
        return $this->logs($msg, $this->e, $param, $location);
    }
    public function mysuccess($msg, $param = '', $location = true) {
        return $this->logs($msg, $this->s, $param, $location);
    }
    public function showlog() {
        if(isset($_GET[$this->e])) {
            if (is_readable(urldecode($_GET[$this->e]))) {
                $this->readlogfile($this->e, urldecode($_GET[$this->e]));
            }
        }
        if(isset($_GET[$this->s])) {
            if (is_readable(urldecode($_GET[$this->s]))) {
                $this->readlogfile($this->s, urldecode($_GET[$this->s]));
            }
        }
        $str = "<!-- showlog -->\n" . '<div id="error_id" class="error" style="display: ' . $this->err_d[0] . '; text-align: left;" onclick="this.style.display = \'none\';">' . "\n" . '<a href="#" class="b_x">[ X ]&nbsp;</a>' . "\n" . '<div><pre id="error_idinternal">&nbsp;' . $this->err_d[1] . '</pre></div></div>' . "\n";

        $str .= "<!-- showlog -->\n" . '<div id="no_error_id" class="no_error" style="display: ' . $this->success_d[0] . '; text-align: left;" onclick="this.style.display = \'none\';">' . "\n" . '<a href="#" class="b_x">[ X ]&nbsp;</a>' . "\n" . '<div><pre>&nbsp;' . $this->success_d[1] . '</pre></div></div>' . "\n";
        return $str;
    }
    public function __construct($dir = './') {
        $this->log_dir = $dir;
    }
} 
?>