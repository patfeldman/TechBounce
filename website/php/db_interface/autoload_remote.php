<?php
//	define(LOCATION, '/home/openm6/public_html/biobounce/');
//	define(BASE_LOCATION, '/home/openm6/cron/');
//	define(IS_TESTING, true);
	const LOCATION = '/home/openm6/public_html/biobounce/';
	const BASE_LOCATION = '/home/openm6/cron/';
	const IS_TESTING = true;
	const USE_REFERRALS = true;

	$host = "localhost";
	$databaseName = "openm6_biobounce";
	$user = "openm6_mini";
	$password = "minigolfrocks" ; //"minigolf";
		
	include_once(BASE_LOCATION . 'project_setup.php');