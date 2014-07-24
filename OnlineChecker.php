<?php
	try
	{
		### START CONFIG ###
		$URL = 'http://github.com/ferminolaiz/url_that_not_exists';
		$Numbers = Array('');
		
		$WP_Username = '';
		$WP_Password = '';
		$WP_Identity = '';
		$WP_Nickname = "Underc0de's bot";
		#### END CONFIG ####
		
		require_once('api/whatsprot.class.php');
		
		$Sended = file_get_contents('issended.dat');
		
		$Headers = get_headers($URL, 1);
		
		if($Headers[0] != 'HTTP/1.0 200 OK' && $Headers[0] != 'HTTP/1.1 200 OK')
		{
			$M = "Underc0de Online Checker - by fermino - http://underc0de.org/fermino\r\n\r\n";
				
			$M .= "Error in " . $URL . "\r\n\r\n";
				
			$M .= "Error code: " . $Headers[0] . "\r\n";
			$M .= "Date: " . date('d/m/Y H:i:s') . "\r\n";
			
			if($Sended == 'false')
			{
				$w = new WhatsProt($WP_Username, $WP_Identity, $WP_Nickname);
				$w->connect();
				
				$w->loginWithPassword($WP_Password);
				
				foreach($Numbers as $To)
				{
					$w->sendMessage($To, $M);
				}
				
				$File = fopen('issended.dat', 'w');
				fwrite($File, 'true');
				fclose($File);
			}
		}
		else
		{
			if($Sended == 'true')
			{
				$File = fopen('issended.dat', 'w');
				fwrite($File, 'false');
				fclose($File);
			}
		}
	}
	catch (Exception $E)
	{
		var_dump($E);
	}
?>