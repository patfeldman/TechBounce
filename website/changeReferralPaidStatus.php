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
	
	$admin = new admins();
	$admin->set_variable('admin_user_id', $user->get_variable('users_id'));
	
	$isAdmin = false;
	if ($admin->load()){
		$isAdmin = true;
	} else {
		header('Location: /');	
	}
	
	$referral = new referral();
	$referral->set_variable("referral_id", intval($_POST['rid']));
	if ($referral->load()){
		$isPaid = ($_POST['isPaid'] == "true") ? 1 : 0;
		$referral->set_variable("referral_paid", $isPaid);
		echo $referral->debug();
		$referral->update();
		echo "SUCCESS";
	} else {
		echo "FAILURE";
	}
	print_r($_POST);
	