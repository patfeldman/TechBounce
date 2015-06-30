<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	session_start();
	
	if (!isset($_SESSION['userid'])){
		header('Location: /');
	}
	$username = "UserName";
	$user = new user();
	$uid = $_SESSION['userid'];
	$user->set_variable('users_id', $uid);
	
	$emailSubscribe = $_POST['esub'] === 'true'? true: false;
	$shortEmailSubscribe = $_POST['essub'] === 'true'? true: false;
	$reversalEmailSubscribe = $_POST['ersub'] === 'true'? true: false;

	$textSubscribe = isset($_POST['tsub']) && $_POST['tsub'] === 'true'? true: false;
	$shortTextSubscribe = isset($_POST['tssub']) && $_POST['tssub'] === 'true'? true: false;
	$reversalTextSubscribe = isset($_POST['trsub']) && $_POST['trsub'] === 'true'? true: false;

	if ($user->load()){
		$user->set_variable("users_send_email_updates", $emailSubscribe);
		$user->set_variable("users_send_short_email_updates", $shortEmailSubscribe);
		$user->set_variable("users_send_reversal_email_updates", $reversalEmailSubscribe);

		if (($textSubscribe || $shortTextSubscribe || $reversalTextSubscribe ) && isset($_POST['tnum'])){
			$number = $_POST["tnum"];
			$number = str_replace("(", "", $number);
			$number = str_replace(")", "", $number);
			$number = str_replace("-", "", $number);
			$number = str_replace(" ", "", $number);
			if (strlen($number) == 10 && is_numeric($number)){
				$textEmail = $number . user::convertIDToCarrierAddress($_POST['tprovider']);
				if (strlen($textEmail) > 11){
					$user->set_variable("users_send_text_updates", $textSubscribe);
					$user->set_variable("users_send_short_text_updates", $shortTextSubscribe);
					$user->set_variable("users_send_reversal_text_updates", $reversalTextSubscribe);
					$user->set_variable("users_text_email_address", $textEmail);
				} else {
					$user->set_variable("users_send_text_updates", false);
					$user->set_variable("users_send_short_text_updates", false);
					$user->set_variable("users_send_reversal_text_updates", false);
				}
			} else {
				$user->set_variable("users_send_text_updates", false);
				$user->set_variable("users_send_short_text_updates", false);
				$user->set_variable("users_send_reversal_text_updates", false);
			}
		} else {
			$user->set_variable("users_send_text_updates", false);
			$user->set_variable("users_send_short_text_updates", false);
			$user->set_variable("users_send_reversal_text_updates", false);
		}		
	}
	$user->update();
	echo "SUCCESS";
