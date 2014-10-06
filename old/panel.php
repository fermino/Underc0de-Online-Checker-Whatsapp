<?php
	
	$Path = 'enabled.dat'; // Relative path to OnlineChecker.php and enabled.dat (WITH 'enabled.dat')
	$Password = md5('Hi!'); // MD5 Hashed
	
	
	session_start();
	
	if(@$_SESSION['password'] == $Password)
	{
		$Status = file_get_contents($Path);
		
		if($Status != 'true' && $Status != 'false')
		{
			$File = fopen($Path, 'w');
			fwrite($File, 'true');
			fclose($File);
			$Status = 'true';
		}
		
		if(@$_GET['action'] == 'change')
		{
			$File = fopen($Path, 'w');
			
			if($Status == 'true')
			{
				fwrite($File, 'false');
			}
			else if($Status == 'false')
			{
				fwrite($File, 'true');
			}
			
			fclose($File);
			
			header('Location:?');
		}
		else if(@$_GET['action'] == 'logout')
		{
			session_destroy();
			header('Location:?');
		}
		else
		{
			?>
<!DOCTYPE html>
<html>
	<head>
		<title>Underc0de Online Checker (Whatsapp) - by fermino - http://underc0de.org/</title>
	</head>
	<body>
		<form action="?action=change" method="POST">
			<b>Status: <?php if($Status == 'true') { ?>Enabled<?php } else if($Status == 'false') { ?>Disabled<?php } ?>. </b>
			<br>
			<input type="submit" value="Change to <?php if($Status == 'true') { ?>disabled<?php } else if($Status == 'false') { ?>enabled<?php } ?>">
		</form>
		<form action="?action=logout" method="POST">
			<input type="submit" value="Logout">
		</form>
	</body>
</html>
			<?php
		}
	}
	else
	{
		if(@$_GET['action'] == 'login')
		{
			sleep(2); // Protect to bruteforce
			
			if(md5($_POST['password']) == $Password)
			{
				$_SESSION['password'] = md5($_POST['password']);
				header('Location:?');
			}
			else
			{
				header('Location:?');
			}
		}
		else
		{
			?>
<!DOCTYPE html>
<html>
	<head>
		<title>Underc0de Online Checker (Whatsapp) - by fermino - http://underc0de.org/</title>
	</head>
	<body>
		<form action="?action=login" method="POST">
			<b>Ingrese la contrase&ntilde;a: </b><input name="password" type="password">
			<br>
			<input type="submit" value="Login">
		</form>
	</body>
</html>
			<?php
		}
	}
?>