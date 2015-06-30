<?php
	const AUTOLOAD_LOCATION = '/home/openm6/public_html/biobounce/db_interface/';
	//const AUTOLOAD_LOCATION = 'C:/xampp/htdocs/biobounce/db_interface/';

// stock market approximate start and end time
$PST_START = 06;
$PST_END = 14;

include_once('autoload.php');

function abandonNowStocks(){
	$holding = new holdings();
	$holding->set_variable("holdings_last_action", ABANDON_AT_CLOSE);
	$holding->set_variable("holdings_abandon_marked", 0);
	$symbols = array();
	$hids = array();
	$hasAbandon = false;
	while($holding->loadNext()){
		$symbol= $holding->get_variable('holdings_ticker_symbol');
		$tid= $holding->get_variable('holdings_ticker_id');
		$hid= $holding->get_variable('holdings_id');
		if (strlen($symbol) <= 0 ){
			$ticker = new ticker();
			$ticker->set_variable("ticker_id", $tid);
			if ($ticker->load()){
				$symbol = $ticker->get_variable("ticker_symbol");
			}
		}
		array_push($symbols, strtoupper($symbol));
		array_push($hids, $hid);
		$hasAbandon = true;
	}

	if ($hasAbandon){
	
		print_r($symbols);
		print_r($hids);
		$results = finance_google::retrieveCurrentPrice($symbols);
	
		$updateHolding = new holdings();
		foreach ($results as $key=>$value){
			if ($key=="filesize") continue;
			$updateHolding->reset_query();
			$ticker = $key;
			$price = floatval($value);	
			if ($price > 0 ){
				echo "\nTICKER:" . $ticker . " PRICE=" . $price . "\n";
				$index = array_search($ticker, $symbols);
				$hid = $hids[$index];
				$updateHolding->set_variable("holdings_id", $hid);
				if (!$updateHolding->load()){
					echo "WRONG::" . $ticker . " NOT RECOGNIZED IN DB\n\n";
					continue;
				}
				$updateHolding->set_variable("holdings_stop_price", $price);
				echo "\nChanging Stop Price for " . $ticker . " to " . $price . ". Opening Price at end of day.\n";
				$updateHolding->set_variable("holdings_abandon_marked", 1);
				$updateHolding->set_variable("holdings_abandon_hide", 1);
				$updateHolding->set_variable('holdings_abandon_date', date('Y-m-d H:i:s'));
				$updateHolding->set_variable("holdings_ticker_symbol", $ticker);
				
				$updateHolding->update();
			}
		}
	}
}

function endOfDayUpdate(){


	// Update the highlights with end of day trigger
	highlights::eventTrigger(highlights::EVENT_END_DAY);
	
	// Abandon the stocks that were marked abandon during the day
	abandonNowStocks();
	
	// update the close price of the ticker 
	$ticker = new ticker();
	while ($ticker->loadNext()){
		$last = $ticker->get_variable('last');
		$ticker->set_variable('last_close', $last);
		$ticker->update();
	}
	
	/// update the holdings 
	$holdings = new holdings();	
	while ($holdings->loadNextAll()){
		$last_action = $holdings->get_variable('holdings_last_action');
		if (IsAbandoned($last_action)) continue;
		$last = floatval($holdings->get_variable('last'));
		$low = floatval($holdings->get_variable('today_low'));
		$t3 = floatval($holdings->get_variable('holdings_t3'));
		$tId = $holdings->get_variable('ticker_id');
		$symbol = $holdings->get_variable('ticker_symbol');
		$stopType = $holdings->get_variable('holdings_stop_type');
		$stopPrice = floatval($holdings->get_variable('holdings_stop_price'));
		$hId = $holdings->get_variable('holdings_id');
		$initSellPriceSet = $holdings->get_variable('holdings_init_sell_price_set');
		$isT1Marked = $holdings->get_variable('holdings_t1_marked');
		$tradeType = $holdings->get_variable("holdings_tradetype");
		echo "\n\n\nChecking ". $hId . " ticker " . $tId . " against $" . $last;

		$action = NONE;
		switch ($tradeType){
			case BREAKDOWN_TRADE:
			case SHORT_TRADE:
				$doAbandon = ($last > $stopPrice);
				break;
			case BACKDRAFT_TRADE:
				$doAbandon = ($last > $stopPrice && !$isT1Marked);
				break;
			case LONG_TRADE:
			case REVERSAL_TRADE:
			case BREAKOUT_TRADE:
				$doAbandon = ($last < $stopPrice);
				break;
			case PULLBACK_TRADE:
				$doAbandon = ($last < $stopPrice && !$isT1Marked);
				break;
		}
		if ($doAbandon){
			$tweet = new tweet();
			$abandonEmail = new email(email::ADDRESSES_SMS_ONLY, $tradeType, $hId);
			$action = ABANDON;
			
			$tweet->newTweet($tradeType, $action, $symbol, $stopPrice);
			$abandonEmail->newEmail($tradeType, $action, $symbol, $stopPrice);
			
			$newHolding = new holdings();
			$newHolding->set_variable('holdings_id', $hId);
			$newHolding->set_variable('holdings_last_action', $action);
			$newHolding->set_variable('holdings_abandon_marked', 1);
			$newHolding->update();
	
			// Add a transaction to the transaction table 
			holdings::CreateNewTransaction($hId, $tradeType, $stopPrice, $action);
		}

		if ($action != ABANDON && $last_action == WARNING){
			// reset WARNING list
			$t1Marked = $holdings->get_variable('holdings_t1_marked');
			$t2Marked = $holdings->get_variable('holdings_t2_marked');
			$t3Marked = $holdings->get_variable('holdings_t3_marked');
			
			if ($t3Marked){
				$next_action = SELL3;
			} else if ($t2Marked){
				$next_action = SELL2;
			} else if ($t1Marked){
				$next_action = SELL1;
			} else {
				$next_action = BUY;
			}
			
			$newHoldings = new holdings();
			$newHoldings->set_variable('holdings_id', $hId);
			$newHoldings->set_variable('holdings_last_action', $next_action);
			echo "\nREPLACE WARNING WITH LAST ACTION\n";
			echo $newHoldings->debug();
			$newHoldings->update();
		}
		
		
	}

	// end of day, check to see if we are out of zone
	/*
	$watchlist = new watchlist();
	$watchlist->set_variable('watchlist_is_zoned', 1);
	$deleteIds = array();
	while ($watchlist->loadNext()){
		$tradeType = $watchlist->get_variable("watchlist_tradetype");
		$ticker = new ticker();
		$ticker->set_variable('ticker_id', $watchlist->get_variable('watchlist_ticker_id'));
		if ($ticker->load()){
			if ($tradeType==SHORT_TRADE){
				$high = floatval($ticker->get_variable('today_high'));
				$bottom = floatval($watchlist->get_variable('watchlist_bottom'));
				if ($high < $bottom){
					$watchlistId = floatval($watchlist->get_variable('watchlist_id'));
					$deleteIds[] = $watchlistId;
				}
			}else if ($tradeType==LONG_TRADE){
				$low = floatval($ticker->get_variable('today_low'));
				$top = floatval($watchlist->get_variable('watchlist_top'));
				if ($low > $top){
					$watchlistId = floatval($watchlist->get_variable('watchlist_id'));
					$deleteIds[] = $watchlistId;
				}
			}else if ($tradeType==REVERSAL_TRADE){
				$low = floatval($ticker->get_variable('today_low'));
				$top = floatval($watchlist->get_variable('watchlist_top'));
				if ($low > $top){
					$watchlistId = floatval($watchlist->get_variable('watchlist_id'));
					$deleteIds[] = $watchlistId;
				}
			}
		}
	}
	foreach ($deleteIds as $wId){
		echo "\n\n\nDELETING WATCHLIST ID ". $wId. " because it was higher than top all day";
		$watchlist = new watchlist();
		$watchlist->set_variable('watchlist_id', $wId);
		$watchlist->delete();
	}
	 * 
	 */
}

function endOfDayEmail(){
	$email = new email(email::ADDRESSES_ALL_ACTIVE);

	$allText = array(BREAKOUT_TRADE=>'',LONG_TRADE=>'', PULLBACK_TRADE=>'', SHORT_TRADE=>'', BACKDRAFT_TRADE=>'', BREAKDOWN_TRADE=>'');
	$transHistory = new transactions();
	while ($transHistory->loadNext(' DATE(`transaction_date`) = DATE(NOW()) ')){
		$hId = $transHistory->get_variable('transaction_holdings_id');
		$holdings = new holdings();
		$holdings->set_variable("holdings_id", $hId);
		if (!$holdings->load()) continue;
		$tId = $holdings->get_variable("holdings_ticker_id");
		
		$ticker = new ticker();
		$ticker->set_variable('ticker_id', $tId);
		if ($ticker->load()){
			$symbol = $ticker->get_variable('ticker_symbol');
			//echo "Adding to EOD EMAIL " . $symbol . "\n\n";
			$tranDate = $transHistory->get_variable('transaction_date');
			$tz = new DateTimeZone('America/New_York');
			$date = new DateTime($tranDate);
			$date->setTimeZone($tz);
			$dateStr = $date->format("h:i A");
			$tradeType = $transHistory->get_variable('transaction_tradetype');
			$action = intval($transHistory->get_variable('transaction_action'));
			$actionPrice = floatval($transHistory->get_variable('transaction_price'));
			$actionInfo = email::GetEmailText($tradeType, $action, $symbol, $actionPrice, "", false);
			if (strlen($actionInfo["body"]) > 0){
				$allText[$tradeType] .= "<div style=\"\" >" .  $dateStr . " - " . $actionInfo["body"] . "</div>";
			}
		}
	}

	
	$bodyText = '';
	foreach ($allText as $key=>$value){
		if (strlen($value) > 0){
			$bodyText .= "<div style=\"font-size:13pt;font-family:Helvetica;text-decoration:underline;text-align:center;padding-bottom:7px;padding-top:20px;font-weight:bold\">" . GetTradeTypeConstantName($key) . "</div>" . $value;
		}
	}
	if (strlen($bodyText) == 0){
		echo "NO TRANSACTIONS TODAY " . $transHistory->debug();
		$email->endOfDay("There were no BioBounce events today.");
	}else {
		echo "SENDING EOD EMAIL\n\n";
		$email->endOfDay($bodyText);
	}
}



$holidays = array();
$holidays[] = strtotime("2015-04-03");
$holidays[] = strtotime("2014-04-18");
$holidays[] = strtotime("2014-05-26");
$holidays[] = strtotime("2014-07-04");
$holidays[] = strtotime("2014-09-01");
$holidays[] = strtotime("2014-11-27");
$holidays[] = strtotime("2014-12-25");
$holidays[] = strtotime("2015-01-01");
$today = strtotime(date("Y-m-d"));
if (!in_array($today, $holidays)){

	endOfDayUpdate();
	endOfDayEmail();
	
	echo "\ncomplete EOD update";
}


	