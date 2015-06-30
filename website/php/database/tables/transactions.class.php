<?php 
class transactions extends genericTable{
	const DB_TABLE_NAME = 'biobounce_transaction_history'; 
	const DB_UNIQUE_ID = 'transaction_id'; 
	
	public function __construct(){
		parent::__construct(transactions::DB_TABLE_NAME, transactions::DB_UNIQUE_ID);
	}
}