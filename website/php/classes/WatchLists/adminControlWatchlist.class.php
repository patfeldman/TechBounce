<?php 
class adminControlWatchlist{
	var $watchlistId;
	var $watchlistRecord;
	var $t0;
	var $t1;
	var $t2;
	var $t3;
	var $topPrice;
	var $bottomPrice;
	var $low;
	var $high;
	
	public function __construct($watchlistId=-1){
		$this->t0=$this->t1=$this->t2=$this->t3 = 0;
		$this->bottomPrice=$this->topPrice = 0;

		$this->watchlistId = $watchlistId;
		if ($watchlistId >= 0){
			$this->watchlistRecord= new watchlist();
			$this->watchlistRecord->set_variable('watchlist_id', $this->watchlistId);
			$this->watchlistRecord->load();
			$this->low = $this->watchlistRecord->get_variable('watchlist_low');
			$this->high = $this->watchlistRecord->get_variable('watchlist_high');
			$this->topPrice = $this->watchlistRecord->get_variable('watchlist_top');
			$this->bottomPrice = $this->watchlistRecord->get_variable('watchlist_bottom');
			$this->t0 = $this->watchlistRecord->get_variable('watchlist_target0');
			$this->t1 = $this->watchlistRecord->get_variable('watchlist_target1');
			$this->t2 = $this->watchlistRecord->get_variable('watchlist_target2');
			$this->t3 = $this->watchlistRecord->get_variable('watchlist_target3');
		}
	}
	
	protected function GetTradeType(){}
	public function AddNewTickerAndWatch($tickerName, $lowOrEntry, $highOrRange){
		
		$tt = $this->GetTradeType();
		// ADD THE TICKER FIRST 
		$ticker = new ticker();
		$ticker -> set_variable('ticker_symbol', $tickerName);
		if (!$ticker -> load()) {
			$tickerId = $ticker->createNew();
			$ticker->load();
			$ticker->updateFromYahoo();
		} else {
			$tickerId = $ticker->get_variable('ticker_id');
		}

		// ADD TO THE WATCHLIST NOW
		$watchlist = new watchlist();
		$watchlist->set_variable('watchlist_ticker_id', $tickerId);
		
		// call pure virtual to get fields based on trade type
		$this->CalculateTargets($lowOrEntry, $highOrRange);

		$watchlist->set_variable('watchlist_low', $lowOrEntry);
		$watchlist->set_variable('watchlist_high', $highOrRange);
		$watchlist->set_variable('watchlist_top', $this->topPrice);
		$watchlist->set_variable('watchlist_bottom', $this->bottomPrice);
		$watchlist->set_variable('watchlist_target0', $this->t0);
		$watchlist->set_variable('watchlist_target1', $this->t1);
		$watchlist->set_variable('watchlist_target2', $this->t2);
		$watchlist->set_variable('watchlist_target3', $this->t3);
		$watchlist->set_variable('watchlist_tradetype', $tt);
		
		$watchId = $watchlist->createNew();
		
		holdings::updateTickerSellPrices($tickerId, $this->bottomPrice, $tt, false);
		highlights::watchlistHighlight($watchId, W_ROW, 0, highlights::EVENT_START_DAY);
	}

	public function UpdateWatch($watchlistRecord, $lowOrEntry, $highOrRange){
		// call pure virtual to get fields based on trade type
		$this->CalculateTargets($lowOrEntry, $highOrRange);

		$watchlistRecord->set_variable('watchlist_low', $lowOrEntry);
		$watchlistRecord->set_variable('watchlist_high', $highOrRange);
		$watchlistRecord->set_variable('watchlist_top', $this->topPrice);
		$watchlistRecord->set_variable('watchlist_bottom', $this->bottomPrice);
		$watchlistRecord->set_variable('watchlist_target0', $this->t0);
		$watchlistRecord->set_variable('watchlist_target1', $this->t1);
		$watchlistRecord->set_variable('watchlist_target2', $this->t2);
		$watchlistRecord->set_variable('watchlist_target3', $this->t3);
		$watchlistRecord->update();
		
		$watchId = $watchlistRecord->get_variable('watchlist_id');
		highlights::watchlistHighlight($watchId, W_ENTRY_ZONE, 0, highlights::EVENT_START_DAY);
		
		
	}


	protected function CalculateTargets(){
		// PURE VIRTUAL - MUST BE IMPLEMENTED BY CHILD
	}

	public function Remove(){
		$this->watchlistRecord->set_variable('watchlist_is_deleted', 1);
		$this->watchlistRecord->update();
	}

	public function Zone(){
		//$this->watchlistRecord->markZoned($this->GetTradeType());
		// TEMPORARY FOR TESTING
		$this->watchlistRecord->markZoned($this->GetTradeType(), false, false);
	}

	public function Edit($low, $high, $top=0, $bottom=0, $t1=0, $t2=0, $t3=0, $tip=null){

		if ($low != $this->low || $high != $this->high){
			$this->UpdateWatch($this->watchlistRecord, $low, $high);
		} else {
			$this->watchlistRecord->set_variable('watchlist_top', $top);
			$this->watchlistRecord->set_variable('watchlist_bottom', $bottom);
			$this->watchlistRecord->set_variable('watchlist_target1', $t1);
			$this->watchlistRecord->set_variable('watchlist_target2', $t2);
			$this->watchlistRecord->set_variable('watchlist_target3', $t3);
		}
		if ($tip != null)
			$this->watchlistRecord->set_variable('watchlist_tooltip', $tip);
		$this->watchlistRecord->update();
	}

	public static function GetWatchClassName($tradeType){
		switch ($tradeType){
			case LONG_TRADE:
				return "pullbackAdminWatch";
			case PULLBACK_TRADE:
				return "pullback2AdminWatch";
			case SHORT_TRADE:
				return "shortAdminWatch";
			case BACKDRAFT_TRADE:
				return "backdraftAdminWatch";
			case REVERSAL_TRADE:
				return "reversalAdminWatch";
			case BREAKOUT_TRADE:
				return "breakoutAdminWatch";
			case BREAKDOWN_TRADE:
				return "breakdownAdminWatch";
			default:
				return "adminControlWatchlist";
		}

	}
}