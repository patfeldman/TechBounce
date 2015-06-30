<?php
	const AUTOLOAD_LOCATION = '/home/openm6/public_html/biobounce/db_interface/';
	//const AUTOLOAD_LOCATION = 'C:/xampp/htdocs/biobounce/db_interface/';

// stock market approximate start and end time
$PST_START = 06;
$PST_END = 17;

echo "\nRequiring\n";
include_once('autoload.php');
include_once('updater.class.php');

$holidays = array();
$holidays[] = strtotime("2015-04-03");
$holidays[] = strtotime("2014-04-18");
$holidays[] = strtotime("2014-05-26");
$holidays[] = strtotime("2014-07-04");
$holidays[] = strtotime("2015-01-01");
$today = strtotime(date("Y-m-d"));
if (!in_array($today, $holidays)){
	$day = date('N');
	if ($day > 5){
	   echo "SKIP! WEEKEND - " . $day; 
		
	} else {
	  $hour = date('G');
	  if ($hour < $PST_START || $hour >= $PST_END){
	     echo "SKIP! OFF HOURS - " . $hour;
	  } else {
		//Include the PS_Pagination class
		$updater = new updater();
		$updater->update();
		
		echo "\ncomplete";
	  }
	}
} else {
	echo "\n SKIP! HOLIDAY";
}


/*
$hour = intval(date('G'));
$minutes = intval(date('i'));
if ($hour == 6 && $minutes == 00){
		echo "\n SKIP! HOLIDAY";
			
	
		// SEND THE EXPIRING EMAIL TO THE expiring users
		$email = new email();
		$email->expirationEnding();
		
		// EXPIRE USERS BEFORE THE DAY STARTS
		user::expireDuplicateUsers();
}
*/
