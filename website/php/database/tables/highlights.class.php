<?php 
class highlights extends genericTable{
	const DB_TABLE_NAME = 'biobounce_highlights'; 
	const DB_UNIQUE_ID = 'highlights_id'; 
	
	const EVENT_HOURS = 0;
	const EVENT_START_DAY = 1;
	const EVENT_END_DAY = 2;
	
	public function __construct(){
		parent::__construct(highlights::DB_TABLE_NAME, highlights::DB_UNIQUE_ID);
	}
	
	static public function getWatchlistHighlights($watchlist_id){
		$highlights = new highlights();
		$highlights->set_variable('highlights_watchlist_id', $watchlist_id);
		$retArr = array(0=>"", 1=>"", 2=>"", 3=>"", 4=>"", 5=>"", 6=>"", 7=>"");
		
		while ($highlights->loadNext()){
			$isValid = true;
			$numHours = $highlights->get_variable('highlights_hourstokeep');
			$start = strtotime($highlights->get_variable('highlights_startdate'));
			$start += ($numHours * 60 * 60);
			$now = strtotime(date("Y-m-d H:i:s"));
			$expiresin = $start - $now;
			if ($expiresin > 0){
				$field = $highlights->get_variable('highlights_field');
				$class = highlights::htmlClass($field);
				$retArr[$field] = $class;
			}
		}
		return $retArr;
	}	
	static public function getHoldingsHighlights($holdings_id){
		$highlights = new highlights();
		$highlights->set_variable('highlights_holdings_id', $holdings_id);
		$retArr = array(8=>"", 9=>"", 10=>"", 11=>"", 12=>"", 13=>"", 14=>"", 15=>"", 16=>"", 17=>"", 18=>"", 19=>"", 20=>"", 21=>"");
		while ($highlights->loadNext()){
			$isValid = true;
			$numHours = $highlights->get_variable('highlights_hourstokeep');
			$start = strtotime($highlights->get_variable('highlights_startdate'));
			$start += ($numHours * 60 * 60);
			$now = strtotime(date("Y-m-d H:i:s"));
			$expiresin = $start - $now;
			if ($expiresin > 0){
				$field = $highlights->get_variable('highlights_field');
				$class = highlights::htmlClass($field);
				$retArr[$field] = $class;
			}
		}
		return $retArr;
	}	

	static public function watchlistHighlight($watchId, $highlightField, $numHours, $event=highlights::EVENT_HOURS){
		$highlights = new highlights();
		$highlights->set_variable('highlights_watchlist_id', $watchId);
		$highlights->set_variable('highlights_startdate', date('Y-m-d H:i:s'));
		$highlights->set_variable('highlights_holdings_id', -1);
		$highlights->set_variable('highlights_tilleventid', $event);
		if ($event != highlights::EVENT_HOURS){
			$numHours = 48;
		}
		$highlights->set_variable('highlights_hourstokeep', $numHours);
		$highlights->set_variable('highlights_field', $highlightField);
		$highlights->createNew();
	}
	static public function holdingsHighlight($holdingsId, $highlightField, $numHours, $event=highlights::EVENT_HOURS){
		$highlights = new highlights();
		$highlights->set_variable('highlights_watchlist_id', -1);
		$highlights->set_variable('highlights_startdate', date('Y-m-d H:i:s'));
		$highlights->set_variable('highlights_holdings_id', $holdingsId);
		$highlights->set_variable('highlights_tilleventid', $event);
		if ($event != highlights::EVENT_HOURS){
			$numHours = 48;
		}
		$highlights->set_variable('highlights_hourstokeep', $numHours);
		$highlights->set_variable('highlights_field', $highlightField);
		$highlights->createNew();
	}
	
	static public function eventTrigger($event){
		$highlights = new highlights();
		$highlights->set_variable('highlights_tilleventid', $event);
		echo "Deleting all highlights because of event id = " . $event;
		$highlights->delete();
	}	
	
	static private function htmlClass($field){
		switch ($field){
			case W_ROW:
				return 'w_row_just_added';
			case W_ROW_DELETE:
				return 'w_row_just_deleted';
			case W_ENTRY_ZONE:
				return 'new_bg';
			case H_T1:
			case H_T2:
			case H_T3:
				return 'new_bg_nocolor';
			case H_ABANDON:
			case H_HARDSTOP:
				return 'new_bg';
			case W_SYMBOL:
			case W_LAST:
			case W_TODAY:
			case W_PERCENT_ZONE:
			case W_T1:
			case H_ROW:
			case H_ZONED_ON:
			case H_SYMBOL:
			case H_TODAY:
			case H_LAST:
			case H_ORIG:
			case H_RETURN:
			case H_LAST_ACTION:
				return '';
		}
	}

}