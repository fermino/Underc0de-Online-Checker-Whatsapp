<?php
	class Logger
	{
		public static $File = 'whatsapp_log';
		
		public static function logError($Message, $Line, $File)
		{
			try
			{
				$Date = date('Y-m-d h:i:s', mktime());
				$UserIP = $_SERVER['REMOTE_ADDR'];
				$Browser = $_SERVER["HTTP_USER_AGENT"];
				
				$Message = "[{$Date}] [{$UserIP}] {$Message}, at line {$Line}. {$File}. ({$Browser})";
				
				if(is_readable(self::$File) && is_writable(self::$File))
				{
					$File = fopen(self::$File, 'a');
					fwrite($File, $Message);
					fclose($File);

					return true;
				}

				return false;
			}
			catch (Exception $E)
			{
				trigger_error($E->getMessage, E_USER_WARNING);
				return false;
			}
		}
	}