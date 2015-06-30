<?php
class breakdownAdminWatch extends adminControlWatchlist{
	public function __construct($watchlistId=-1){
		parent::__construct($watchlistId);
	}
	
	// Set all of the values that will be entered into the database
	protected function CalculateTargets($entry, $range){
		$this->topPrice = round($entry + ($range*1.5), 2);
		$this->t0 = round($entry + ($range), 2);
		$this->t1 = round($entry - ($range), 2);
		$this->t2 = round($entry - ($range*1.5), 2);
		$this->t3 = round($entry - ($range*2), 2);
	}
	
	protected function GetTradeType(){
		return BREAKDOWN_TRADE;
	}
	
	
}
