<?php
/**
 * CommandLine class
 *
 * @package             Framework
 */
/**
 * Command Line Interface (CLI) utility class.
 *
 * @author              Patrick Fisher <patrick@pwfisher.com>
 * @since               August 21, 2009
 * @package             Framework
 * @subpackage          Env 
 * @note 				Modified by r.osmanov
 */
class CLI {

	public static $args;
	private static $_pocess = array();

	/**
	 * PARSE ARGUMENTS
	 *
	 * This command line option parser supports any combination of three types
	 * of options (switches, flags and arguments) and returns a simple array.
	 *
	 * [pfisher ~]$ php test.php --foo --bar=baz
	 *   ["foo"]   => true
	 *   ["bar"]   => "baz"
	 *
	 * [pfisher ~]$ php test.php -abc
	 *   ["a"]     => true
	 *   ["b"]     => true
	 *   ["c"]     => true
	 *
	 * [pfisher ~]$ php test.php arg1 arg2 arg3
	 *   [0]       => "arg1"
	 *   [1]       => "arg2"
	 *   [2]       => "arg3"
	 *
	 * [pfisher ~]$ php test.php plain-arg --foo --bar=baz --funny="spam=eggs" --also-funny=spam=eggs \
	 * > 'plain arg 2' -abc -k=value "plain arg 3" --s="original" --s='overwrite' --s
	 *   [0]       => "plain-arg"
	 *   ["foo"]   => true
	 *   ["bar"]   => "baz"
	 *   ["funny"] => "spam=eggs"
	 *   ["also-funny"]=> "spam=eggs"
	 *   [1]       => "plain arg 2"
	 *   ["a"]     => true
	 *   ["b"]     => true
	 *   ["c"]     => true
	 *   ["k"]     => "value"
	 *   [2]       => "plain arg 3"
	 *   ["s"]     => "overwrite"
	 *
	 * @author              Patrick Fisher <patrick@pwfisher.com>
	 * @since               August 21, 2009
	 * @see                 http://www.php.net/manual/en/features.commandline.php
	 *                      #81042 function arguments($argv) by technorati at gmail dot com, 12-Feb-2008
	 *                      #78651 function getArgs($args) by B Crawford, 22-Oct-2007
	 * @usage               $args = CommandLine::parseArgs($_SERVER['argv']);
	 */
	public static function parseArgs($defaultArgv = array(), $helpArgv = array()){

		$argv = $_SERVER["argv"];
		 
		array_shift($argv);
		$out = array();

		foreach ($argv as $arg){

			// --foo --bar=baz
			if (substr($arg,0,2) == '--'){
				$eqPos = strpos($arg,'=');

				// --foo
				if ($eqPos === false){
					$key = substr($arg,2);
					$value = isset($out[$key]) ? $out[$key] : true;
					$out[$key] = $value;
				}
				// --bar=baz
				else {
					$key = substr($arg,2,$eqPos-2);
					$value = substr($arg,$eqPos+1);
					$out[$key] = $value;
				}
			}
			// -k=value -abc
			else if (substr($arg,0,1) == '-'){

				// -k=value
				if (substr($arg,2,1) == '='){
					$key = substr($arg,1,1);
					$value = substr($arg,3);
					$out[$key] = $value;
				}
				// -abc
				else {
					$chars = str_split(substr($arg,1));
					foreach ($chars as $char){
						$key = $char;
						$value = isset($out[$key]) ? $out[$key] : true;
						$out[$key] = $value;
					}
				}
			}
			// plain-arg
			else {
				$value = $arg;
				$out[] = $value;
			}
		}
		$out = array_merge($defaultArgv, $out);
		self::$args = $out;
		if(isset($out["help"])) {
			foreach($defaultArgv as $a => $v) {
				echo "\t$a - ".var_export($a)." ";
				if(isset($helpArgv[$a]))
					echo "\t".$helpArgv[$a];
				echo "\n";
			}
		}
		return $out;
	}

	/**
	 * GET STR
	 */
	public static function getStr($key, $default=""){
		if (!isset(self::$args[$key]))
		return $default;
		return (string)(self::$args[$key]);
	}
}
?>
