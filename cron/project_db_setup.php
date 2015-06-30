<?php
date_default_timezone_set('America/New_York');

	//biobounce
	$mysqli = new mysqli($host, $user, $password, $databaseName);
	if($mysqli->connect_errno > 0){
   		die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}
