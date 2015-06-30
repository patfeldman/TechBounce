<?php 
class admins extends genericTable{
	const DB_TABLE_NAME = 'biobounce_admins'; 
	const DB_UNIQUE_ID = 'admin_id'; 
	
	public function __construct(){
		parent::__construct(admins::DB_TABLE_NAME, admins::DB_UNIQUE_ID);
	}
}