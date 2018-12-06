<?php
class PLib_Autoload {
    public static function register($pear_naming_conventions_lib) {
        spl_autoload_register(create_function('$cn',
        		'$f = "' . $pear_naming_conventions_lib . '/" . str_replace("_", "/", $cn) . ".class.php";
            	if(is_file($f)) {
            		require_once $f;
            	}'
        ));
    }
}
?>