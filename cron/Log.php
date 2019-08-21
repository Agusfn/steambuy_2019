<?php

class Log
{

	public function __construct() {
	}

	public static function info($message)
	{
		self::log("INFO", $message);
	}

	public static function notice($message)
	{
		self::log("NOTICE", $message);
	}


	private static function log($type, $text)
	{
		$line = "[".date("d/m/Y H:i:s")."] ".$type.": ".$text."\r\n";
		file_put_contents("logs/log.txt", $line, FILE_APPEND);
	}


}