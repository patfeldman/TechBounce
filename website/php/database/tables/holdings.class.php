<?php 
class holdings extends genericTable{
	const DB_TABLE_NAME = 'biobounce_holdings'; 
	const DB_UNIQUE_ID = 'holdings_id'; 
	
	public function __construct(){
		parent::__construct(holdings::DB_TABLE_NAME, holdings::DB_UNIQUE_ID);
	}
	
	public function loadNextAll(){
		if ($this->sql_query  && isset($this->sql_query)){
			if ($row = $this->sql_query->fetch_assoc()) 
				$this->loadRowInformation($row);
			else
				return false;
		}else{
			$sql = "SELECT 
				t.ticker_id, t.last, t.ticker_symbol, t.today_low, t.today_high, t.last_high, t.last_low,
				h.holdings_id, h.holdings_t1, h.holdings_t2, h.holdings_t3, h.holdings_orig_price, h.holdings_orig_date,
				h.holdings_t1_marked, h.holdings_t2_marked, h.holdings_t3_marked, h.holdings_init_sell_price_set, h.holdings_abandon_marked,
				h.holdings_stop_price, h.holdings_stop_type, h.holdings_last_action , h.holdings_stoch_action, h.holdings_tradetype, h.holdings_top_price 
				FROM biobounce_holdings h, biobounce_tickers t 
				WHERE h.holdings_ticker_id = t.ticker_id and h.holdings_abandon_marked=0" ;
			$this->sql_query=$this->db->query($sql);
			if ($row = $this->sql_query->fetch_assoc() ) 
				$this->loadRowInformation($row);
			else 
				return false;
			return true;
		}
		return true;
	}

	public function markTarget($targetNum, $symbol, $last, $sendUpdate = false, $updatedSellPrice=-1){
		$isMarked = $this->get_variable('holdings_t' . $targetNum . '_marked');
		if ($isMarked) return;
		$targetVal = $this->get_variable('holdings_t' . $targetNum);
		$hId = $this->get_variable('holdings_id');
		$tId = $this->get_variable('holdings_ticker_id');
		if (!isset($tId)) $tId = $this->get_variable('ticker_id'); // fix if not set

		$tradeType = $this->get_variable('holdings_tradetype');
		$action = GetActionByTarget($targetNum);
		$highlight = GetHighlightByTarget($targetNum);
		if ($sendUpdate){
			$tweet = new tweet();
			$updateEmail = new email(email::ADDRESSES_ALL_CHOSEN, $tradeType, $hId);
			switch ($targetNum){
				case 1:
					$action = SELL1;
					break;
				case 2:
					$action = SELL2;
					break;
				case 3:
					$action = SELL3;
					break;
			}
			$tweet->newTweet($tradeType, $action, $symbol, $targetVal);
			$updateEmail->newEmail($tradeType, $action, $symbol, $targetVal);
			//$tweet->hitTarget($tradeType, $targetNum, $symbol, $last);
			//$updateEmail->hitTarget($tradeType, $targetNum, $symbol, $last);
		}

		// update sell price of all holdings with this ticker_id		
		if ($targetNum==1) watchlist::removeTickerHitT1($tId);
		// get default price if not specified
		$updatedSellPrice = $this->GetUpdatedSellPrice($tradeType, $targetNum);
		// don't run if default is -1
		echo "\n<br> Updating with sellPrice = " . $updatedSellPrice;
		if ($updatedSellPrice >= 0) holdings::updateTickerSellPrices($tId, $updatedSellPrice, $tradeType);
		
		$newHoldings = new holdings();
		$newHoldings->set_variable('holdings_id', $hId);
		$newHoldings->set_variable('holdings_last_action', $action);
		$newHoldings->set_variable('holdings_t'.$targetNum.'_marked', 1);
		$newHoldings->update();
		
		holdings::CreateNewTransaction($hId, $tradeType, $targetVal, $action);
		highlights::holdingsHighlight($hId, $highlight, 0, highlights::EVENT_START_DAY);
	}
	private function GetUpdatedSellPrice($tradeType, $targetNum){
		switch ($tradeType){
			case LONG_TRADE:
			case SHORT_TRADE:
			case REVERSAL_TRADE:
				switch ($targetNum){
					case 0:
					case 1:
						return -1;
					case 2: 
						return $this->get_variable('holdings_t1');
					case 3:
						return $this->get_variable('holdings_t3');
				}
				break;
			case PULLBACK_TRADE:
			case BACKDRAFT_TRADE:
				switch ($targetNum){
					case 1:
						return $this->get_variable('holdings_t1');
				}
				break;
			case BREAKOUT_TRADE:
			case BREAKDOWN_TRADE:
				switch ($targetNum){
					case 3:
						//return $this->get_variable('holdings_t2');
				}
				break;
		}
		
		return -1;
	}

	public function GetStartPrice(){
		$tradeType = $this->get_variable('holdings_tradetype');
		switch ($tt){
			case LONG_TRADE:
			case SHORT_TRADE:
			case PULLBACK_TRADE:
			case BACKDRAFT_TRADE:
			case REVERSAL_TRADE:
				return $holding['holdings_top_price'];
			case BREAKOUT_TRADE:
			case BREAKDOWN_TRADE:
				return $holding['holdings_orig_price'];
		}
		
		return 1;
	}

	static public function CreateNewTransaction($hId, $tradeType, $last, $action){
		// Add a transaction to the transaction table 
		$transactions = new transactions();
		$transactions->set_variable('transaction_holdings_id', $hId);
		$transactions->set_variable('transaction_price', $last);
		$transactions->set_variable('transaction_date', date('Y-m-d H:i:s'));
		$transactions->set_variable('transaction_action', $action);
		$transactions->set_variable('transaction_tradetype', $tradeType);
		$transactions->createNew();
	}
	
	
	static public function updateTickerSellPrices($ticker_id, $new_stop, $tradeType=-1, $debug=true){
	
		$allHoldings = new holdings();
		$allHoldings->set_variable('holdings_ticker_id', $ticker_id);
		if ($tradeType >=0 ) $allHoldings->set_variable('holdings_tradetype', $tradeType);
		if ($debug) echo "Updating Ticker Sell Prices for " . $ticker_id . " tt=" . $tradeType ;

		while ($allHoldings->loadNext()){
			$old_stop = $allHoldings->get_variable('holdings_stop_price');
			$allHoldings->set_variable('holdings_stop_price', $new_stop);
			$allHoldings->update();

			if ($debug) echo "\nUPDATED TO " . $new_stop . " from " . $old_stop ;
			
			highlights::holdingsHighlight($allHoldings->get_variable('holdings_id'), H_ABANDON, 2, highlights::EVENT_START_DAY);
		}
	}
	
	static public function GetReturnPercent($tradeType, $last, $start, $t1, $t2, $t3, $hitT1, $hitT2, $hitT3, $abandonPrice=-1){
		$endPrice = ($abandonPrice > 0) ? $abandonPrice : $last;
		$counter = 0;
		$sum = 0;
		if ($hitT1) {
			$counter++;
			$sum += $t1;
		} 
		if ($hitT2) {
			$counter++;
			$sum += $t2;
		} 
		if ($hitT3) {
			$counter++;
			$sum += $t3;
		} 

		$useAccurate = ($abandonPrice > 0);
		
		switch ($tradeType){
			case LONG_TRADE:
				if ($useAccurate){
					$sum += (4-$counter) * $endPrice;
					$orig = 4*$start;
					$return_percent = 100* (($sum  - $orig)/$orig);			
				} else {
					$return_percent = 100* (($endPrice - $start)/$start);
				}
				break;
			case REVERSAL_TRADE:
				if ($useAccurate){
					$sum += (3-$counter) * $endPrice;
					$orig = 3*$start;
					$return_percent = 100* (($sum  - $orig)/$orig);			
				} else {
					$return_percent = 100* (($endPrice - $start)/$start);
				}
				break;
			case SHORT_TRADE:
				if ($useAccurate){
					$sum += (4-$counter) * $endPrice;
					$orig = 4*$start;
					$return_percent = -100* (($sum  - $orig)/$orig);			
				} else {
					$return_percent = -100* (($endPrice - $start)/$start);
				}
				break;
			case BREAKDOWN_TRADE:
				$sum = ($hitT3 && $useAccurate)? ($endPrice + $t3) : $endPrice * 2;
				$orig = 2 * $start;
				$return_percent = -100* (($sum - $orig)/$orig);
				break;
			case BREAKOUT_TRADE:
				$sum = ($hitT3 && $useAccurate)? ($endPrice + $t3) : $endPrice * 2;
				$orig = 2 * $start;
				$return_percent = 100* (($sum - $orig)/$orig);
				break;
			case PULLBACK_TRADE:
				$return_percent = 100* (($endPrice - $start)/$start);			
				break;
			case BACKDRAFT_TRADE:
				$return_percent = -100* (($endPrice - $start)/$start);
				break;
		}	
		return $return_percent;
	}
	
	static public function updateHoldingPrices($holding_id, $today_low, $zone_bottom){
		$holding = new holdings();
		$newOrigPrice = max($today_low, $zone_bottom);
		$holding->set_variable('holdings_id', $holding_id);
		if ($holding->loadNext()){
			echo "\nUPDATING ORIG: " . $holding_id . " $" . $newOrigPrice;
			$holding->set_variable('holdings_init_sell_price_set', 1);
			$holding->set_variable('holdings_orig_price', $newOrigPrice);
			$holding->update();
		}
	}
	static public function abandonAtClose($holding_id, $message="", $textActionClass=ABANDON_AT_CLOSE){
		$holding = new holdings();
		$holding->set_variable('holdings_id', $holding_id);
		if ($holding->loadNext()){
			
			$tId = $holding->get_variable('holdings_ticker_id');
			$symbol = $holding->get_variable('holdings_ticker_symbol');
			$stopPrice = floatval($holding->get_variable('holdings_stop_price'));
			$hId = $holding->get_variable('holdings_id');
			$tradeType = $holding->get_variable("holdings_tradetype");
			if ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE){
				$stopPrice = floatval($holding->get_variable('holdings_top_price'));
			}
			$action = $holding->get_variable("holdings_last_action");
			//if ($action == ABANDON_AT_CLOSE || $action == ABANDON) return;
			if (IsAbandoned($action)) return;
			$tweet = new tweet();
			$abandonEmail = new email(email::ADDRESSES_ALL_CHOSEN, $tradeType, $hId);
	
			if (strlen($symbol) <= 0 ){
				$ticker = new ticker();
				$ticker->set_variable("ticker_id", $tId);
				if ($ticker->load()){
					$symbol = $ticker->get_variable("ticker_symbol");
					$last = $ticker->get_variable('last');
				}
			}
			
			//"ABANDON";
			$action = ABANDON_AT_CLOSE;
			$tweet->newTweet($tradeType, $textActionClass, $symbol, $stopPrice, $message);
			$abandonEmail->newEmail($tradeType, $textActionClass, $symbol, $stopPrice, $message);
			
			$holding->set_variable('holdings_last_action', $action);
			$holding->update();

			highlights::holdingsHighlight($hId, H_LAST_ACTION, 0, highlights::EVENT_END_DAY);
			
			// Add a transaction to the transaction table 
			$transactions = new transactions();
			$transactions->set_variable('transaction_holdings_id', $hId);
			$transactions->set_variable('transaction_price', $stopPrice);
			$transactions->set_variable('transaction_date', date('Y-m-d H:i:s'));
			$transactions->set_variable('transaction_action', ABANDON_AT_CLOSE);
			$transactions->set_variable('transaction_tradetype', $tradeType);
			$transactions->createNew();
		}
	}	

	static public function abandonHardStop($holding_id, $message=""){
		$holding = new holdings();
		$holding->set_variable('holdings_id', $holding_id);
		if ($holding->load()){
			$symbol = $holding->get_variable('holdings_ticker_symbol');
			$tId = $holding->get_variable('holdings_ticker_id');
			$tradeType = $holding->get_variable("holdings_tradetype");
			$stopPrice = floatval($holding->get_variable('holdings_top_price'));
			$action = $holding->get_variable("holdings_last_action");
			if (IsAbandoned($action)) return;
			$tweet = new tweet();
			$abandonEmail = new email(email::ADDRESSES_ALL_CHOSEN, $tradeType, $hId);

			if (strlen($symbol) <= 0 ){
				$ticker = new ticker();
				$ticker->set_variable("ticker_id", $tId);
				if ($ticker->load()){
					$symbol = $ticker->get_variable("ticker_symbol");
					$last = $ticker->get_variable('last');
				}
			}
							
			//"ABANDON";
			$action = ABANDON_HARD_STOP;
			$tweet->newTweet($tradeType, $action, $symbol, $stopPrice, $message);
			$abandonEmail->newEmail($tradeType, $action, $symbol, $stopPrice, $message);
			
			$holding->set_variable('holdings_last_action', $action);
			$holding->set_variable('holdings_stop_price', $stopPrice);
			$holding->update();

			highlights::holdingsHighlight($hId, H_LAST_ACTION, 0, highlights::EVENT_END_DAY);
			
			// Add a transaction to the transaction table 
			$transactions = new transactions();
			$transactions->set_variable('transaction_holdings_id', $holding_id);
			$transactions->set_variable('transaction_price', $stopPrice);
			$transactions->set_variable('transaction_date', date('Y-m-d H:i:s'));
			$transactions->set_variable('transaction_action', ABANDON_HARD_STOP);
			$transactions->set_variable('transaction_tradetype', $tradeType);
			$transactions->createNew();
		}
	}	

	static public function abandonPriceMet($holding_id, $abandonPrice){
		if ($holding_id == 0 || $abandonPrice == 0 ) {
			echo " ERROR: HID = " . $holding_id . " AbandonPrice = " . $abandonPrice;
			return;
		}
		
		$holding = new holdings();
		$holding->set_variable('holdings_id', $holding_id);
		if ($holding->load()){
			$symbol = $holding->get_variable('holdings_ticker_symbol');
			if (strlen($symbol) <= 0 ){
				$tId = $holding->get_variable('holdings_ticker_id');		
				$ticker = new ticker();
				$ticker->set_variable("ticker_id", $tId);
				if ($ticker->load()){
					$symbol = $ticker->get_variable("ticker_symbol");
					$last = $ticker->get_variable('last');
				}
			}
			
			$tradeType = $holding->get_variable("holdings_tradetype");
			$action = $holding->get_variable("holdings_last_action");
			if ($action == WARNING || IsAbandoned($action)) return;

			$holding -> set_variable('holdings_last_action', WARNING);
			$holding -> update();

			holdings::updateHoldingEvent($holding_id, $tradeType, WARNING, $abandonPrice, $action, $symbol);
		}
	}	

	static public function updateHoldingEvent($hId, $tradeType, $action, $actionPrice, $previousAction, $symbol)	{
			$today = date('Y-m-d H:i:s');
			holdings::CreateNewTransaction($hId, $tradeType, $abandonPrice, $action);
			$tweet = new tweet();
			$updateEmail = new email(email::ADDRESSES_ALL_CHOSEN, $tradeType, $hId);

			$tweet->newTweet($tradeType, $action, $symbol, $actionPrice);
			$updateEmail->newEmail($tradeType, $action, $symbol, $actionPrice);	
	}
	
	
	static public function ADMIN_MarkAbandoned($holding_id){
		$updateHolding = new holdings();
		$updateHolding->set_variable('holdings_id', $holding_id);
		if ($updateHolding->loadNext()){
			$tId = $updateHolding->get_variable('holdings_ticker_id');
			$ticker = new ticker();
			$ticker->set_variable("ticker_id", $tId);
			if ($ticker->load()){
				$symbol = $ticker->get_variable("ticker_symbol");
			}
			$updateHolding->set_variable("holdings_abandon_marked", 1);
			$updateHolding->set_variable("holdings_abandon_hide", 1);
			$updateHolding->set_variable('holdings_abandon_date', date('Y-m-d H:i:s'));
			$updateHolding->set_variable("holdings_ticker_symbol", $symbol);
			$updateHolding->update();
		}
	}
}