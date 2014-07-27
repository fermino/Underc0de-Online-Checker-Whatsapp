<?php
	try
	{
		### START CONFIG ###
		$URL = 'http://github.com/ferminolaiz/url_that_not_exists';
		$Numbers = Array('');
		$OKPrint = false; // False if is running on cron job (This will avoid overload of cron log)
		
		$WP_Username = '';
		$WP_Password = '';
		$WP_Identity = '';
		$WP_Nickname = "Underc0de's bot";
		#### END CONFIG ####
		
		$Enabled = file_get_contents('enabled.dat');
		
		if($Enabled == 'true')
		{
			require_once('api/whatsprot.class.php');
			
			$Sended = file_get_contents('issended.dat');
			
			$Headers = get_headers($URL, 1);
			
			$w = new WhatsProt($WP_Username, $WP_Identity, $WP_Nickname);
			$w->connect();
				
			$w->loginWithPassword($WP_Password);
			
			if($Headers[0] != 'HTTP/1.0 200 OK' && $Headers[0] != 'HTTP/1.1 200 OK')
			{
				if($Sended == 'false')
				{
					$M = "Underc0de Online Checker - by fermino - http://underc0de.org/profile/fermino\r\n\r\n";
					
					$M .= "Error in " . $URL . "\r\n\r\n";
					
					$M .= "Error code: " . $Headers[0] . "\r\n";
					$M .= "Date: " . date('d/m/Y H:i:s');
					
					foreach($Numbers as $To)
					{
						$w->sendMessage($To, $M);
					}
					
					echo $M;
					
					$File = fopen('issended.dat', 'w');
					fwrite($File, 'true');
					fclose($File);
				}
			}
			else
			{
				if($OKPrint) echo 'Everything is OK in ' . $URL;
				if($Sended == 'true')
				{
					$M = "Underc0de Online Checker - by fermino - http://underc0de.org/profile/fermino\r\n\r\n";
					
					$M .= $URL . " back online\r\n\r\n";
					$M .= "Date: " . date('d/m/Y H:i:s');
					
					foreach($Numbers as $To)
					{
						$w->sendMessage($To, $M);
					}
					
					$File = fopen('issended.dat', 'w');
					fwrite($File, 'false');
					fclose($File);
				}
			}
		}
		else
		{
			if($OKPrint) echo 'OnlineChecker is disabled. ';
		}
	}
	catch (Exception $E)
	{
		var_dump($E);
	}
?>