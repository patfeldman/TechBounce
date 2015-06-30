<?php 
class ticker extends genericTable{
	const DB_TABLE_NAME = 'biobounce_tickers'; 
	const DB_UNIQUE_ID = 'ticker_id'; 
	
	public function __construct(){
		parent::__construct(ticker::DB_TABLE_NAME, ticker::DB_UNIQUE_ID);
	}
	
	public function updateFromYahoo(){
		$date = date('Y-m-d H:i:s');
		
		$stocks = array();
		$symbol = strtoupper($this->get_variable('ticker_symbol'));
		$stocks[] = $symbol;
		$results = finance::retrieveCurrentPrice($stocks, "l1hgp2");
		foreach($results as $key=>$value){
			if (strtoupper($key)!=$symbol) continue;
			$stockUpdates = explode(",", $value);
			$this->set_variable('last', $stockUpdates[0]);
			$this->set_variable('today_high', $stockUpdates[1]);
			$this->set_variable('today_low', $stockUpdates[2]);
			$change = str_replace('"', "", $stockUpdates[3]);
			$change = str_replace('%', "", $change);
			$change = str_replace('+', "", $change);
			$this->set_variable('change_today_percent', $change);
			$this->set_variable('last_update', $date);
			$this->update();
		}
	}
}