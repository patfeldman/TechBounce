<?php
	include_once(BASE_LOCATION . 'project_db_setup.php');
	include_once(BASE_LOCATION . "constants.php");
	include_once(BASE_LOCATION . "library/twitteroauth/twitterinfo.php");
	include_once(BASE_LOCATION . "library/twitteroauth/twitteroauth.php");
	
	function __autoload($className)
	{
		$ar_class_directories = array(LOCATION . 'php/database/tables/', 
									  LOCATION . 'php/email_templates/', 
									  LOCATION . 'php/database/', 
									  LOCATION . 'php/classes/', 
									  LOCATION . 'php/classes/WatchLists/', 
									  LOCATION . 'php/db_interface/', 
									  LOCATION . 'php/', 
									  BASE_LOCATION . 'library/twitteroauth/', 
									  BASE_LOCATION . 'library/', 
                                      BASE_LOCATION 
                                      );
		foreach ($ar_class_directories as $directory)
		{
	        if(file_exists($directory.$className.'.class.php') ){
				require_once $directory.$className.'.class.php';
				return;
			}
		}
	}
	
