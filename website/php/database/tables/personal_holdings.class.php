<?php 
class personal_holdings extends genericTable{
	const DB_TABLE_NAME = 'biobounce_personal_holdings'; 
	const DB_UNIQUE_ID = 'personal_holdings_id'; 
	
	public function __construct(){
		parent::__construct(personal_holdings::DB_TABLE_NAME, personal_holdings::DB_UNIQUE_ID);
	}
	
	public static function iAmHolding($uid, $holdingid){
		$ph = new personal_holdings();
		$ph->set_variable("personal_holdings_userid", $uid);
		$ph->set_variable("personal_holdings_holdingsid", $holdingid);
		if ($ph->load()){
			return false;
		} else {
			return true;
		}
	}
}