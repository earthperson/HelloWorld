<?php
class PLib_EM_Emulator {
    public static function plain_text($source) {
        return strtr($source, array(
            '<' => "&lt;",
            '>' => "&gt;",
            '"' => "&quot;",
            "'" => "&#039;"
            ));
    }
    
    public static function br_plain_text($source) {
        return nl2br($this->plain_text($source));
    }
    
    public static function get_property($key) {
        $res = mysql_query("SELECT value FROM property WHERE name = '" . mysql_real_escape_string($key) . "' LIMIT 1");
        return mysql_num_rows($res) ? mysql_result($res, 0) : false;
    }
}
?>