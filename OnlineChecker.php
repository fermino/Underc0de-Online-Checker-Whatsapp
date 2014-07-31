<?php
	try
	{
		### START CONFIG ###
		$URL = 'http://github.com/ferminolaiz/url_that_not_exists'; // URL to check
		$Numbers = Array(''); // Whatsapp numbers to send notification
		$OKPrint = false; // False if is running on cron job (This will avoid overload of cron log)
		
		$WP_Username = ''; // Whatsapp number
		$WP_Password = ''; // Whatsapp password (Use WART to generate)
		$WP_Identity = ''; // Whatsapp identity (Use WART to generate)
		$WP_Nickname = "Underc0de's bot"; // Bot's nickname
		#### END CONFIG ####
		
		$Enabled = file_get_contents('enabled.dat'); // Get the state of enabled.dat (true/false) (Modified in control panel)
		
		if($Enabled == 'true') // If the OC (OnlineChecker) is enabled ==>
		{
			require_once('api/whatsprot.class.php'); // Load the WhatsApi class
			
			$Sended = file_get_contents('issended.dat'); // Get state of notification
			
			$Headers = get_headers($URL, 1); // Get URL headers
			
			$w = new WhatsProt($WP_Username, $WP_Identity, $WP_Nickname); // Load an instance of WhatsApi class
			$w->connect(); // Connect WhatsApi to whatsapp server
				
			$w->loginWithPassword($WP_Password); // Login in Whatsapp servers
			
			if($Headers[0] != 'HTTP/1.0 200 OK' && $Headers[0] != 'HTTP/1.1 200 OK') // If URL's http code isn't 200 (OK) ==>
			{
				if($Sended == 'false') // If notification state is false ==>
				{
					$M = "Underc0de Online Checker - by fermino - http://underc0de.org/profile/fermino\r\n\r\n"; // Generate message
					
					$M .= "Error in " . $URL . "\r\n\r\n"; // Generate message
					
					$M .= "Error code: " . $Headers[0] . "\r\n"; // Generate message
					$M .= "Date: " . date('d/m/Y H:i:s'); // Generate message
					
					foreach($Numbers as $To) // Numbers to send message
					{
						$w->sendMessage($To, $M); // Send message to number
					}
					
					echo $M; // Show the error (Cron Job's log)
					
					$File = fopen('issended.dat', 'w'); // Open the notification state file
					fwrite($File, 'true'); // Save state
					fclose($File); // Close the fle
				}
			}
			else // If headers are 200 (OK)
			{
				if($OKPrint) echo 'Everything is OK in ' . $URL; // Show 'Everything is OK' message (only if OC isn't running in cron [$OKPrint]) 
				if($Sended == 'true') // If notification state is true (This will send a 'Site is back online' notification)
				{
					$M = "Underc0de Online Checker - by fermino - http://underc0de.org/profile/fermino\r\n\r\n"; // Generate message
					
					$M .= $URL . " back online\r\n\r\n"; // Generate message
					$M .= "Date: " . date('d/m/Y H:i:s'); // Generate message
					
					foreach($Numbers as $To) // Numbers to send message
					{
						$w->sendMessage($To, $M); // Send message to number
					}
					
					$File = fopen('issended.dat', 'w'); // Open the notification state file
					fwrite($File, 'false'); // Save state
					fclose($File); // Close file
				}
			}
		}
		else // If OC isn't enabled
		{
			if($OKPrint) echo 'OnlineChecker is disabled. '; // Show that OC is disabled (only if OC isn't running in cron [$OKPrint])
		}
	}
	catch (Exception $E) // Catch the exception
	{
		var_dump($E); // Var dump the exception
	}
?>