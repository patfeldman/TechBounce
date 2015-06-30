<?php 
class ticker_history extends genericTable{
	const DB_TABLE_NAME = 'biobounce_tickers_history'; 
	const DB_UNIQUE_ID = 'history_id'; 
	const CHART_COUNT = 12;
	const N_COUNT = 14;
	const K_SLOW_COUNT = 3; 
	const D_SLOW_COUNT = 3; 
	
	public function __construct(){
		parent::__construct(ticker_history::DB_TABLE_NAME, ticker_history::DB_UNIQUE_ID);
	}
	public function loadLatest(){
		$orderAndLimit = ' ORDER BY history_date DESC LIMIT 1';
		return $this->load('', '*', TRUE,  $orderAndLimit);
	}
	
	static public function getSlowKAndSlowDArray($tid){
		global $mysqli;
		$sql = "
			select history_slowk, history_slowd
			from `biobounce_tickers_history`
			WHERE history_ticker_id = ". $tid . " 
			ORDER BY  history_date DESC 
			 LIMIT 0, ". ticker_history::CHART_COUNT;
		$sql_query=$mysqli->query($sql) or die("SQL ERROR " . mysqli_error());
		$retArr = array();
		while($row = $sql_query->fetch_assoc()) {
			$retArr['slowk'][] = $row['history_slowk'];
			$retArr['slowd'][] = $row['history_slowd'];
		}
		
		$sql_query->close();
		return $retArr;
	}

	static public function addStochInfoAndCreate($ticker, $tid){
		$parts = finance_google::retrieveSlowDAndParts($ticker);
	    
		echo "\nUpdating history for " . $ticker . " TID:" . $tid;
		$tickerHistory = new ticker_history();
		$tickerHistory->set_variable('history_price', $parts['last']);
		$date = date("Y-m-d h:i:s", $parts['date']);
		$tickerHistory->set_variable('history_date', $date);
		$tickerHistory->set_variable('history_ticker_id', $tid);
		$tickerHistory->set_variable('history_lasthigh', $parts['high']);
		$tickerHistory->set_variable('history_lastlow', $parts['low']);
		$tickerHistory->set_variable('history_r1', $parts['r1']);
		$tickerHistory->set_variable('history_r2', $parts['r2']);
		$tickerHistory->set_variable('history_fastk', $parts['fastK']);
		$tickerHistory->set_variable('history_slowk', $parts['slowK']);
		$tickerHistory->set_variable('history_slowd', $parts['slowD']);
		$tickerHistory->createNew();
	}

	public function loadNextDistinctTicker(){
		if ($this->sql_query  && isset($this->sql_query)){
			if ($row = $this->sql_query->fetch_assoc() ) 
				$this->loadRowInformation($row);
			else
				return false;
		}else{
			$sql = "SELECT DISTINCT(history_ticker_id) FROM biobounce_tickers_history";
			$this->sql_query=$this->db->query($sql);
			if ($row = $this->sql_query->fetch_assoc() ) 
				$this->loadRowInformation($row);
			else 
				return false;
			return true;
		}
		return true;
		
	}
	
	static public function removeTickerHistory($tid){
		global $mysqli;
		
		$sql = "DELETE FROM "  . ticker_history::DB_TABLE_NAME . " WHERE history_ticker_id=" . $tid;
		$mysqli->query($sql);
	}
}