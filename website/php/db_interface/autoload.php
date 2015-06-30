<?php
	if ($_SERVER['REMOTE_ADDR'] == '::1')
		include_once('autoload_local.php');
	else 
		include_once('autoload_remote.php');

	
date_default_timezone_set('America/New_York');