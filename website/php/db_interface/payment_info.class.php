<?php 
class payment_info{
    static function getPaymentDates($uid) {
    	global $mysqli;
		
		$sql = "SELECT * FROM `biobounce_paypal_transactions` WHERE bp_biobounce_uid='".$uid."' AND bp_paypal_txn_type='subscr_payment' AND bp_payment_status='Completed' ORDER BY bp_payment_date DESC LIMIT 1";
		$sql_query=$mysqli->query($sql);
		$dates = array();
		while ($row = $sql_query->fetch_assoc()) {
			$paydate = new DateTime($row['bp_payment_date']);
			$nextdate = new DateTime($row['bp_payment_date']);
			$price = intval($row['bp_paypal_mc_gross']);
			if ($price == 360 || $price == 205){
				$nextdate = $nextdate->modify("+1 year");
			} else {
				$nextdate = $nextdate->modify("+1 month");
			}
			$dates['next'] = $nextdate;
			$dates['last'] = $paydate;
		}
		$sql_query->close();
		return $dates;
    }

    static function getPaymentDatesTEST($uid) {
    	global $mysqli;
		
		$sql = "SELECT * FROM `biobounce_paypal_transactions` WHERE bp_biobounce_uid='".$uid."' AND bp_paypal_txn_type='subscr_payment' AND bp_payment_status='Completed' ORDER BY bp_payment_date DESC LIMIT 1";
		$sql_query=$mysqli->query($sql);
		$dates = array();
		while ($row = $sql_query->fetch_assoc()) {
			$paydate = new DateTime($row['bp_payment_date']);
			$nextdate = new DateTime($row['bp_payment_date']);
			echo "\nPRICE:" . $price = intval($row['bp_paypal_mc_gross']);
			if ($price == 360 || $price == 205){
				$nextdate = $nextdate->modify("+1 year");
			} else {
				$nextdate = $nextdate->modify("+1 month");
			}
			$dates['next'] = $nextdate;
			$dates['last'] = $paydate;
		}
		
		echo "::UID=" . $uid;
		print_r($dates);
		$sql_query->close();
		return $dates;
    }
	
	
	static function reconcileAllPaymentUids()
	{
		$txn = new paypal_transaction();
		$extraWhere = "bp_biobounce_uid ='0'";
		while ($txn->loadNext($extraWhere)){
			$found = false;
			$email = $txn->get_variable('bp_paypal_email');
			$paypalid = $txn->get_variable('bp_paypal_payer_id');
			
			$txnFind = new paypal_transaction();
			$extraWhere2 = "bp_biobounce_uid<>'0' AND bp_paypal_payer_id='" . $paypalid . "'";
			if ($txnFind->load($extraWhere2)){
				$bioId = $txnFind->get_variable('bp_biobounce_uid');
				$found = true;
				
				//echo "\n\nFOUND THE USER ID BASED ON PREVIOUSLY BEING SET:PAYPALID=" . $paypalid;
			} else {
				$usr = new user();
				$usr->set_variable('users_email', $email);
				if ($usr->load()){
					$bioId = $usr->get_variable('users_id');
					$found = true;
					//echo "\nFOUND THE USER ID BASED ON SAME EMAIL ADDRESS:ADDRESS=" . $email;
				}
			}

			if ($found){
				$txnId = $txn->get_variable('bp_id');
				//echo "\nUPDATING TRANSACTION NUMBER=" . $txnId . " to use UID=" . $bioId;
				$txn->set_variable('bp_biobounce_uid', $bioId);
				$txn->update();
			}
		}
	}
}