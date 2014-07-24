<?php
	try
	{
		### START CONFIG ###
		$URL = 'http://github.com/ferminolaiz/url_that_not_exists'; // URL to check
		$To = ''; // Number to send the error report
		
		$WP_Username = '';
		$WP_Password = '';
		$WP_Identity = '';
		$WP_Nickname = "Underc0de's bot";
		#### END CONFIG ####
		
		require_once('api/whatsprot.class.php');
		
		$Headers = get_headers($URL, 1);
		
		$Data = Array();
		
		if($Headers[0] != 'HTTP/1.0 200 OK' || $Headers[0] != 'HTTP/1.1 200 OK')
		{
			$M = "WhatsBot Online Checker - by fermino - http://underc0de.org/fermino\r\n\r\n";
			
			$M .= "Error in " . $URL . "\r\n\r\n";
			
			$M .= "Error code: " . $Headers[0] . "\r\n";
			$M .= "Date: " . date('d/m/Y H:i:s') . "\r\n";
			
			$w = new WhatsProt($WP_Username, $WP_Identity, $WP_Nickname);
			$w->connect();
			
			$w->loginWithPassword($WP_Password);
			
			$w->sendMessage($To, $M);
		}
	}
	catch (Exception $E)
	{
		var_dump($E);
	}
?>