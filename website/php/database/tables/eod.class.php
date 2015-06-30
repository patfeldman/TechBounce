<?php 
class eod extends genericTable{
	const DB_TABLE_NAME = 'biobounce_last_eod'; 
	const DB_UNIQUE_ID = 'eod_id'; 
	
	public function __construct($id){
		parent::__construct(eod::DB_TABLE_NAME, eod::DB_UNIQUE_ID);
		
		$this->set_variable(eod::DB_UNIQUE_ID, $id);
		if (!$this->load()){
			// doesn't exist, create a new one with yesterdays date. 
			$yesterday= date('Y-m-d H:i:s', time() - 60 * 60 * 24);
			$this->set_variable('eod_date', $yesterday);
			$this->createNew();
		}
	}
	
}
