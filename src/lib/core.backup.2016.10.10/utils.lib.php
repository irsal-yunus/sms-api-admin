<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

final class SmsApiAdminUtility{
	const HELP_OPTION_NAME = 'help';
	const APP_USER_NAME = 'system';
	const APP_USER_ID = 0;
	/**
	 * Standar logger
	 * @var Logger
	 */
	private static $logger;
	private static $utilityName = null;
	private static $db = array();

	public static function init($utilName=null){
		if(self::$utilityName !== null)
			return;
		if($utilName===null){
			$utilName = basename($_SERVER['SCRIPT_NAME'], '.php');
		}
		self::$utilityName = (string) $utilName;
		self::$logger = Logger::getRootLogger("$utilName");
		self::checkHelpRequest();
		self::showTitle();
		$dbList = parse_ini_file(SMSAPIADMIN_CONFIG_DIR.'database.ini', true);
		if(!is_array($dbList)){
			self::$logger->warn('Invalid database configuration!');
		}else{
			self::$db = $dbList;
		}
	}
	/**
	 * Exception handler
	 * @param Exception $e
	 */
	public static function catchException(Exception $e) {
		try {
			if(self::$logger){
				self::$logger->fatal("$e");
			}else{
				error_log("$e");
			}
			self::forceShutDown($e->getMessage());
		} catch (Exception $e) {
			error_log("Uncaught exception: $e");
			exit(1);
		}
	}
	/**
	 * Error handler
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 * @return bool
	 */
	public static function catchError($errno, $errstr, $errfile, $errline) {
		try {
			if(self::$utilityName !== null){
				$tag = '['.self::$utilityName.'] ';
			}else{
				$tag = '';
			}
			$msg = "Caught an error with message '$errstr' in $errfile:$errline";
			switch ($errno){
				case E_RECOVERABLE_ERROR :
					if(self::$logger){
						self::$logger->error($msg);
					}else{
						error_log("$tag$msg");
					}
					return true;
				case E_WARNING :
				case E_USER_WARNING :
				case E_CORE_WARNING :
				case E_COMPILE_WARNING :
					if(self::$logger){
						self::$logger->warn($msg);
					}else{
						error_log("$tag$msg");
					}
					return true;
				case E_STRICT :
//				case E_DEPRECATED : //PHP 5.3
//				case E_USER_DEPRECATED : //PHP 5.3
				case E_NOTICE :
				case E_USER_NOTICE :
					if(self::$logger){
						self::$logger->info($msg);
					}else{
						error_log("$tag$msg");
					}
					return true;
				default:
					
			}
		} catch (Exception $e) {
			self::catchException($e);
		}
	}
	/**
	 * get logger
	 * @return Logger
	 */
	public static function getLogger() {
		return self::$logger;
	}
	/**
	 * terminate application
	 * @param string $text
	 */
	public static function forceShutDown($message) {
		self::writeLn("\nERROR: $message");
		if(self::$logger)
			self::$logger->info("Application terminated!");
		exit(1);
	}
	/**
	 * Print array key=>value mappings
	 * @param string $text
	 */
	public static function writeArrayMap(array $array, $format="%s:%s") {
		if(empty($array))
			return;
		foreach($array as $key=>$value){
			self::writeLn(sprintf($format, $key, $value));
		}
	}
	/**
	 * Print array contents as table
	 * @param string $text
	 */
	public static function writeArrayTable(array $array, $showKey=false, $format="%s") {
		if(empty($array))
			return;
		if($showKey){
			foreach($array as $key=>$value){
				self::writeLnFormatted($format, array_merge(array($key), (array) $value));
			}
		}else{
			foreach($array as $value){
				self::writeLnFormatted($format, (array) $value);
			}
		}
	}
	/**
	 * Print text to console with new line
	 * @param string $text
	 */
	public static function writeLn($text='') {
		echo $text.PHP_EOL;
	}
	/**
	 * Print text to console with format
	 * @param string $format
	 * @param array $arguments
	 */
	public static function writeFormatted($format, array $arguments) {
		vprintf($format, $arguments);
	}
	/**
	 * Print text to console with format and new line
	 * @param string $format
	 * @param array $arguments
	 */
	public static function writeLnFormatted($format, array $arguments) {
		vprintf($format, $arguments);
		echo PHP_EOL;
	}
	/**
	 * Print text to console
	 * @param string $text
	 */
	public static function write($text) {
		echo $text;
	}
	/**
	 * Print text to console
	 * @param string $text
	 */
	public static function writeLnAndLog($text) {
		self::writeLn($text);
		self::$logger->info($text);
	}
	/**
	 * Print tutorial
	 * @param string $module module name
	 */
	public static function showHelp() {
		$module = self::$utilityName;
		if(empty($module))
			self::forceShutDown('Invalid module: '.$module);
		self::showTitle();
		$helpFile = SMSAPIADMIN_UTILS_DOC_DIR.$module.'.txt';
		if(!file_exists($helpFile)){
			self::writeLn('Help file was not found!');
		}elseif(!is_readable($helpFile)){
			self::writeLn('Help file is not accessible!');
		}else{
			readfile($helpFile);
			self::writeLn();
		}
		self::writeLn();
		exit(0);
	}
	/**
	 * Check if --help option is specified and print tutorial
	 */
	private static function checkHelpRequest() {
		if(!CommandLine::hasArgument(self::HELP_OPTION_NAME))
			return;
		self::showHelp();
	}
	/**
	 * Print copyright information
	 */
	public static function showTitle() {
		self::writeLn();
		readfile(SMSAPIADMIN_UTILS_DOC_DIR.'HEADER');
		self::writeLn();
		self::showIntro();
		self::writeLn();
	}
	
	/**
	 * Print utility introduction
	 */
	public static function showIntro() {
		$introFile = SMSAPIADMIN_UTILS_DOC_DIR.self::$utilityName.'.intro';
		if(!file_exists($introFile) || !is_readable($introFile) || !is_file($introFile)){
			return;
		}
		self::writeLn();
		readfile($introFile);
		self::writeLn();
	}



	/**
	 *
	 * @param string $profile
	 * @return PDO
	 */
	public static function connectDB($profile='default') {
		if(!isset(self::$db[$profile])){
			throw new Exception("Undefined database profile: $profile");
		}
		$config = self::$db[$profile];
		$db = new PDO($config['dsn'], $config['username'], $config['password']);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $db;
	}
	public static function saveRowsToCsv(PDOStatement $result, $filePath) {
		$fileHandle = fopen($filePath, 'w');
		if($fileHandle === false)
			throw new Exception('Could not create file');
		$columnsCount = $result->columnCount();
		$columnsNames = array();
		if(!$columnsCount){
			self::$logger->info("No column was found in the result. Nothing is recorded to $filePath");
			fclose($fileHandle);
			return;			
		}
		for($i=0; $i<$columnsCount; ++$i){
			$columnMeta = $result->getColumnMeta($i);
			$columnsNames[$i] = $columnMeta['name'];
		}
		$isWritten = fputcsv($fileHandle,
								$columnsNames,
								SMSAPIADMIN_ARCHIEVE_CSV_DELIMITER,
								SMSAPIADMIN_ARCHIEVE_CSV_ENCLOSURE);
		if($isWritten === false){
			fclose($fileHandle);
			throw new Exception("Failed writing CSV header to $filePath");
		}

		if(!$result->rowCount()){
			self::$logger->info("No row was found in the result");
			fclose($fileHandle);
			return;
		}

		$result->setFetchMode(PDO::FETCH_NUM);
		$rowIdx = 0;
		if($result->rowCount()){
			while($row = $result->fetch()){
				$isWritten = fputcsv($fileHandle,
										$row,
										SMSAPIADMIN_ARCHIEVE_CSV_DELIMITER,
										SMSAPIADMIN_ARCHIEVE_CSV_ENCLOSURE);
				if($isWritten === false){
					fclose($fileHandle);
					throw new Exception("Failed writing CSV rows at index-$rowIdx to $filePath");
				}
				++$rowIdx;
			}
		}
		fclose($fileHandle);
	}
	/**
	 * Compress file
	 * @param string $source
	 * @param string $output
	 */
	public static function compress($source, $output) {
		//check input
		$sourceFile = filter_var($source,
							FILTER_SANITIZE_STRING,
							array('flags'=>
								FILTER_FLAG_STRIP_LOW)
							);
		if(($sourceFile === false) || (trim($sourceFile) == '')){
			throw new Exception("Invalid source file name: $source");
		}elseif(!is_readable($sourceFile)){
			throw new Exception('Source file is not readable');
		}
		//check output
		$outputFile = filter_var($output,
							FILTER_SANITIZE_STRING,
							array('flags'=>
								FILTER_FLAG_STRIP_LOW)
							);

		if(($outputFile === false) || (trim($outputFile) == '')){
			throw new Exception("Invalid output file name: $output");
		}elseif(file_exists($outputFile) && !is_dir($outputFile)){
			self::$logger->info("Output file <$outputFile> has existed before and may be replaced");
		}

		$sourceFile = escapeshellcmd($sourceFile);
		$outputFile = escapeshellcmd($outputFile);
		$workDir = escapeshellcmd(dirname($sourceFile));

		$command = str_replace(array('{SRC-NAME}', '{OUT-NAME}'),
								array($sourceFile, $outputFile),
								SMSAPIADMIN_ARCHIEVE_CMD_CREATE);
		$cmdOutput = array();
		$cmdReturn = null;
		
		self::writeLnAndLog("[".__METHOD__."] Compression command: $command");
		self::writeLn("[".__METHOD__."] Compressing... ");
		exec($command, $cmdOutput, $cmdReturn);
		self::writeLn("Compression was done");
		self::$logger->debug("Command output: ".implode(PHP_EOL, $cmdOutput));
		self::writeLn("[".__METHOD__."] Executed command returned status $cmdReturn");
	}
	/**
	 * Decompress file
	 * @param string $source
	 * @param string $output
	 */
	public static function decompress($source, $output=null) {
		//check input
		$sourceFile = filter_var($source,
							FILTER_SANITIZE_STRING,
							array('flags'=>
								FILTER_FLAG_STRIP_LOW)
							);
		if(($sourceFile === false) || (trim($sourceFile) == '')){
			throw new Exception("Invalid source file name: $source");
		}elseif(!is_readable($sourceFile)){
			throw new Exception('Source file is not readable');
		}
		if($output===null){
			$outputFile = '';
		}else{
			//check output
			$outputFile = filter_var($output,
								FILTER_SANITIZE_STRING,
								array('flags'=>
									FILTER_FLAG_STRIP_LOW)
								);

			if(($outputFile === false) || (trim($outputFile) == '')){
				throw new Exception("Invalid source file name: $output");
			}elseif(file_exists($outputFile) && !is_dir($outputFile)){
				self::$logger->info("Output file <$outputFile> has existed before and may be replaced");
			}
		}
		$sourceFile = escapeshellcmd($sourceFile);
		$outputFile = escapeshellcmd($outputFile);
		$command = str_replace(array('{SRC-NAME}', '{OUT-NAME}'),
								array($sourceFile, $outputFile),
								SMSAPIADMIN_ARCHIEVE_CMD_EXTRACT);
		$cmdOutput = array();
		$cmdReturn = null;
		self::writeLnAndLog("[".__METHOD__."] About to execute decompression command: $command");
		self::writeLn("[".__METHOD__."] Decompressing... ");
		exec($command, $cmdOutput, $cmdReturn);
		self::writeLn("Decompression was done");
		self::$logger->debug("Command output: ".implode(PHP_EOL, $cmdOutput));
		self::writeLn("[".__METHOD__."] Executed command returned status $cmdReturn");
	}

	/**
	 *
	 * @param string $question
	 * @param int $inputMaxLength
	 * @param array $validAnswers
	 * @param int $maxAttempt
	 * @return string
	 */
	public static function prompt($question, $inputMaxLength=255, array $validAnswers=null, $maxAttempt=0){
		if($inputMaxLength <= 0){ //input length must always be specified to avoid overflow
			throw new InvalidArgumentException("Invalid maximum length for input value : $inputMaxLength");
		}
		$length = $inputMaxLength + 1;
		if(empty($validAnswers)){
			self::write($question);
			return fgets(STDIN, $length);
		}
		$question .= ' ['.implode('/', $validAnswers).'] : ';
		if($maxAttempt > 0){
			$attempt = 0;
			$maxAttempt = (int) $maxAttempt;
			while(++$attempt <= $maxAttempt){
				self::write($question);
				$input = fgets(STDIN, $length);
				if(in_array($input, $validAnswers)){
					return $input;
				}
				self::writeLn('Could not understand given answer!');
			}
			self::writeLn('No valid answer was given after '.($attempt-1).' attempt(s)');
			return null;
		}else{
			while(true){
				self::write($question);
				$input = fgets(STDIN, $length);
				if(in_array($input, $validAnswers)){
					return $input;
				}
				self::writeLn('Could not understand given answer!');
			}
		}
	}

	/**
	 * Unlink a file / dir from file system
	 * If the path is directory it will be deleted recursively
	 * @param string $path The file / directory path
	 */
	public static function unlink($path)	{
		if(!file_exists($path))
			throw "File $path does not exists";
		if(!is_dir($path)){
			$deleted = unlink($path);
			if(!$deleted){
				throw new Exception("Failed removing $path");
			}
		}
		$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
		for ($dir->rewind(); $dir->valid(); $dir->next()) {
			if ($dir->isDir()) {
				$deleted = rmdir($dir->getPathname());
			} else {
				$deleted = unlink($dir->getPathname());
			}
			if(!$deleted){
				throw new Exception('Failed removing '.$dir->getPathname());
			}
		}
		$deleted = rmdir($path);
		if(!$deleted){
			throw new Exception("Failed removing $path");
		}
	}
}