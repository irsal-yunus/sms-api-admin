<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

/**
 * @author setia.budi
 * @since 18 May 2010
 *
 */

/**
 * Command Line Interface (CLI) utility class.
 *
 * CHANGES:
 * - 18 May 2010 Buddy <setia.budi@1rstwap.com>
 *		# Make it' object's data object independent
 *		+ Changed static function to object method
 * - 20 Sept 2010 Buddy <setia.budi@1rstwap.com>
 *      # Change members back to static members
 *
 * @author              Patrick Fisher <patrick@pwfisher.com>
 * @since               August 21, 2009
 * @package             Framework
 * @subpackage          Env
 */
class CommandLine {

    private static $arguments=array();

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
	 * CHANGES
	 * - 20 Sept 2010 Buddy <setia.budi@1rstwap.com>
	 *                # Compact code
	 * @author              Patrick Fisher <patrick@pwfisher.com>
	 * @since               August 21, 2009
	 * @see                 http://www.php.net/manual/en/features.commandline.php
	 *                      #81042 function arguments($argv) by technorati at gmail dot com, 12-Feb-2008
	 *                      #78651 function getArgs($args) by B Crawford, 22-Oct-2007
	 * @usage               $args = CommandLine::parseArgs($_SERVER['argv']);
	 * @usage               $args = CommandLine::parseArgs(); //since 18 May 2010
	 * @param array $argv Arguments list
	 */
    public static function parseArgs(array $argv=null){
		if($argv===null) {//enable auto sourcing, Buddy
			$argv = isset($_SERVER['argv'])? $_SERVER['argv'] : array();
		}
		if(empty($argv)){
			return self::$arguments = array();
		}

		array_shift($argv);
		$out = array();

		foreach($argv as $arg){

			// --foo --bar=baz
			if(substr($arg, 0, 2)=='--'){
				$eqPos = strpos($arg, '=');

				// --foo
				if($eqPos===false){
					$key = substr($arg, 2);
					$value = isset($out[$key]) ? $out[$key] : true;
					$out[$key] = $value;
				}
				// --bar=baz
				else{
					$key = substr($arg, 2, $eqPos-2);
					$value = substr($arg, $eqPos+1);
					$out[$key] = $value;
				}
			}
			// -k=value -abc
			elseif(substr($arg, 0, 1)=='-'){

				// -k=value
				if(substr($arg, 2, 1)=='='){
					$key = substr($arg, 1, 1);
					$value = substr($arg, 3);
					$out[$key] = $value;
				}
				// -abc
				else{
					$chars = str_split(substr($arg, 1));
					foreach($chars as $char){
						$key = $char;
						$value = isset($out[$key]) ? $out[$key] : true;
						$out[$key] = $value;
					}
				}
			}
			// plain-arg
			else{
				$value = $arg;
				$out[] = $value;
			}
		}
		self::$arguments = $out;
		return $out;
	}
	/**
	 * Get argument as boolean value
	 * - 18 May 2010 Buddy changed the method name to getAsBoolean
	 * - 20 Sept 2010 Buddy boolean map now is static variable, change type checking order
	 * @param mixed $key Key name
	 * @param bool $default Default value for invalid  / inexistent option
	 */
	public static function getAsBoolean($key, $default = false){
		static $map = array(
			'y'    => true,
			'n'    => false,
			'yes'  => true,
			'no'   => false,
			'true' => true,
			'false'=> false,
			'1'    => true,
			'0'    => false,
			'on'   => true,
			'off'  => false,
		);
		if (!isset(self::$arguments[$key])){
			return $default;
		}
		$value	 = self::$arguments[$key];
		if (is_string($value)){
			$value = strtolower($value);
			if (isset($map[$value])){
				return $map[$value];
			}
		}
		if (is_bool($value)){
			return $value;
		}
		if (is_int($value)){
			return (bool)$value;
		}
		return $default;
	}

	/**
	 * Get parsed arguments
	 * @since 18 May 2010
	 */
	public static function getArguments(){
		return self::$arguments;
	}

	/**
	 * Get argument value
	 * @param mixed $name argument name or index
	 * @return mixed The argument value
	 * @since 20 Sept 2010
	 */
	public static function getArgument($name){
		if(!self::hasArgument($name)){
			trigger_error('Request for undefined argument: '.$name, E_USER_NOTICE);
			return null;
		}
		return self::$arguments[$name];

	}
	
	/**
	 * Check if an argument is exist
	 * @param mixed $name argument name or index
	 * @since 18 May 2010
	 */
	public static function hasArgument($name){
		return isset(self::$arguments[$name]);
	}
}

CommandLine::parseArgs();