<?php
	require_once('api/whatsprot.class.php'); // Load the WhatsApi class

	class OnlineChecker
	{
		private static $Whatsapp = null;
		
		private static $BotName = null;
		
		private static $URL = null;
		private static $Numbers = null;
		private static $OKPrint = null;
		
		public function __construct($WP_U, $WP_P, $WP_I, $WP_N, $U, $N, $OK_P, $B_N = 'UnderBot')
		{
			try
			{
				$w = new WhatsProt($WP_U, $WP_I, $WP_N);
				$w->connect();
				
				$w->loginWithPassword($WP_P);
				
				OnlineChecker::$BotName = strtolower($B_N);
				
				OnlineChecker::$URL = $U;
				OnlineChecker::$Numbers = $N;
				OnlineChecker::$OKPrint = $OK_P;
			}
			catch (Exception $E)
			{
				// Possible error_log();
			}
		}
		
		public function Check()
		{
			try
			{
				$Enabled = $this->GetStatus('enabled.dat'); // Get the state of enabled.dat (true/false) (Modified in control panel)
				
				if($Enabled == 'true') // If the OC (OnlineChecker) is enabled ==>
				{
					$Headers = get_headers(OnlineChecker::$URL, 1); // Get $URL headers
					$Sended = $this->GetStatus('issended.dat'); // Get state of notification
					
					if($Headers[0] != 'HTTP/1.0 200 OK' && $Headers[0] != 'HTTP/1.1 200 OK') // If URL's http code isn't 200 (OK) ==>
					{
						if($Sended == 'false') // If notification state is false ==>
						{
							$M = "Underc0de Online Checker - by fermino - http://underc0de.org/profile/fermino\r\n\r\n"; // Generate message
							
							$M .= "Error in " . OnlineChecker::$URL . "\r\n\r\n"; // Generate message
							
							$M .= "Error code: " . $Headers[0] . "\r\n"; // Generate message
							$M .= "Date: " . date('d/m/Y H:i:s'); // Generate message
							
							foreach(OnlineChecker::$Numbers as $To) // Numbers to send message
							{
								OnlineChecker::$Whatsapp->sendMessage($To, $M); // Send message to number
							}
							
							echo $M; // Show the error (Cron Job's log)
							
							$this->ChangeStatus('issended.dat', 'true');
						}
					}
					else // If headers are 200 (OK)
					{
						if(OnlineChecker::$OKPrint) echo 'Everything is OK in ' . OnlineChecker::$URL; // Show 'Everything is OK' message (only if OC isn't running in cron [$OKPrint])
						
						if($Sended == 'true') // If notification state is true (This will send a 'Site is back online' notification)
						{
							$M = "Underc0de Online Checker - by fermino - http://underc0de.org/profile/fermino\r\n\r\n"; // Generate message
							
							$M .= OnlineChecker::$URL . " back online\r\n\r\n"; // Generate message
							$M .= "Date: " . date('d/m/Y H:i:s'); // Generate message
							
							foreach(OnlineChecker::$Numbers as $To) // Numbers to send message
							{
								OnlineChecker::$Whatsapp->sendMessage($To, $M); // Send message to number
							}
							
							$this->PutStatus('issended.dat', 'false');
						}
					}
				}
				else // If OC isn't enabled
				{
					if(OnlineChecker::$OKPrint) echo 'OnlineChecker is disabled. '; // Show that OC is disabled (only if OC isn't running in cron [$OKPrint])
				}
				return true;
			}
			catch (Exception $E)
			{
				return $E;
			}
		}
		
		private function PutStatus($File, $Status)
		{
			try
			{
				if(($File == 'issended.dat' || $File == 'enabled.dat') && ($Status == 'true' || $Status == 'false'))
				{
					$File = fopen($File, 'w'); // Open the notification state file
					fwrite($File, $Status); // Save state
					fclose($File); // Close file
					
					return true;
				}
				else
				{
					return false;
				}
			}
			catch (Exception $E)
			{
				return false;
			}
		}
		
		private function GetStatus($File)
		{
			try
			{
				if($File == 'issended.dat' || $File == 'enabled.dat')
				{
					$Status = file_get_contents($File);
					return $Status;
				}
				else
				{
					return false;
				}
			}
			catch (Exception $E)
			{
				return false;
			}
		}
	}
?>