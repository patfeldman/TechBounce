<?php 
class referral extends genericTable{
	const DB_TABLE_NAME = 'biobounce_referrals'; 
	const DB_UNIQUE_ID = 'referral_id'; 
	
	public function __construct(){
		parent::__construct(referral::DB_TABLE_NAME, referral::DB_UNIQUE_ID);
	}
	
	public static function updateReferral($newUserId, $referralCode){
		$referredByUser = new user();
		$referredByUser->set_variable('users_referralid', $referralCode);
		echo "CHECKING REFERRAL";
		if ($referredByUser->load()){
			$rbUid = $referredByUser->get_variable("users_id");
			$referral = new referral();
			$referral->set_variable("referral_referred_by_userid", $rbUid);
			$referral->set_variable("referral_referred_userid", $newUserId);
			$referral->set_variable("referral_date", date('Y-m-d'));
			$referral->set_variable("referral_paid", 0);
			$referral->createNew();
		}
	}
}