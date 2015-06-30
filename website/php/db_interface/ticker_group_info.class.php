<?php 
class ticker_group_info{
	
	// THE CORRECT WAY
    static function retrieveWatchlistArray($tradeTypes=LONG_TRADE) {
    	global $mysqli;
		$tradeTypeWhere='';
		if (is_array($tradeTypes)) {
			foreach ($tradeTypes as $tradeType){
				$tradeTypeWhere .= "w.watchlist_tradetype=" . $tradeType . " OR ";
			}
			$tradeTypeWhere = " (". rtrim($tradeTypeWhere, ' OR ').") ";
		}else {
			$tradeTypeWhere = "w.watchlist_tradetype=" . $tradeTypes;
		}
		
		$sql = "SELECT t.ticker_symbol, t.last, t.change_today_percent, t.today_low, 
		w.watchlist_top, w.watchlist_bottom, w.watchlist_is_zoned, w.watchlist_target1, 
		w.watchlist_target3, w.watchlist_id, w.watchlist_tooltip, w.watchlist_low, w.watchlist_tradetype
		FROM `biobounce_watchlist` w, `biobounce_tickers` t WHERE t.ticker_id = w.watchlist_ticker_id AND " . $tradeTypeWhere . "
		ORDER BY w.watchlist_is_zoned ASC, t.ticker_symbol ASC ";
		//h.highlights_startdate, h.highlights_hourstokeep, h.highlights_tilleventid, h.highlights_htmlclassid, h.highlights_field
		//AND h.highlights_watchlist_id = w.watchlist_id
		$sql_query=$mysqli->query($sql);
		$all_return = array();
		while ($row = $sql_query->fetch_assoc()) {
			$all_return [] = $row;
		}
		$sql_query->close();
		return $all_return;
    }
	
	
    static function retrieveHoldingsArray($tradeTypes=LONG_TRADE, $useAllTrades=false) {
    	global $mysqli;
		$tradeTypeWhere='';
		if ($useAllTrades){
			$tradeTypeWhere = "";
		}else if (is_array($tradeTypes)) {
			foreach ($tradeTypes as $tradeType){
				$tradeTypeWhere .= "h.holdings_tradetype=" . $tradeType . " OR ";
			}
			$tradeTypeWhere = " AND (". rtrim($tradeTypeWhere, ' OR ').") ";
		}else {
			$tradeTypeWhere = " AND h.holdings_tradetype=" . $tradeTypes;
		}
		
		$sql = "SELECT 
			t.ticker_id, t.ticker_symbol, t.change_today_percent, t.last, t.today_low, t.today_high, 
			h.holdings_id, h.holdings_t1_marked, h.holdings_t2_marked, h.holdings_t3_marked, h.holdings_tooltip,
			h.holdings_t1, h.holdings_t2, h.holdings_t3, h.holdings_stop_price, h.holdings_init_sell_price_set,
			h.holdings_stop_type, h.holdings_orig_date, h.holdings_orig_price, h.holdings_last_action, h.holdings_top_price, h.holdings_stoch_action, h.holdings_tradetype
		FROM `biobounce_tickers` t, `biobounce_holdings` h WHERE t.ticker_id = h.holdings_ticker_id AND h.holdings_abandon_hide=0 " . $tradeTypeWhere . " 
		ORDER BY h.holdings_orig_date, t.ticker_symbol ASC ";
		$sql_query=$mysqli->query($sql);
		$all_return = array();
		while ($row = $sql_query->fetch_assoc()) {
			$all_return [] = $row;
		}
		$sql_query->close();
		return $all_return;
    }
	
	
    static function retrieveAllAbandonArray($numMonths = 100) {
    	global $mysqli;
		$sql = "SELECT 
			h.holdings_ticker_symbol, h.holdings_id, h.holdings_t1_marked, h.holdings_t2_marked, h.holdings_t3_marked,
			h.holdings_t1, h.holdings_t2, h.holdings_t3, h.holdings_stop_price, h.holdings_init_sell_price_set,
			h.holdings_stop_type, h.holdings_orig_date, h.holdings_abandon_date,h.holdings_orig_price, h.holdings_last_action, h.holdings_top_price, h.holdings_stoch_action, h.holdings_tradetype
		FROM `biobounce_holdings` h WHERE h.holdings_abandon_hide=1 and h.holdings_tradetype>=0 and h.holdings_abandon_date > DATE_SUB(now(), INTERVAL " . $numMonths . " MONTH)
		ORDER BY h.holdings_abandon_date DESC, h.holdings_orig_date ASC ";
		$sql_query=$mysqli->query($sql);
		$all_return = array();
		while ($row = $sql_query->fetch_assoc()) {
			$all_return [] = $row;
		}
		$sql_query->close();
		return $all_return;
	}
	
    static function retrieveAbandonArray($tradeTypes=LONG_TRADE) {
    	global $mysqli;
		$tradeTypeWhere='';
		if (is_array($tradeTypes)) {
			foreach ($tradeTypes as $tradeType){
				$tradeTypeWhere .= "h.holdings_tradetype=" . $tradeType . " OR ";
			}
			$tradeTypeWhere = " (". rtrim($tradeTypeWhere, ' OR ').") ";
		}else {
			$tradeTypeWhere = "h.holdings_tradetype=" . $tradeTypes;
		}
		
		
		$sql = "SELECT 
			h.holdings_ticker_symbol, h.holdings_id, h.holdings_t1_marked, h.holdings_t2_marked, h.holdings_t3_marked,
			h.holdings_t1, h.holdings_t2, h.holdings_t3, h.holdings_stop_price, h.holdings_init_sell_price_set, h.holdings_tradetype,
			h.holdings_stop_type, h.holdings_orig_date, h.holdings_abandon_date,h.holdings_orig_price, h.holdings_last_action, h.holdings_top_price, h.holdings_stoch_action
		FROM `biobounce_holdings` h WHERE h.holdings_abandon_hide=1 AND " . $tradeTypeWhere . "
		ORDER BY h.holdings_abandon_date DESC, h.holdings_orig_date ASC ";
		$sql_query=$mysqli->query($sql);
		$all_return = array();
		while ($row = $sql_query->fetch_assoc()) {
			$all_return [] = $row;
		}
		$sql_query->close();
		return $all_return;
    }

    
    static function retrieveAdminWatchlistArray($tradeType=LONG_TRADE) {
    	global $mysqli;
		$sql = "SELECT w.watchlist_id, t.last, t.ticker_id, t.ticker_symbol, w.watchlist_id, w.watchlist_low, w.watchlist_high, w.watchlist_top, w.watchlist_bottom, w.watchlist_is_zoned, w.watchlist_target0, w.watchlist_target1, w.watchlist_target2, w.watchlist_target3, w.watchlist_tooltip " .
			   "FROM `biobounce_watchlist` w, `biobounce_tickers` t WHERE t.ticker_id = w.watchlist_ticker_id AND w.watchlist_is_deleted = 0 AND w.watchlist_tradetype=" . $tradeType . " ORDER BY t.ticker_symbol ASC ";
		$sql_query=$mysqli->query($sql);
		$all_return = array();
		while ($row = $sql_query->fetch_assoc()) {
			$all_return [] = $row;
		}
		$sql_query->close();
		return $all_return;
    }
    
    static function HoldingThisTradeType($tradeType){
    	global $mysqli;
		$tradeTypeWhere = " AND h.holdings_tradetype=" . $tradeType;
		$sql = "SELECT count(h.holdings_tradetype) as numHoldings
		FROM `biobounce_tickers` t, `biobounce_holdings` h WHERE t.ticker_id = h.holdings_ticker_id AND h.holdings_abandon_hide=0 " . $tradeTypeWhere ;
		$sql_query=$mysqli->query($sql);
		$hasHolding = false;
		while ($row = $sql_query->fetch_assoc()) {
			$numHoldings = $row['numHoldings'];
			if ($numHoldings > 0) $hasHolding = true;
		}
		$sql_query->close();
		return $hasHolding;
		
    }
}