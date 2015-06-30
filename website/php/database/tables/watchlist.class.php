<?php 
class watchlist extends genericTable{
	const DB_TABLE_NAME = 'biobounce_watchlist'; 
	const DB_UNIQUE_ID = 'watchlist_id'; 
	
	public function __construct(){
		parent::__construct(watchlist::DB_TABLE_NAME, watchlist::DB_UNIQUE_ID);
	}
	
	public function markZoned($tradeType=LONG_TRADE, $sendUpdate=true, $logTransaction=true){
		$notesStr = "";
		$zoned = $this->get_variable('watchlist_is_zoned');
		if ($zoned == 0){
			$tid = $this->get_variable('watchlist_ticker_id');
			$ticker = new ticker();
			$ticker->set_variable('ticker_id', $tid);
			if ($ticker->load()){
				$tickerSymbol = $ticker->get_variable('ticker_symbol');

				$last = $ticker->get_variable('last');

				$today_low = $ticker->get_variable('today_low');
				$today_high = $ticker->get_variable('today_high');

				$date = date('Y-m-d H:i:s');
				
				$bottom = $this->get_variable('watchlist_bottom');
				$top = $this->get_variable('watchlist_top');

				$today_low = max($bottom, min($last, $today_low));
				$today_high = min($top, max($last, $today_high));
				
				if ($today_low == 0 || $today_high == 0) return "ERROR IN DATA PROVIDED::HIGH=" . $today_high . "::LOW=".$today_low;
				
				$holdings = new holdings();
				$holdings->set_variable('holdings_ticker_id', $tid);
				$holdings->set_variable('holdings_orig_date', $date);
				
				$lowOrEntry = round(floatval ($this->get_variable('watchlist_low')), 2);
				$t0 = round(floatval ($this->get_variable('watchlist_target0')), 2);
				$t1 = round(floatval ($this->get_variable('watchlist_target1')), 2);
				$t2 = round(floatval ($this->get_variable('watchlist_target2')), 2);
				$t3 = round(floatval ($this->get_variable('watchlist_target3')), 2);
				
				$holdings->set_variable('holdings_t0', $t0);
				$holdings->set_variable('holdings_t1', $t1);
				$holdings->set_variable('holdings_t2', $t2);
				$holdings->set_variable('holdings_t3', $t3);
				
				$holdings->set_variable('holdings_tradetype', $tradeType);
				$holdings->set_variable('holdings_stop_type', 'EOD');
				$holdings->set_variable('holdings_last_action', BUY);

				switch ($tradeType){
					case SHORT_TRADE:
					case BACKDRAFT_TRADE:
						$actionPrice = $bottom;
						$holdings->set_variable('holdings_orig_price', $bottom);
						$holdings->set_variable('holdings_stop_price', $top);
						$holdings->set_variable('holdings_top_price', $bottom);
						$notesStr.= "\n<br/>\nIN ZONE ticker:". $tickerSymbol . " $" . $last . " (ORIG_PRICE = " . $today_high . ")";
	
						break;
					case LONG_TRADE:
					case PULLBACK_TRADE:
						$actionPrice = $top;
						$holdings->set_variable('holdings_orig_price', $top);
						$holdings->set_variable('holdings_stop_price', $bottom);
						$holdings->set_variable('holdings_top_price', $top);
						$notesStr.= "\n<br/>\nIN ZONE ticker:". $tickerSymbol . " $" . $last . " (ORIG_PRICE = " . $today_low . ")";
						break;
					case REVERSAL_TRADE:
						$actionPrice = $top;
						$holdings->set_variable('holdings_orig_price', $top);
						$holdings->set_variable('holdings_stop_price', $top);
						$holdings->set_variable('holdings_top_price', $top);
						$notesStr.= "\n<br/>\nIN ZONE ticker:". $tickerSymbol . " $" . $last . " (ORIG_PRICE = " . $today_low . ")";
						break;
					case BREAKOUT_TRADE:
						$actionPrice = $lowOrEntry;
						$holdings->set_variable('holdings_orig_price', $lowOrEntry);
						$holdings->set_variable('holdings_stop_price', $top);
						$holdings->set_variable('holdings_top_price', $top); // Hard Stop Price
						$notesStr.= "\n<br/>\nIN ZONE ticker:". $tickerSymbol . " $" . $last . " (ORIG_PRICE = " . $today_low . ")";
						break;
					case BREAKDOWN_TRADE:
						$actionPrice = $lowOrEntry;
						$holdings->set_variable('holdings_orig_price', $lowOrEntry);
						$holdings->set_variable('holdings_stop_price', $top);
						$holdings->set_variable('holdings_top_price', $top);
						$notesStr.= "\n<br/>\nIN ZONE ticker:". $tickerSymbol . " $" . $last . " (ORIG_PRICE = " . $today_low . ")";	
						break;	
				}
				$tweet = new tweet();
				$updateEmail = new email(email::ADDRESSES_ALL_CHOSEN, $tradeType);	
				$action = BUY;
				$tweet->newTweet($tradeType, $action, $tickerSymbol, $actionPrice);
				$updateEmail->newEmail($tradeType, $action, $tickerSymbol, $actionPrice);
				

				$tooltip = $this->get_variable("watchlist_tooltip");
				$holdings->set_variable("holdings_tooltip", $tooltip);		
				$holdingsId = $holdings->createNew();
				
				if ($logTransaction){
					// Add a transaction to the transaction table 
					$transactions = new transactions();
					$transactions->set_variable('transaction_holdings_id', $holdingsId);
					$transactions->set_variable('transaction_price', $actionPrice);
					$transactions->set_variable('transaction_date', date('Y-m-d H:i:s'));
					$transactions->set_variable('transaction_action', BUY);
					$transactions->set_variable('transaction_tradetype', $tradeType);
					$transactions->createNew();
				}
									
				// add to the holdings table with the current information
				$this->set_variable('watchlist_is_zoned', 1);
				$this->update();
			}
		}
		return $notesStr;
	}

	public static function removeTickerHitT1($tickerId){
		$watchlist = new watchlist();
		$watchlist->set_variable('watchlist_ticker_id', $tickerId);
		if ($watchlist->load()){
			$watchlist->delete();
		}
	}

	public static function pruneDeletedWatchlistIds(){
		$watchlist = new watchlist();
		$watchlist->set_variable('watchlist_is_deleted', 1);
		$watchlist->delete();
	}
	public static function deleteZonedTickers($tradeType){
		$watchlist = new watchlist();
		$watchlist->set_variable('watchlist_is_zoned', 1);
		$watchlist->set_variable('watchlist_tradetype', $tradeType);
		$watchlist->delete();
	}
}