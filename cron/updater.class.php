<?php
const TIME_BETWEEN_HISTORY_UPDATES_IN_MINUTES = 5;
const NUM_HISTORY_TO_KEEP = 15;
class updater {
	const EOD_ID = '1';
	const HIGH_MINUTE_WINDOW = 10;
	private $symbolsWithExtra;
	private $tickerSymbols;


	public function __construct() {
		$nasdaqNeeded = array();
		$nasdaqNeeded[] = "HALO";
			

		$this -> tickerSymbols = array();
		$this -> symbolsWithExtra = array();
		$ticker = new ticker();
		while ($ticker -> loadNext()) {
			$tickerSymbol = strtoupper($ticker -> get_variable('ticker_symbol'));
			$googleSymbol = $tickerSymbol;
			if (in_array($tickerSymbol, $nasdaqNeeded)){
				$googleSymbol = "NASDAQ:" . $tickerSymbol;
			} 
			$this -> symbolsWithExtra[] = $googleSymbol;
			$this -> tickerSymbols[] = $tickerSymbol;
		}
	}

	public function DEBUG_testPriceChange($tSymbol, $newLast) {
		$date = date('Y-m-d H:i:s');
		$ticker = new ticker();
		while ($ticker -> loadNext(" UPPER(`ticker_symbol`) LIKE '" . strtoupper($tSymbol) . "'")) {
			echo "\n</br> Updating " . $tSymbol . " to " . $newLast;
			$ticker -> set_variable('last', $newLast);
			$ticker -> set_variable('today_high', $newLast);
			$ticker -> set_variable('today_low', $newLast);
			$ticker -> set_variable('last_high', $newLast);
			$ticker -> set_variable('last_low', $newLast);
			$ticker -> set_variable('last_update', $date);
			$ticker -> update();
		}

		$this -> updateWatchlist();
		$this -> updateHoldings();

	}

	public function DEBUG_runTest() {
		$eod = new eod(updater::EOD_ID);
		$phpdate = strtotime($eod -> get_variable('eod_date'));
		$mysqldate = date('Y-m-d h:i:s', $phpdate);
		$currentdate = date('Y-m-d h:i:s', strtotime("-10 seconds"));

		if ($mysqldate < $currentdate) {
			$eod -> set_variable('eod_date', date('Y-m-d h:i:s'));
			$eod -> update();
		}

	}

	public function update() {
		$hour = intval(date('G'));
		$minutes = intval(date('i'));
		echo "\nHOUR: " . $hour . " MINUTE:" . $minutes;
		// 1pm on PST is the closing of the stock market
		$duringTradingDay = (($hour >= 9 && $minutes >= 29) || ($hour >= 10)) && ($hour < 16 || ($hour == 16 && $minutes <= 30));
		if ($duringTradingDay) {
			echo "\nUpdate Minute\n";
			$this -> updateTickers();
			$this -> updateHoldings();
			$this -> updateWatchlist();
		}
	}

	public function ZoneSymbol($list, $symbol, $target) {
		$ticker = new ticker();
		$ticker -> set_variable('ticker_symbol', $symbol);
		if ($ticker -> loadNext()) {
			$tId = $ticker -> get_variable('ticker_id');
			$last = floatval($ticker -> get_variable('last'));
			$holdings = new holdings();
			$holdings -> set_variable('holdings_ticker_id', $tId);
			if ($holdings -> loadNext()) {
				$t1 = floatval($holdings -> get_variable('holdings_t1'));
				$t2 = floatval($holdings -> get_variable('holdings_t2'));
				$t3 = floatval($holdings -> get_variable('holdings_t3'));
				$origPrice = $holdings -> get_variable('holdings_orig_price');
				$hId = $holdings -> get_variable('holdings_id');
				$tradeType = $holdings -> get_variable('holdings_tradetype');

				$newHolding = new holdings();

				if ($list == "L") {
					$action = "";
					if ($target == "T1") {
						$action = SELL1;
						$newHolding -> set_variable('holdings_t1_marked', 1);
						$newHolding -> set_variable('holdings_stop_price', $origPrice);
						watchlist::removeTickerHitT1($tId);
						holdings::updateTickerSellPrices($tId, $origPrice);
						highlights::holdingsHighlight($hId, H_T1, 0, highlights::EVENT_START_DAY);
					} else if ($target == "T2") {
						$action = SELL2;
						$newHolding -> set_variable('holdings_t2_marked', 1);
						holdings::updateTickerSellPrices($tId, $t1);
						highlights::holdingsHighlight($hId, H_T2, 0, highlights::EVENT_START_DAY);
					} else if ($target == "T3") {
						$action = SELL3;
						$newHolding -> set_variable('holdings_t3_marked', 1);
						holdings::updateTickerSellPrices($tId, $t2);
						highlights::holdingsHighlight($hId, H_T3, 0, highlights::EVENT_START_DAY);
					}
					if ($action != "") {
						$newHolding -> set_variable('holdings_id', $hId);
						$newHolding -> set_variable('holdings_last_action', $action);
						$newHolding -> update();

						// Add a transaction to the transaction table
						$transactions = new transactions();
						$transactions -> set_variable('transaction_holdings_id', $hId);
						$transactions -> set_variable('transaction_price', $last);
						$transactions -> set_variable('transaction_date', date('Y-m-d H:i:s'));
						$transactions -> set_variable('transaction_action', $action);
						$transactions -> set_variable('transaction_tradetype', $tradeType);
						$transactions -> createNew();

					}

				}
			}
		}
	}

	private function updateTickers() {
		$results = finance_google::retrieveCurrentPrice($this -> symbolsWithExtra);
		$date = date('Y-m-d H:i:s');
		$ticker = new ticker();
		foreach ($results as $key => $value) {
			$ticker -> reset_query();
			$ticker -> set_variable('ticker_symbol', $key);
			if ($ticker -> load()) {
				$stockUpdates = explode(",", $value);
				$last = $stockUpdates[0];
				echo "\n UPDATING " . $key . " " . $last;
				$ticker -> set_variable('last', $last);
				$change = str_replace('"', "", $stockUpdates[1]);
				$ticker -> set_variable('change_today_percent', $change);
				$ticker -> set_variable('last_update', $date);
				$ticker -> update();
			}
		}

		// UPDATE HISTORY EVERY 5 MIN WARNING:: DEPENDANT ON CRON ONLY RUNNING once a minute
		$hour = intval(date('G'));
		$minutes = intval(date('i'));
		echo "\nTesting Use Google for HOUR: " . $hour . " MINUTE:" . $minutes;
		$useGoogle = ($hour >= 9 && $minutes >= 31) || ($hour >= 10);
		$eod = new eod(updater::EOD_ID);
		$phpdate = strtotime($eod -> get_variable('eod_date'));
		$mysqldate = date('H:i:s Y-m-d', $phpdate);
		$currentdate = date('H:i:s Y-m-d', strtotime("-4 minutes"));

		if (strtotime($mysqldate) <= strtotime($currentdate)) {
			echo "\nupdating history\n";
			$this -> updateHistoryAndBorders($useGoogle);
			$eod -> set_variable('eod_date', date('Y-m-d H:i:s'));
			$eod -> update();
		} else {
			echo "\nNOT updating history\n";
			print_r($mysqldate);
			echo "\n";
			print_r($currentdate);
		}
		/*
		 $hour = intval(date('G'));
		 $minutes = intval(date('i'));
		 $isZero = ($minutes % TIME_BETWEEN_HISTORY_UPDATES_IN_MINUTES) == 0;
		 $isDuringDay = ($hour >= 9 && $minutes >= 31) || ($hour >= 10);
		 $useGoogle = ($hour >= 9 && $minutes >= 31) || ($hour >= 10);
		 echo "\ntesting checks\n";
		 if (!empty($isZero) && $isDuringDay){
		 }
		 * */
	}

	public function updateHistoryAndBorders($useGoogle) {
		echo "\n\nUpdating History and highs and lows\n\n";
		if ($useGoogle) {
			$results = finance_google::retrieveHighsLowsPrice($this -> tickerSymbols);
		}
		$results2 = finance::retrieveCurrentPrice($this -> tickerSymbols, "gh");
		$date = date('Y-m-d H:i:s');
		$ticker = new ticker();
		foreach ($results2 as $key => $value) {
			$ticker -> reset_query();
			$ticker -> set_variable('ticker_symbol', $key);
			if ($ticker -> load()) {
				$tickerClose = $ticker -> get_variable('last_close');
				$tickerId = $ticker -> get_variable('ticker_id');
				$last = $ticker -> get_variable('last');
				$yahooArr = explode(",", $value);
				if ($yahooArr[0] == 0)
					$yahooArr[0] = $last;
				if ($yahooArr[1] == 0)
					$yahooArr[1] = $last;

				$lasthigh = floatval($ticker -> get_variable('today_high'));
				// Yahoo is broken
				//$currenthigh = max($lasthigh, floatval ($yahooArr[1]));
				// TEST CODE
				echo "\nYAHOO HIGH: " . $yahoohigh = max($lasthigh, floatval($yahooArr[1]));
				$currenthigh = $yahoohigh;
				// END TEST CODE
				$googleHigh = $last;
				if ($useGoogle) {
					$googleHigh = floatval($results[$key]['high']);
					if ($googleHigh > 0) {
						echo "\n Updating Last High " . $key . " " . $googleHigh;
						$ticker -> set_variable('last_high', $googleHigh);
						$ticker -> update();
						$currenthigh = max($currenthigh, $googleHigh);
					}
				}

				$lastlow = $ticker -> get_variable('today_low');
				// Yahoo is broken
				//$currentlow = min($lastlow, floatval ($yahooArr[0]));
				// TEST CODE
				$yahoolow = min($lastlow, floatval($yahooArr[0]));
				$yahoolow = ($yahoolow == 0) ? $lastlow : $yahoolow;
				$currentlow = $yahoolow;
				// END TEST CODE

				$googleLow = $last;
				if ($useGoogle) {
					$googleLow = floatval($results[$key]['low']);
					if ($googleLow < 10000) {
						if ($currentlow <= 0) {
							$currentlow = $googleLow;
							$ticker -> set_variable('today_low', $currentlow);
						}
						$currentlow = min($googleLow, $currentlow);
						$ticker -> set_variable('last_low', $googleLow);
						$ticker -> update();
						echo "\n Updating Last Low " . $key . " " . $googleLow . " And today low " . $currentlow;
					}
				}

				$lastupdate = date('Y-m-d', strtotime($ticker -> get_variable('last_highlow_update')));
				$today = date('Y-m-d');

				echo "\n\nTESTING HIGH and LOW of " . $key . " as " . $currenthigh . " : " . $currentlow . "\n";
				$doUpdate = false;
				if ($currenthigh != 0 && ($today != $lastupdate || $currenthigh > $lasthigh)) {
					echo "\nset high " . $currenthigh;
					$ticker -> set_variable('yahoo_high', round($yahoohigh, 2));
					$ticker -> set_variable('today_high', round($currenthigh, 2));
					$doUpdate = true;
				}
				if ($currentlow != 0 && ($today != $lastupdate || $currentlow < $lastlow)) {
					echo "\nset low " . $currentlow;
					$ticker -> set_variable('yahoo_low', round($yahoolow, 2));
					$ticker -> set_variable('today_low', round($currentlow, 2));
					$doUpdate = true;
				}

				if ($doUpdate) {
					if ($today != $lastupdate) {
						echo "\nset last ";
						$ticker -> set_variable('last_highlow_update', $today);
					}
					$ticker -> update();
				}

				// add ticker history
				/*

				 $tickerHistory = new ticker_history();
				 $tickerHistory->set_variable('history_price', $last);
				 $tickerHistory->set_variable('history_date', $date);
				 $tickerHistory->set_variable('history_ticker_id', $tickerId);
				 if ($googleHigh > 0)
				 $tickerHistory->set_variable('history_lasthigh', $googleHigh);
				 else
				 $tickerHistory->set_variable('history_lasthigh', $last);

				 if ($googleLow < 10000)
				 $tickerHistory->set_variable('history_lastlow', $googleLow);
				 else
				 $tickerHistory->set_variable('history_lastlow', $last);
				 $tickerHistory->addStochInfoAndCreate($last);
				 *
				 */
				//ticker_history::addStochInfoAndCreate($key, $tickerId);

				// remove oldest history
				$tickerHistory = new ticker_history();
				$tickerHistory -> set_variable('history_ticker_id', $tickerId);
				$numHistory = $tickerHistory -> countAll('', '', 'history_id');

				$tickerHistory -> reset_query();
				$limit = NUM_HISTORY_TO_KEEP;
				if ($numHistory > $limit) {
					$limit = $numHistory - $limit;
					$tickerHistory -> delete(" WHERE history_ticker_id='" . $tickerId . "' ORDER BY history_date ASC LIMIT " . $limit);
				}
			}
		}
	}

	private function updateWatchlist() {
		$ticker = new ticker();
		$date = date('Y-m-d H:i:s');
		$deleteIds = array();
		echo "\n\nUpdating Watchlist\n";
		while ($ticker -> loadNext()) {
			$tickerId = $ticker -> get_variable('ticker_id');
			$tickerSymbol = $ticker -> get_variable('ticker_symbol');
			$last = floatval($ticker -> get_variable('last'));
			$low = floatval($ticker -> get_variable('today_low'));
			$high = floatval($ticker -> get_variable('today_high'));
			$watchlist = new watchlist();
			$watchlist -> set_variable('watchlist_ticker_id', $tickerId);
			while ($watchlist -> loadNext()) {
				$zoned = $watchlist -> get_variable('watchlist_is_zoned');
				$watchlistId = $watchlist -> get_variable('watchlist_id');
				$bottom = floatval($watchlist -> get_variable('watchlist_bottom'));
				$top = floatval($watchlist -> get_variable('watchlist_top'));
				$watchlistLow = floatval($watchlist -> get_variable('watchlist_low'));
				$tradeType = $watchlist -> get_variable('watchlist_tradetype');
				switch ($tradeType) {
					case SHORT_TRADE :
					case BACKDRAFT_TRADE:
						if ($zoned == 0 && ($last >= $bottom || $high >= $bottom) && ($last <= $top || ($low <= $top && $low > 0))) {
							echo $watchlist -> markZoned($tradeType);
						} else if ($zoned == 1 && $last <= floatval($watchlist -> get_variable('watchlist_target1'))) {
							$deleteIds[] = $watchlistId;
						}
						break;
					case LONG_TRADE :
					case PULLBACK_TRADE : 
						if ($zoned == 0 && $last >= $bottom && ($last <= $top || ($low <= $top && $low > 0))) {
							echo $watchlist -> markZoned($tradeType);
						} else if ($zoned == 1 && $last >= floatval($watchlist -> get_variable('watchlist_target1'))) {
							$deleteIds[] = $watchlistId;
						}
						break;
					case REVERSAL_TRADE :
						if ($zoned == 0 && ($last <= $top || ($low <= $top && $low > 0))) {
							echo $watchlist -> markZoned($tradeType);
						} else if ($zoned == 1 && $last >= floatval($watchlist -> get_variable('watchlist_target1'))) {
							$deleteIds[] = $watchlistId;
						}
						break;
					case BREAKOUT_TRADE :
						if ($zoned == 0 && ($last >= $watchlistLow || $low >= $watchlistLow)) {
							echo $watchlist -> markZoned($tradeType);
						} else if ($zoned == 1 && $last >= floatval($watchlist -> get_variable('watchlist_target1'))) {
							$deleteIds[] = $watchlistId;
						}
						break;
					case BREAKDOWN_TRADE :
						$lowerThanLastAndNotZero = ($last <= $watchlistLow && $last != 0);
						$lowerThanLowAndNotZero = ($low <= $watchlistLow && $low != 0);
						if ($zoned == 0 && ($lowerThanLastAndNotZero || $lowerThanLowAndNotZero)) {
							echo $watchlist->markZoned($tradeType);
						} else if ($zoned == 1 && $last <= floatval($watchlist -> get_variable('watchlist_target1'))) {
							$deleteIds[] = $watchlistId;
						}
						break;
				}
			}
		}
		foreach ($deleteIds as $wId) {
			echo "\n\n\nDELETING WATCHLIST ID " . $wId . " because it passed T1 ";
			$watchlist = new watchlist();
			$watchlist -> set_variable('watchlist_id', $wId);
			$watchlist -> delete();
		}
	}

	private function updateHoldings() {
		$holdings = new holdings();
		$now = date('Y-m-d H:i:s');
		while ($holdings -> loadNextAll()) {
			$last_action = $holdings -> get_variable('holdings_last_action');
			$action = $last_action;
			$last = floatval($holdings -> get_variable('last'));
			$high = floatval($holdings -> get_variable('last_high'));
			$low = floatval($holdings -> get_variable('last_low'));

			// check to make sure we didn't just zone for testing the ticker information
			$origDate = $holdings -> get_variable('holdings_orig_date');
			$diff = (strtotime($now) - (strtotime($origDate) + 60 * updater::HIGH_MINUTE_WINDOW)) / 60;
			if (($diff) < 0)
				$high = $last;

			if ($high <= 0)
				$high = $last;
			if ($low <= 0)
				$low = $last;

			$symbol = $holdings -> get_variable('ticker_symbol');
			$tId = $holdings -> get_variable('ticker_id');
			//$t0 = floatval($holdings->get_variable('holdings_t0'));
			$t1 = floatval($holdings -> get_variable('holdings_t1'));
			$t2 = floatval($holdings -> get_variable('holdings_t2'));
			$t3 = floatval($holdings -> get_variable('holdings_t3'));
			$t1Marked = $holdings -> get_variable('holdings_t1_marked');
			$t2Marked = $holdings -> get_variable('holdings_t2_marked');
			$t3Marked = $holdings -> get_variable('holdings_t3_marked');
			$stopType = $holdings -> get_variable('holdings_stop_type');
			$stopPrice = $holdings -> get_variable('holdings_stop_price');
			$topPrice = $holdings -> get_variable('holdings_top_price');

			$origPrice = $holdings -> get_variable('holdings_orig_price');
			$hId = $holdings -> get_variable('holdings_id');
			$tradeType = $holdings -> get_variable('holdings_tradetype');

			$newHolding = new holdings();
			if ($tradeType == SHORT_TRADE) {
				if (!$t1Marked && (($last <= $t1) || ($low <= $t1))) {
					//"SELL #1";
					echo "\n\n\nSHORT BUY 1 and send tweet!";
					$action = SELL1;
					$actionPrice = $t1;
					$newHolding -> set_variable('holdings_t1_marked', 1);

					// update sell price of all holdings with this ticker_id
					watchlist::removeTickerHitT1($tId);
					//holdings::updateTickerSellPrices($tId, $origPrice);
					highlights::holdingsHighlight($hId, H_T1, 0, highlights::EVENT_START_DAY);
				} else if (!$t2Marked && (($last <= $t2) || ($low <= $t2))) {
					//"SELL #2";
					echo "\n\n\nSHORT BUY2 and send tweet!";
					$action = SELL2;
					$actionPrice = $t2;
					$newHolding -> set_variable('holdings_t2_marked', 1);
					holdings::updateTickerSellPrices($tId, $t1);
					highlights::holdingsHighlight($hId, H_T2, 0, highlights::EVENT_START_DAY);
				} else if (!$t3Marked && (($last <= $t3) || ($low <= $t3))) {
					//"SELL #3";
					echo "\n\n\nSELL 3 and send tweet!";
					$actionPrice = $t3;
					$action = SELL3;
					$newHolding -> set_variable('holdings_t3_marked', 1);
					holdings::updateTickerSellPrices($tId, $t3);
					highlights::holdingsHighlight($hId, H_T3, 0, highlights::EVENT_START_DAY);
				} else if (!$t3Marked && $last >= $stopPrice) {
					if ($last_action != WARNING && !IsAbandoned($last_action)) {
						//"WARNING";
						echo "\n\n\nWARNING and send tweet!";
						$action = WARNING;
						$actionPrice = $stopPrice;
						
					}
				}
				if ($action != $last_action) {
					$newHolding -> set_variable('holdings_id', $hId);
					$newHolding -> set_variable('holdings_last_action', $action);
					$newHolding -> update();

					// Add a transaction to the transaction table
					holdings::CreateNewTransaction($hId, $tradeType, $actionPrice, $action);
				}

			} else if ($tradeType == LONG_TRADE) {
				if (!$t1Marked && (($last >= $t1) || ($high >= $t1))) {
					//"SELL #1";
					echo "\n\n\nSELL 1 and send tweet!";
					$action = SELL1;
					$actionPrice = $t1;
					
					$newHolding -> set_variable('holdings_t1_marked', 1);
					$newHolding -> set_variable('holdings_stop_price', $origPrice);

					// update sell price of all holdings with this ticker_id
					watchlist::removeTickerHitT1($tId);
					//holdings::updateTickerSellPrices($tId, $origPrice);
					highlights::holdingsHighlight($hId, H_T1, 0, highlights::EVENT_START_DAY);
				} else if (!$t2Marked && (($last >= $t2) || ($high >= $t2))) {
					//"SELL #2";
					echo "\n\n\nSELL 2 and send tweet!";
					$action = SELL2;
					$actionPrice = $t2;
					
					$newHolding -> set_variable('holdings_t2_marked', 1);
					holdings::updateTickerSellPrices($tId, $t1);
					highlights::holdingsHighlight($hId, H_T2, 0, highlights::EVENT_START_DAY);
				} else if (!$t3Marked && (($last >= $t3) || ($high >= $t3))) {
					//"SELL #3";
					echo "\n\n\nSELL 3 and send tweet!";
					$action = SELL3;
					$actionPrice = $t3;
					
					$newHolding -> set_variable('holdings_t3_marked', 1);
					holdings::updateTickerSellPrices($tId, $t2);
					highlights::holdingsHighlight($hId, H_T3, 0, highlights::EVENT_START_DAY);
				} else if ($last <= $stopPrice) {
					if ($last_action != WARNING && !IsAbandoned($last_action)) {
						//"WARNING";
						echo "\n\n\nWARNING and send tweet!";
						$action = WARNING;
						$actionPrice = $stopPrice;					
					}
				}
				if ($action != $last_action) {
					$newHolding -> set_variable('holdings_id', $hId);
					$newHolding -> set_variable('holdings_last_action', $action);
					$newHolding -> update();

					// Add a transaction to the transaction table
					holdings::CreateNewTransaction($hId, $tradeType, $actionPrice, $action);
				}
			} else if ($tradeType == REVERSAL_TRADE) {
			} else {
				$abandonPrice = $stopPrice;
				$hardStopPrice = $topPrice;
				switch ($tradeType) {
					case BREAKOUT_TRADE :
						if (!$t3Marked && (($last >= $t3) || ($high >= $t3))) {
							$holdings -> markTarget(3, $symbol, $last, true, $t3);
						} else if ($last <= $hardStopPrice) {
							holdings::abandonHardStop($hId, "");
						} else if ($last <= $abandonPrice) {
							holdings::abandonPriceMet($hId, $abandonPrice);
							
							// if ($last_action != WARNING && !IsAbandoned($last_action)) {
								// //"WARNING";
								// echo "\n\n\nWARNING and send tweet!";
								// $action = WARNING;
								// $actionPrice = $abandonPrice;
								// $newHolding -> set_variable('holdings_id', $hId);
								// $newHolding -> set_variable('holdings_last_action', $action);
								// $newHolding -> update();
// 
								// holdings::CreateNewTransaction($hId, $tradeType, $abandonPrice, $action);
							// }
						}
						break;
					case PULLBACK_TRADE:
						if (!$t1Marked && (($last >= $t1) || ($high >= $t1))) {
							$holdings -> markTarget(1, $symbol, $last, true, $t1);
						} else if (!$t1Marked && $last <= $abandonPrice) {
							holdings::abandonPriceMet($hId, $abandonPrice);
							// if ($last_action != WARNING && !IsAbandoned($last_action)) {
								// //"WARNING";
								// echo "\n\n\nWARNING and send tweet!";
								// $action = WARNING;
								// $actionPrice = $abandonPrice;
								// $newHolding -> set_variable('holdings_id', $hId);
								// $newHolding -> set_variable('holdings_last_action', $action);
								// $newHolding -> update();
// 
								// holdings::CreateNewTransaction($hId, $tradeType, $abandonPrice, $action);
							// }
						}
						break;
					case BACKDRAFT_TRADE:
						if (!$t1Marked && (($last <= $t1) || ($low <= $t1))) {
							$holdings -> markTarget(1, $symbol, $last, true, $t1);
						} else if (!$t1Marked && $last >= $abandonPrice) {
							holdings::abandonPriceMet($hId, $abandonPrice);
							// if ($last_action != WARNING && !IsAbandoned($last_action)) {
								// //"WARNING";
								// echo "\n\n\nWARNING and send tweet!";
								// $action = WARNING;
								// $actionPrice = $abandonPrice;
								// $newHolding -> set_variable('holdings_id', $hId);
								// $newHolding -> set_variable('holdings_last_action', $action);
								// $newHolding -> update();
// 
								// holdings::CreateNewTransaction($hId, $tradeType, $abandonPrice, $action);
							// }
						}
						break;
					case BREAKDOWN_TRADE :
						if (!$t3Marked && (($last <= $t3) || ($high <= $t3))) {
							$holdings -> markTarget(3, $symbol, $last, true, $t3);
						} else if ($last >= $hardStopPrice) {
							holdings::abandonHardStop($hId, "");
						} else if ($last >= $abandonPrice) {
							holdings::abandonPriceMet($hId, $abandonPrice);
							// if ($last_action != WARNING && !IsAbandoned($last_action)) {
								// //"WARNING";
								// echo "\n\n\nWARNING and send tweet!";
								// $action = WARNING;
								// $actionPrice = $abandonPrice;
								// $newHolding -> set_variable('holdings_id', $hId);
								// $newHolding -> set_variable('holdings_last_action', $action);
								// $newHolding -> update();
// 
								// holdings::CreateNewTransaction($hId, $tradeType, $abandonPrice, $action);
							// }
						}

						break;
				}
			}

			// OBSOLETE WITH THE NEW METHODS
			if ($action != $last_action){
				$tweet = new tweet();
				$updateEmail = new email(email::ADDRESSES_ALL_CHOSEN, $tradeType, $hId);

				$tweet->newTweet($tradeType, $action, $symbol, $actionPrice);
				$updateEmail->newEmail($tradeType, $action, $symbol, $actionPrice);
			}
	
		}
	}

	private function updateStochStateMachine($hId, $tId, $symbol, $last, $stoch_action, $action) {
		$new_stoch_action = $stoch_action;
		$func = "";
		if ($stoch_action != STOCH_STATE_SELL3) {
			echo "\nTESTING STOCH MACHINE " . $symbol;
			$tickerHistory = new ticker_history();
			$tickerHistory -> set_variable('history_ticker_id', $tId);
			if ($tickerHistory -> loadLatest()) {
				$slowk = $tickerHistory -> get_variable('history_slowk');
				$slowd = $tickerHistory -> get_variable('history_slowd');
				switch ($stoch_action) {
					case STOCH_PENDING :
						if ($action == BUY && $slowk <= 20) {
							// TEST 1 - set when slow K is below 20
							$new_stoch_action = STOCH_STATE_BUY_WAIT;
							$func = stoch_state_buy_wait;
						}
						break;
					case STOCH_STATE_BUY_WAIT :
						if ($slowk >= 20) {
							// BUY NOW
							$new_stoch_action = STOCH_STATE_BUY;
							$func = stoch_state_buy;
						}
						break;
					case STOCH_STATE_BUY :
						if ($slowk >= 80 && $action == SELL1) {
							$new_stoch_action = STOCH_STATE_SELL1_WAIT;
							$func = stoch_state_sell_wait;
						}
						break;
					case STOCH_STATE_SELL1 :
						if ($slowk >= 80 && $action == SELL2) {
							$new_stoch_action = STOCH_STATE_SELL2_WAIT;
							$func = stoch_state_sell_wait;
						}
						break;
					case STOCH_STATE_SELL2 :
						if ($slowk >= 80 && $action == SELL3) {
							$new_stoch_action = STOCH_STATE_SELL3_WAIT;
							$func = stoch_state_sell_wait;
						}
						break;
					case STOCH_STATE_SELL1_WAIT :
					case STOCH_STATE_SELL2_WAIT :
					case STOCH_STATE_SELL3_WAIT :
						if ($slowk >= 80) {
							if ($stoch_action == STOCH_STATE_SELL1_WAIT && $action == SELL2) {
								$new_stoch_action = STOCH_STATE_SELL2_WAIT;
								$func = stoch_state_sell_wait;
							}
							if ($stoch_action == STOCH_STATE_SELL2_WAIT && $action == SELL3) {
								$new_stoch_action = STOCH_STATE_SELL3_WAIT;
								$func = stoch_state_sell_wait;
							}
						} else if ($slowk <= 80) {
							if ($stoch_action == STOCH_STATE_SELL1_WAIT) {
								$new_stoch_action = STOCH_STATE_SELL1;
								$func = stoch_state_sell;
							} else if ($stoch_action == STOCH_STATE_SELL2_WAIT) {
								$new_stoch_action = STOCH_STATE_SELL2;
								$func = stoch_state_sell;
							} else if ($stoch_action == STOCH_STATE_SELL3_WAIT) {
								$new_stoch_action = STOCH_STATE_SELL3;
								$func = stoch_state_sell;
							}
						}
						break;
				}
			}
		}
		if ($new_stoch_action != $stoch_action) {
			echo "\n" . $symbol . " UPDATING STOCH STATE MACHINE FROM " . $stoch_action . " TO " . $new_stoch_action;
			$newHolding = new holdings();
			$newHolding -> set_variable('holdings_id', $hId);
			$newHolding -> set_variable('holdings_stoch_action', $new_stoch_action);
			$newHolding -> update();

			$tweetprivate = new tweetprivate();
			$tweetprivate -> $func($symbol, $last, $new_stoch_action);
		}

	}

	// TODO UPDATE THIS WITH SHORT SALE STUFF
	// BELOW HERE

}