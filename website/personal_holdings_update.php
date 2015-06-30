<?php

	require_once('php/db_interface/autoload.php');
	session_start();

	if (isset($_SESSION['userid'])){
		$uid = $_SESSION['userid'];

		if (isset($_GET['hid'])){
			$holdingid = $_GET['hid'];
			$checked = $_GET['checked'];
			$ph = new personal_holdings();
			$ph->set_variable("personal_holdings_userid", $uid);
			$ph->set_variable("personal_holdings_holdingsid", $holdingid);
			if ($ph->load()){
				echo "Loaded";
				if ($checked=="true") {
					$ph->delete();
					echo "DELETED BECAUSE WATCHING";
				}
			} else if ($checked=="false"){
					$ph->createNew();
					echo "CREATED NOT WATCHED ID";
			}
		}
	} else {
		echo "FAILED";
	}

