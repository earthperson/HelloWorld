<?php
class PLib_EM_DB {
    private $config;
    private $charset;
    
    public function __construct($charset = null) {
        $this->config = $GLOBALS['CONFIG']['db'];
        $this->charset = $charset;
    }
    
    public function connect() {
        if(mysql_connect($this->config['host'], $this->config['login'], $this->config['password'])) {
            if(mysql_select_db($this->config['base'])) {
                if(mysql_query("SET CHARACTER SET " . ($this->charset ? $this->charset : $this->config['charset']))) {
                    return true;
                }
            }
        }
        return mysql_error();
    }
}
?>