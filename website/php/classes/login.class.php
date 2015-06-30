<?php
class login{
	var $isAdmin;
	var $username;
	var $userId;
	var $user;
	var $trialTimeClass = "";
	var $trialMessage= "**** YOUR TRIAL MEMBERSHIP HAS EXPIRED! ****";
	var $showData = false;
	var $showClass = "none";
	var $useSubscriptionText = false;
	var $nextPaymentDate;
	public function __construct($isAdminPage = false, $doExpirationCheck = true){
		$this->LoginCheck($isAdminPage);
		if ($doExpirationCheck){
			$this->CheckUserExpiration();
		}
	}

	function LoginCheck($isAdminPage){
		session_start();
		if (!isset($_SESSION['userid'])) {
			header('Location: /');
		}
		$username = "UserName";
		$user = new user();
		$user -> set_variable('users_id', $_SESSION['userid']);
		if ($user -> load()) {
			$username = $user -> get_variable('users_username');
			$userId = $user -> get_variable('users_id');
		}
		
		$admin = new admins();
		$admin -> set_variable('admin_user_id', $userId);
		
		$isAdmin = false;
		if ($admin -> load()) {
			$isAdmin = true;
		} else if ($isAdminPage) {
			header('Location: /');
		}
		
		if (isset($_GET['lo'])) {
			session_destroy();
			header('Location: /');
		}
		$this->isAdmin = $isAdmin;
		$this->username = $username;
		$this->userId = $userId;
		$this->user = $user;
	}
	
	function CheckUserExpiration(){
	
		$this->showClass = "";
		$this->trialTimeClass = "none";
		$this->showData = true;
		$paymentDates = payment_info::getPaymentDates($this->userId);
		$this->trialMessage= "";
		$lastPaymentDate = "";
		$nextPaymentDate = "";
		$useSubscriptionText = true;
		
		$expirationInfo = user::getUserExpirationDate($this->userId);
		$expDate = new DateTime($expirationInfo['date']);
		$now = new DateTime(date("Y-m-d"));
		$diff = intval($now->diff($expDate)->format("%r%a"));
		$nextPaymentDate = $expDate->format("F dS, Y");
		$isTrial = $expirationInfo['type'] == user::EXP_TYPE_TRIAL;
		if ($isTrial){
			$useTrialEnd = true;
			if ($diff < 0){
				$this->trialTimeClass = "";
				$this->trialMessage= "**** YOUR TRIAL MEMBERSHIP HAS EXPIRED! ****";
				$this->showData = false;
				$this->showClass = "none";
			} else if ($diff <= 15) {
				$this->trialTimeClass = "";
				if ($diff == 0)
					$this->trialMessage= "**** TRIAL MEMBERSHIP EXPIRES TODAY! ****";
				else
					$this->trialMessage= "**** TRIAL MEMBERSHIP EXPIRES IN ". $diff . " DAYS! ****";
			}		
		} else {
			if ($diff >= -3 ){
				$useSubscriptionText = false;
			} else {
				$this->trialTimeClass = "";
				$this->trialMessage= "**** YOUR SUBSCRIPTION HAS EXPIRED! ****";
				$this->showData = false;
				$this->showClass = "none";
			}
		}
		
		$this->useSubscriptionText = $useSubscriptionText;
		$this->nextPaymentDate = $nextPaymentDate;
	}
	
	// DEPRECATED - DELETE WHEN ALL USE THE CLASS STRUCTURE
	static function LoginCheckAndRedirect(&$isAdmin, &$username){
		session_start();
		if (!isset($_SESSION['userid'])) {
			header('Location: /');
		}
		$username = "UserName";
		$user = new user();
		$user -> set_variable('users_id', $_SESSION['userid']);
		if ($user -> load()) {
			$username = $user -> get_variable('users_username');
		}
		
		$admin = new admins();
		$admin -> set_variable('admin_user_id', $user -> get_variable('users_id'));
		
		$isAdmin = false;
		if ($admin -> load()) {
			$isAdmin = true;
		} else {
			header('Location: /');
		}
		
		if (isset($_GET['lo'])) {
			session_destroy();
			header('Location: /');
		}
		return $isAdmin;
	}
	
}