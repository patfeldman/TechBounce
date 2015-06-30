<?php
	const AUTOLOAD_LOCATION = '/home/openm6/public_html/biobounce/db_interface/';
	//const AUTOLOAD_LOCATION = 'C:/xampp/htdocs/biobounce/db_interface/';

// stock market approximate start and end time
$PST_START = 06;
$PST_END = 14;

echo "\nRequiring\n";
include_once('autoload.php');


function startOfDayUpdate(){

	// Update the highlights with start of day trigger
	highlights::eventTrigger(highlights::EVENT_START_DAY);
	
	// Make sure no expired users get emails. 
	user::resetAllExpiredEmailUpdates();

	// Delete previously deleted watchlists
	watchlist::pruneDeletedWatchlistIds();
	
	watchlist::deleteZonedTickers(BREAKOUT_TRADE);
	watchlist::deleteZonedTickers(BREAKDOWN_TRADE);
	watchlist::deleteZonedTickers(LONG_TRADE);
	watchlist::deleteZonedTickers(SHORT_TRADE);
	watchlist::deleteZonedTickers(PULLBACK_TRADE);
	watchlist::deleteZonedTickers(BACKDRAFT_TRADE);

	$deleteIds = array();
	$deleteHoldingsIds = array();
	$holdings = new holdings();
	while($holdings->loadNext()){
		$hid = $holdings->get_variable("holdings_id");
		$tid = $holdings->get_variable("holdings_ticker_id");
		$action = $holdings->get_variable("holdings_last_action");
		$hidden = $holdings->get_variable("holdings_abandon_hide");
		$tradeType = $holdings->get_variable("holdings_tradetype");
		if (IsAbandoned($action) && $hidden == 0 ){
			$deleteHoldingsIds[]=$hid;
			echo "\nDELETING Holdings ID ". $hid. " because it was abandoned";
			$watchlist = new watchlist();
			$watchlist->set_variable("watchlist_ticker_id", $tid);
			$watchlist->set_variable("watchlist_tradetype", $tradeType);
			while($watchlist->loadNext()){
				$deleteIds[] = $watchlist->get_variable("watchlist_id");
			}
		}else if (($tradeType==REVERSAL_TRADE || $tradeType==SHORT_TRADE) && $hidden == 0){
			$hitT3 = $holdings->get_variable("holdings_t3_marked");
			if ($hitT3 == 1){
				$deleteHoldingsIds[]=$hid;
				echo "\nDELETING Holdings ID ". $hid. " because topped reversal/shorts";
				$watchlist = new watchlist();
				$watchlist->set_variable("watchlist_ticker_id", $tid);
				$watchlist->set_variable("watchlist_tradetype", $tradeType);
				while($watchlist->loadNext()){
					$deleteIds[] = $watchlist->get_variable("watchlist_id");
				}
			}
		}else if (($tradeType==BACKDRAFT_TRADE || $tradeType==PULLBACK_TRADE) && $hidden == 0){
			$hitT1 = $holdings->get_variable("holdings_t1_marked");
			if ($hitT1 == 1){
				$deleteHoldingsIds[]=$hid;
				echo "\nDELETING Holdings ID ". $hid. " because topped pullback/backdraft";
				$watchlist = new watchlist();
				$watchlist->set_variable("watchlist_ticker_id", $tid);
				$watchlist->set_variable("watchlist_tradetype", $tradeType);
				while($watchlist->loadNext()){
					$deleteIds[] = $watchlist->get_variable("watchlist_id");
				}
			}
		}
	}
	foreach ($deleteIds as $wid){
		echo "\n\n\nDELETING Watchlist ID ". $wid. " because it is abandoned or topped out reversal/shorts";
		$watchlist = new watchlist();
		$watchlist->set_variable('watchlist_id', $wid);
		$watchlist->delete();
	}
	
	
	foreach ($deleteHoldingsIds as $hid){
		echo "\n\n\nDELETING Holdings ID ". $hid. " because it is abandoned or topped out reversal/shorts";
		$holdings = new holdings();
		$holdings->set_variable('holdings_id', $hid);
		// instead of deleting, mark it as hidden
		if ($holdings->load()){
			$tickerInfo = new ticker();
			$tickerInfo->set_variable('ticker_id', $holdings->get_variable('holdings_ticker_id'));
			if ($tickerInfo->load()){
				$holdings->set_variable('holdings_ticker_symbol', $tickerInfo->get_variable('ticker_symbol'));
			}
			$holdings->set_variable('holdings_abandon_hide', 1);
			$holdings->set_variable('holdings_abandon_date', date('Y-m-d H:i:s'));
			$holdings->update();
		}
		// replace this
		//$holdings->delete();
	}


	// delete the tickers that are no longer used. 
	$ticker = new ticker();
	$deleteIds = array();
	while ($ticker->loadNext()){
		$tid = $ticker->get_variable("ticker_id");
		// reset the high and low
		$last = $ticker->get_variable("last");
		$ticker->set_variable("today_high", $last);
		$ticker->set_variable("today_low", $last);
		$ticker->set_variable("last_high", $last);
		$ticker->set_variable("last_low", $last);
		$ticker->update();
		
		$watchlist = new watchlist();
		$watchlist->set_variable("watchlist_ticker_id", $tid);
		if (!$watchlist->load()){
			$holding = new holdings();
			$holding->set_variable("holdings_ticker_id", $tid);
			$holding->set_variable("holdings_abandon_hide", 0);
			if (!$holding->load()){
				$deleteIds[] = $tid;
			}
		}
	}
	foreach ($deleteIds as $tid){
		echo "\n\n\nDELETING TICKER ID ". $tid. " because it is no longer used";
		$ticker = new ticker();
		$ticker->set_variable('ticker_id', $tid);
		$ticker->delete();
	}
	
	$tickerHist = new ticker_history();
	while ($tickerHist->loadNextDistinctTicker()){
		$ticker = new ticker();
		$tid = $tickerHist->get_variable('history_ticker_id');
		$ticker->set_variable('ticker_id', $tid);
		if (!$ticker->load()){
			echo "\nTicker History Cleanup:: Removing " . $tid . "\n";
			ticker_history::removeTickerHistory($tid);
		}
	}
		
	// try to reconcile any payments that are not currently known. 
	// now doing this whenever a payment is received.
	//payment_info::reconcileAllPaymentUids();
	
}

$holidays = array();
$holidays[] = strtotime("2014-02-17");
$holidays[] = strtotime("2014-04-18");
$holidays[] = strtotime("2014-05-26");
$holidays[] = strtotime("2014-07-04");
$holidays[] = strtotime("2014-09-01");
$holidays[] = strtotime("2014-11-27");
$holidays[] = strtotime("2014-12-25");
$holidays[] = strtotime("2015-01-01");
$today = strtotime(date("Y-m-d"));
if (!in_array($today, $holidays)){
	$day = date('N');
	if ($day > 5){
	   echo "SKIP! WEEKEND - " . $day; 
		
	} else {
		//Include the PS_Pagination class
		startOfDayUpdate();
		echo "\ncomplete";

		// SEND THE EXPIRING EMAIL TO THE expiring users
		$email = new email();
		$email->expirationEnding();
		
		// EXPIRE USERS BEFORE THE DAY STARTS
		user::expireDuplicateUsers();
	}
} else {
	echo "\n SKIP! HOLIDAY";
}


	