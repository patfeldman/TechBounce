<?php 
class user extends genericTable{
	const DB_TABLE_NAME = 'biobounce_users'; 
	const DB_UNIQUE_ID = 'users_id'; 
	const TRIAL_PERIOD = 14;
	const EXP_TYPE_TRIAL = 0;
	const EXP_TYPE_MANUAL = 1;
	const EXP_TYPE_PAID = 2;
	
	private static $Whitelist = array ("320", "344" );
	public function __construct(){
		parent::__construct(user::DB_TABLE_NAME, user::DB_UNIQUE_ID);
	}
	
	
	
	static public function expireDuplicateUsers(){
		$users = new user();
		$dup_emails = array();
		while ($users->loadNext("users_dupid != '0'")){
			$id = $users->get_variable('users_id');
			// don't check the whitelist
			//if (in_array($id, extends::$Whitelist)) continue;
			
			$expirationdateinfo = user::getUserExpirationDate($id);
			if (!$expirationdateinfo['isExpired']){
				if ($expirationdateinfo['type'] == user::EXP_TYPE_TRIAL){
					$dup_emails[] = $users->get_variable('users_email');
					$users->set_variable('users_creationdate', '2013-12-01');
					$users->update();
					echo "\n\nExpiring USER and sending email: " . $users->get_variable('users_username') . " ID:" . $id;
				}
			}
		}

		$email = new email(email::ADDRESSES_PROVIDED);
		$email->sendDuplicateAccountExpired($dup_emails);		
	}
	
	
	static public function isDupIp($uid, $ipAddr){
		global $mysqli;
		if (!empty($ipAddr)){
			$sql = "SELECT users_id from `biobounce_users` WHERE users_ipaddress='". $ipAddr ."' AND users_id <> '" . $uid . "'" ;
			$sql_query=$mysqli->query($sql);
			if ($row = $sql_query->fetch_array(MYSQLI_NUM) ) {
				return $row[0];
			}
			$sql_query->close();
		}
		return -1;
	}
	
	static public function randomPassword() {
	    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}

	static public function getAllTrialUserEmailAddresses(){
		global $mysqli;
		$sql = "SELECT u.users_id, u.users_email
		FROM `biobounce_users` u";
		$sql_query=$mysqli->query($sql);
		$all_return = array();
		while ($row = $sql_query->fetch_assoc()) {
			$id = $row['users_id'];
			$expirationdateinfo = user::getUserExpirationDate($id);
			if (!$expirationdateinfo['isExpired']  && $expirationdateinfo['type'] == user::EXP_TYPE_TRIAL){
				$all_return [] = $row['users_email'];
			}
		}
		$sql_query->close();
		return $all_return;
	}

	static public function DEBUG_setAllTrialUsersToGetEmailUpdates(){
		$user = new user();
		while ($user->loadNext()){
			$id = $user->get_variable("users_id");
			echo "</br>TESTING:" . $user->get_variable("users_username");
			
			$expirationdateinfo = user::getUserExpirationDate($id);
			if (!$expirationdateinfo['isExpired']  && $expirationdateinfo['type'] == user::EXP_TYPE_TRIAL){
				$user->set_variable("users_send_email_updates", 1);
				$user->update();
				echo "</br>   ADDING:" . $user->get_variable("users_username");
			}
		}
	}

	static public function resetAllExpiredEmailUpdates(){
		$user = new user();
		$user->set_variable("users_send_email_updates", 1);
		$user->set_variable("users_send_text_updates", 1);
		$user->set_variable("users_send_short_email_updates", 1);
		$user->set_variable("users_send_short_text_updates", 1);
		while ($user->loadNextOr()){
			$id = $user->get_variable("users_id");
			$expirationdateinfo = user::getUserExpirationDate($id);
			if ($expirationdateinfo['isExpired']){
				$user->set_variable("users_send_email_updates", 0);
				$user->set_variable("users_send_text_updates", 0);
				$user->set_variable("users_send_short_email_updates", 0);
				$user->set_variable("users_send_short_text_updates", 0);
				$user->set_variable("users_send_reversal_email_updates", 0);
				$user->set_variable("users_send_reversal_text_updates", 0);
				echo "</br>   REMOVING USER EMAIL UPDATES:" . $user->get_variable("users_username");
				$user->update();
			}
		}
	}

	static public function getAllEmailAddressSetForUpdates($tradeType, $hId){
		$user = new user();
		if ($tradeType==SHORT_TRADE || $tradeType == BREAKDOWN_TRADE || $tradeType == BACKDRAFT_TRADE){
			$user->set_variable("users_send_short_email_updates", 1);
		} else if ($tradeType==LONG_TRADE|| $tradeType == BREAKOUT_TRADE || $tradeType==PULLBACK_TRADE){
			$user->set_variable("users_send_email_updates", 1);
		} else if ($tradeType==REVERSAL_TRADE){
			$user->set_variable("users_send_reversal_email_updates", 1);
		}
		
		$all_return = array();
		while ($user->loadNext()){
			if ($hId > 0){
				$uId = $user->get_variable("users_id");
				$pholdings = new personal_holdings();
				$pholdings->set_variable("personal_holdings_userid", $uId);
				$pholdings->set_variable("personal_holdings_holdingsid", $hId);
				if (!$pholdings->load()){
					$all_return [] = $user->get_variable('users_email');
				}
			} else {
				$all_return [] = $user->get_variable('users_email');
			}
		}
		return $all_return;
	}

	static public function getAllTextAddressSetForUpdates($tradeType, $hId){
		$user = new user();
		if ($tradeType==SHORT_TRADE || $tradeType == BREAKDOWN_TRADE || $tradeType == BACKDRAFT_TRADE){
			$user->set_variable("users_send_short_text_updates", 1);
		} else if ($tradeType==LONG_TRADE|| $tradeType == BREAKOUT_TRADE || $tradeType==PULLBACK_TRADE){
			$user->set_variable("users_send_text_updates", 1);
		} else if ($tradeType==REVERSAL_TRADE){
			$user->set_variable("users_send_reversal_text_updates", 1);
		}
		
		$all_return = array();
		while ($user->loadNext()){
			$uId = $user->get_variable("users_id");
			if ($hId > 0){
				$pholdings = new personal_holdings();
				$pholdings->set_variable("personal_holdings_userid", $uId);
				$pholdings->set_variable("personal_holdings_holdingsid", $hId);
				//echo "</br>" . $pholdings->debug();
				if (!$pholdings->load()){
					$all_return [] = $user->get_variable('users_text_email_address');
				}
			} else if ($hId == -10000) {
				if ($uId == 8 || $uId == 9 || $uId == 10){
					echo "\nRUNNING TEST ";
					$all_return [] = $user->get_variable('users_text_email_address');
				}
			} else {
				$all_return [] = $user->get_variable('users_text_email_address');
			}
		}
		return $all_return;
	}


	static public function getAllActiveUserEmailAddresses(){
		global $mysqli;
		$sql = "SELECT u.users_id, u.users_email
		FROM `biobounce_users` u";
		$sql_query=$mysqli->query($sql);
		$all_return = array();
		while ($row = $sql_query->fetch_assoc()) {
			$id = $row['users_id'];
			$expirationdateinfo = user::getUserExpirationDate($id);
			if (!$expirationdateinfo['isExpired']){
				$all_return [] = $row['users_email'];
			}
		}
		$sql_query->close();
		return $all_return;
	}

	static public function getExpiringUsersEmailAddresses($numDays=2){
		global $mysqli;
		$sql = "SELECT u.users_id, u.users_email
		FROM `biobounce_users` u";
		$sql_query=$mysqli->query($sql);
		$all_return = array();
		$now = new DateTime(date("Y-m-d"));
		
		while ($row = $sql_query->fetch_assoc() ) {
			$id = $row['users_id'];
			$expirationdateinfo = user::getUserExpirationDate($id);
			if ($expirationdateinfo['type'] == user::EXP_TYPE_TRIAL){
				$expdate = new DateTime($expirationdateinfo['date']);
				$expiresin = intval($now->diff($expdate)->format("%r%a"));
				if ($expiresin == $numDays){
					$all_return[] = $row['users_email'];
				}
			}
		}
		$sql_query->close();
		return $all_return;
	}
	
	
	static public function getAllTrialUserInfo(){
		global $mysqli;
		$sql = "SELECT 
			u.users_id, u.users_username, u.users_email, u.users_verified, u.users_creationdate, u.users_lastlogindate, u.users_manualexpdate, u.users_ipaddress
		FROM `biobounce_users` u";
		$sql_query=$mysqli->query($sql);
		$all_return = array();
		while ($row = $sql_query->fetch_assoc()) {
			$id = $row['users_id'];
			$expirationdateinfo = user::getUserExpirationDate($id);
			if ($expirationdateinfo['type'] == user::EXP_TYPE_TRIAL){
				$row['expiration'] = $expirationDate;
				$all_return [] = $row;
			}
		}
		$sql_query->close();
		return $all_return;
	}

	static public function getAllUserInfo(){
		global $mysqli;
		$sql = "SELECT 
			u.users_id, u.users_username, u.users_twitterhandle, u.users_email, 
			u.users_verified, u.users_creationdate, u.users_lastlogindate, u.users_manualexpdate, u.users_ipaddress, u.users_dupid,
			u.users_send_email_updates, u.users_send_short_email_updates,u.users_send_text_updates, u.users_send_short_text_updates, 
			u.users_send_reversal_email_updates, u.users_send_reversal_text_updates
		FROM `biobounce_users` u";
		$sql_query=$mysqli->query($sql);
		$all_return = array();
		$now = new DateTime(date("Y-m-d"));
		while ($row = $sql_query->fetch_assoc()) {
			$id = $row['users_id'];
			$expirationdateinfo = user::getUserExpirationDate($id);
			$row['expdate'] = new DateTime($expirationdateinfo['date']);
			switch ($expirationdateinfo['type']){
				case user::EXP_TYPE_TRIAL:
					$exptype = "TRIAL";
					break;
				case user::EXP_TYPE_MANUAL:
					$exptype = "MANUAL";
					break;
				case user::EXP_TYPE_PAID:
					$exptype = "PAID";
					break;
			}
			
			$row['exptype'] = $exptype;
			$expiresin = intval($now->diff($row['expdate'])->format("%r%a"));
			if ($expiresin >= 0){
				$all_return['valid'][$expirationdateinfo['type']][] = $row;
			} else {
				$all_return['expired'][$expirationdateinfo['type']][] = $row;
			}
		}
		$sql_query->close();
		return $all_return;
	}
		

	static public function getUserExpirationDate($uid){
		$paymentDates = payment_info::getPaymentDates($uid);
		$paymentDate = date('0-0-00');
		$expiration = array();
		
		if (!empty($paymentDates)){
			$paymentDate = $paymentDates['next']->format("Y-m-d");
		}

		$trialDate = date('0-0-00');
		$manualExpDate = date('0-0-00');
		$user = new user();
		$user->set_variable('users_id', $uid);
		$isCreated = false;
		if ($user->load()){
			$created = new DateTime($user->get_variable('users_creationdate'));
			$created->add(new DateInterval('P'.user::TRIAL_PERIOD.'D'));
			$trialDate = $created->format('F j, Y');
			if (intval($created->format('Y')) < 2013)
				$isCreated = false;
			else 
				$isCreated = true; 
			$manualExpDate = $user->get_variable('users_manualexpdate');
		}
		
		
		$datetime = max(strtotime($paymentDate), strtotime($trialDate), strtotime($manualExpDate));
		
		if ($isCreated)
			$expiration['date'] = date("F j, Y", $datetime);
		else 
			$expiration['date'] = date("F j, Y", 0);

		$expiration['type'] = user::EXP_TYPE_TRIAL;
		if ($datetime == strtotime($paymentDate)){
			$expiration['type'] = user::EXP_TYPE_PAID;
		} else if ($datetime == strtotime($manualExpDate)){
			$expiration['type'] = user::EXP_TYPE_MANUAL;
		}
		
		$expirationdate = strtotime($expiration['date']);
		$now = strtotime(date("Y-m-d"));
		$expiresin = $expirationdate - $now;
		if ($expiresin >= 0){
			$expiration['isExpired'] = false;
		} else {
			$expiration['isExpired'] = true;
		}
		return $expiration;
	}

	static public function getAllUserInfoTEST(){
		global $mysqli;
		$sql = "SELECT 
			u.users_id, u.users_username, u.users_email, u.users_verified, u.users_creationdate, u.users_lastlogindate, u.users_manualexpdate
		FROM `biobounce_users` u";
		$sql_query=$mysqli->query($sql);
		$all_return = array();
		$now = new DateTime(date("Y-m-d"));
		while ($row = $sql_query->fetch_assoc()) {
			$id = $row['users_id'];
			$expirationdateinfo = user::getUserExpirationDateTEST($id);
			$row['expdate'] = new DateTime($expirationdateinfo['date']);
			switch ($expirationdateinfo['type']){
				case user::EXP_TYPE_TRIAL:
					$exptype = "TRIAL";
					break;
				case user::EXP_TYPE_MANUAL:
					$exptype = "MANUAL";
					break;
				case user::EXP_TYPE_PAID:
					$exptype = "PAID";
					break;
			}
			
			$row['exptype'] = $exptype;
			$expiresin = intval($now->diff($row['expdate'])->format("%r%a"));
			if ($expiresin >= 0){
				$all_return['valid'][$expirationdateinfo['type']][] = $row;
			} else {
				$all_return['expired'][$expirationdateinfo['type']][] = $row;
			}
		}
		$sql_query->close();
		return $all_return;
	}
		

	static public function getUserExpirationDateTEST($uid){
		$paymentDates = payment_info::getPaymentDates($uid);
		$paymentDate = date('0-0-00');
		$expiration = array();
		
		if (!empty($paymentDates)){
			$paymentDate = $paymentDates['next']->format("Y-m-d");
		}

		$trialDate = date('0-0-00');
		$manualExpDate = date('0-0-00');
		$user = new user();
		$user->set_variable('users_id', $uid);
		if ($user->load()){
			$created = new DateTime($user->get_variable('users_creationdate'));
			$created->add(new DateInterval('P'.user::TRIAL_PERIOD.'D'));
			$trialDate = $created->format('F j, Y');
			
			$manualExpDate = $user->get_variable('users_manualexpdate');
		}
		
		
		if (!empty($paymentDates)){
			$datetime = strtotime($paymentDate);
		
			$expiration['date'] = date("F j, Y", $datetime);
			$expiration['type'] = user::EXP_TYPE_PAID;
		}
		return $expiration;
	}
					
	static public function convertIDToCarrierAddress($carrierId){
		switch($carrierId){
			case "0":
				return "@vtext.com";
			case "1":
				return "@txt.att.net";
			case "2":
				return "@messaging.sprintpcs.com";
			case "3":
				return "@tmomail.net";
		}
		return "";
	}

	static public function convertCarrierAddressToID($carrierAddress){
		switch($carrierAddress){
			case "@vtext.com":
				return 0;
			case "@txt.att.net":
				return 1;
			case "@messaging.sprintpcs.com":
				return 2;
			case "@tmomail.net":
				return 3;
		}
		return "";
	}


	static public function createUserReferralCodes(){
		$user = new user();
		while ($user->loadNext()){
			$referralId = $user->get_variable("users_referralid");
			$userName = $user->get_variable("users_username");
			echo $userName . "  :" . $referralId; 
			if (!isset($referralId) || strlen($referralId) == 0){
				$user->set_variable("users_referralid", md5($userName));
				$user->update();
			}
		}
	}


					

}