<?php
	class Logger
	{
		private $Error = null;
		private $Exception = null;
		private $Log = null;

		public function __construct($ErrorFile = 'error_log', $Exception = 'exception_log', $LogFile = 'checker_log')
		{
			$this->Error = $ErrorFile;
			$this->Exception = $Exception;
			$this->Log = $LogFile;
		}

		public static function logError($Message)
		{
			try
			{
				$Date = date('Y-m-d h:i:s', mktime());
				
				$Message = "[{$Date}] {$Message}, at line {$Line}. {$File}";
				
				if(is_readable($this->Error) && is_writable($this->Error))
				{
					$File = fopen($this->Error, 'a');
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

		public function logException($Message, $Line, $File)
		{
			try
			{
				$Date = date('Y-m-d h:i:s', mktime());
				
				$Message = "[{$Date}] {$Message}, at line {$Line}. {$File}";
				
				if(is_readable($this->Exception) && is_writable($this->Exception))
				{
					$File = fopen($this->Exception, 'a');
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

		public function log($Message)
		{
			try
			{
				$Date = date('Y-m-d h:i:s', mktime());
				
				$Message = "[{$Date}] {$Message}";
				
				if(is_readable($this->Log) && is_writable($this->Log))
				{
					$File = fopen($this->Log, 'a');
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