<?php 

class paypal_transaction extends genericTable{
	const DB_TABLE_NAME = 'biobounce_paypal_transactions'; 
	const DB_UNIQUE_ID = 'bp_id'; 
	
	public function __construct(){
		parent::__construct(paypal_transaction::DB_TABLE_NAME, paypal_transaction::DB_UNIQUE_ID);
	}
	
	static public function getPaypalEmail($uid){
		$paypalFind = new paypal_transaction();
		$paypalFind->set_variable('bp_biobounce_uid', $uid);
		if (!$paypalFind->load()) return "";
		return $paypalFind->get_variable("bp_paypal_email");
	}
}