<?php
	const AUTOLOAD_LOCATION = '/home/openm6/public_html/biobounce/db_interface/';
	//const AUTOLOAD_LOCATION = 'C:/xampp/htdocs/biobounce/db_interface/';

// stock market approximate start and end time
$PST_START = 06;
$PST_END = 14;

echo "\nRequiring\n";
include_once('autoload.php');


function GetATR($yesterdaysClose, $todayHigh, $todayLow){
	$val1 = round(abs($yesterdaysClose - $todayHigh), 2);
	$val2 = round(abs($yesterdaysClose - $todayLow), 2);
	$val3 = round(abs($todayHigh - $todayLow),2);
	$finalVal = max($val1, $val2, $val3);
	echo "    VAL1=".$val1." VAL2=".$val2." VAL3=".$val3;
	return $finalVal;
}
function processBreakouts(){
	
	$holding = new holdings();
	$holding->set_variable("holdings_abandon_hide", 0);
	$holding->set_variable("holdings_tradetype", BREAKOUT_TRADE);
	$symbols = array();
	$hids = array();
	while($holding->loadNext()){
		$hId = $holding->get_variable("holdings_id") ;
		$tId = $holding->get_variable("holdings_ticker_id") ;
		$abandon = $holding->get_variable("holdings_stop_price");
		$ticker = new ticker();
		$ticker->set_variable("ticker_id", $tId);
		if ($ticker->load()){
			$symbol = strtoupper($ticker->get_variable("ticker_symbol"));
			echo "Symbol: " . $symbol . "\n";
			$weeksAgo = strtotime("-32 days");			
			$historicalData = finance::retrieveHistorical($symbol, date("Ymd", $weeksAgo), date("Ymd"), 'daily');
			$ATRCount=$ATRSum=0;
			$count = sizeof($historicalData);
			// get the last 19 days of information for the average and add today on the end
			$i = min($count-1, 19);
			for ($j = $i-1; $j >= 0 ; $j--){
				$today = $historicalData[$j]['date'];
				$yesterdaysClose = $historicalData[$i]['close'];
				$todayHigh = $historicalData[$j]['high'];
				$todayLow = $historicalData[$j]['low'];
				$todayClose = $historicalData[$j]['close'];
				echo "TEST - DATE=".$today." PrevDayClose=".$yesterdaysClose." DayHigh=".$todayHigh." DayLow=".$todayLow;
				$finalATRVal = GetATR($yesterdaysClose, $todayHigh, $todayLow);
				$ATRSum += $finalATRVal; 
				$ATRCount++; 
				$i = $j;
				echo " FINAL=".$finalATRVal."\n";
			}

			// ADD Today
			$yesterdaysClose = $todayClose;
			$todayHigh = $ticker->get_variable("today_high");
			$todayLow = $ticker->get_variable("today_low");
			$todayClose = $ticker->get_variable("last_close");
			echo "TEST - DATE=TODAY PrevDayClose=".$yesterdaysClose." DayHigh=".$todayHigh." DayLow=".$todayLow;
			$finalATRVal = GetATR($yesterdaysClose, $todayHigh, $todayLow);
			echo "   FINAL=".$finalATRVal."\n";
	
			$ATRSum += $finalATRVal; 
			$ATRCount++; 
						
			
			$ATRAvg = round($ATRSum/$ATRCount,2);
			echo $symbol . " ATR=" . $ATRAvg . " todayClose=". $todayClose. "\n";			
			$newAbandon=round($todayClose - (2*$ATRAvg),2);
			echo "Comparing Abandons: Old=" . $abandon . "   New=". $newAbandon . "\n";			
			if ($newAbandon > $abandon){
				echo "NEW ABANDON PRICE SET TO " . $newAbandon."\n";
				$holding->set_variable("holdings_stop_price", $newAbandon);
				$holding->update();
				// highlight the abandon price
				highlights::holdingsHighlight($hId, H_ABANDON, 0, highlights::EVENT_START_DAY);

			}
			echo "\n\n\n\n";
		}
	}
}

function processPullbacks(){

	$watches = new watchlist();
	$watches->set_variable("watchlist_tradetype", PULLBACK_TRADE);
	while($watches->loadNext()){
		$tId = $watches->get_variable("watchlist_ticker_id") ;
		$wId = $watches->get_variable("watchlist_id") ;
		$calcHigh = $watches->get_variable("watchlist_high");
		$calcLow = $watches->get_variable("watchlist_low");
		$ticker = new ticker();
		$ticker->set_variable("ticker_id", $tId);
		if ($ticker->load()){
			$dayHigh = $ticker->get_variable("today_high");
			$symbol = strtoupper($ticker->get_variable("ticker_symbol"));
			echo "Testing " . $symbol . " PrevHigh=" . $calcHigh . " DayHigh=" . $dayHigh . "<br>\n";
			if ($dayHigh > $calcHigh){
				echo "          RECALC AND HIGHLIGHTING\n<br>";
				$className = adminControlWatchlist::GetWatchClassName(PULLBACK_TRADE);
				$watchlistAdmin = new $className($wId);
				$watchlistAdmin->Edit($calcLow, $dayHigh); 

				// highlight the abandon price
				highlights::watchlistHighlight($wId, W_ENTRY_ZONE, 0, highlights::EVENT_START_DAY);
			}
		}

	}


}

//Include the PS_Pagination class
// Updating abandon prices on breakouts
processBreakouts();

// Updating watchlist if new high
processPullbacks();

echo "\ncomplete";