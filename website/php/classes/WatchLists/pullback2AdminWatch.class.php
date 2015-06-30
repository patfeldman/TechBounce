<?php
class pullback2AdminWatch extends adminControlWatchlist{
	public function __construct($watchlistId=-1){
		parent::__construct($watchlistId);
	}
	
	// Set all of the values that will be entered into the database
	protected function CalculateTargets($low_or_range, $high_or_entry){
		$diff = $high_or_entry - $low_or_range;
		$this->topPrice = round($high_or_entry - ($diff *.5), 2);
		$this->bottomPrice = round($high_or_entry - ($diff *.618), 2);
		$this->t1 = round(($diff *.25) + $this->topPrice, 2);
		$this->t2 = round(($diff *.5) + $this->topPrice, 2);
		$this->t3 = round(($diff *.236) + $high_or_entry, 2);		
	}
	protected function GetTradeType(){
		return PULLBACK_TRADE;
	}
	
	
}
