<?php
	const AUTOLOAD_LOCATION = '/home/openm6/public_html/biobounce/db_interface/';
	//const AUTOLOAD_LOCATION = 'C:/xampp/htdocs/biobounce/db_interface/';

// stock market approximate start and end time
echo "\nRequiring\n";
include_once('autoload.php');
include_once('updater.class.php');

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
	//Include the PS_Pagination class
	$holding = new holdings();
	$holding->set_variable("holdings_abandon_hide", 1);
	$symbols = array();
	$hids = array();
	while($holding->loadNext("holdings_abandon_date LIKE '" . date("Y-m-d"). "%'")){
		$symbol= $holding->get_variable('holdings_ticker_symbol');
		$tid= $holding->get_variable('holdings_ticker_id');
		$hid= $holding->get_variable('holdings_id');
		if (strlen($symbol) <= 0 ){
			$ticker = new ticker();
			$ticker->set_variable("ticker_id", $tid);
			if ($ticker->load()){
				$symbol = $ticker->get_variable("ticker_symbol");
				$holding->set_variable('holdings_ticker_symbol', strtoupper($symbol));
				$holding->update();
				echo "\nUpdating Ticker Symbol for " . $symbol;
			}
		}
		$tt = $holding->get_variable('holdings_tradetype');
		$action = $holding->get_variable('holdings_last_action');
		if ($tt == PULLBACK_TRADE && $action != ABANDON) continue;
		if ($tt == BACKDRAFT_TRADE && $action != ABANDON) continue;
		if ($action == ABANDON_HARD_STOP) continue;
		if ($action == ABANDON_AT_CLOSE) continue;
		
		echo "\nAdding Ticker Symbol for " . $symbol . " to price update list.\n" ;
		
		array_push($symbols, strtoupper($symbol));
		array_push($hids, $hid);
	}

	//$results = finance_google::retrieveCurrentPrice($symbols);

	$results = finance::retrieveCurrentPrice($symbols, "o");


	$updateHolding = new holdings();	
	foreach($hids as $updateHid){
	
		$updateHolding->reset_query();
		$updateHolding->set_variable("holdings_id", $updateHid);
		if ($updateHolding->load()){
			$symbol= strtoupper($updateHolding->get_variable('holdings_ticker_symbol'));
			$tid= $holding->get_variable('holdings_ticker_id');
			$price = floatval($results[$symbol]);
			if ($price > 0){
				$updateHolding->set_variable("holdings_stop_price", $price);
				echo "\n<br>Changing Stop Price for " . $symbol . " to " . $price . ". Opening Price after Abandon.\n<br>";
				$updateHolding->update();
			}else{
				$str = "holdings_abandon_date LIKE '" . date("Y-m-d"). "%'";
				echo "\nNOT Changing Stop Price for " . $symbol. " to " . $price . ". Opening Price after Abandon. SOMETHiNG WRONG!! " . $str . "\n<br>";
			}
		}
	}
	
	echo "\ncomplete HOLDINGS abandon update";
}


	