<?php
class backdraftAdminWatch extends adminControlWatchlist{
	public function __construct($watchlistId=-1){
		parent::__construct($watchlistId);
	}
	
	// Set all of the values that will be entered into the database
	protected function CalculateTargets($low_or_range, $high_or_entry){
		$diff = $high_or_entry - $low_or_range;
		$this->topPrice = round($high_or_entry - ($diff *.382), 2);
		$this->bottomPrice = round($high_or_entry - ($diff *.5), 2);
		$this->t1 = round($this->bottomPrice - ($diff *.25), 2);
		$this->t2 = round($this->bottomPrice - ($diff *.5), 2);
		$this->t3 = round($high_or_entry - ($diff *1.236), 2);	
	}
	
	protected function GetTradeType(){
		return BACKDRAFT_TRADE;
	}
}
