<?php
class tweet{
	private $twitterConnection;
	private $twitterContent;
	public function __construct(){
		$this->twitterConnection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
		$this->twitterContent = $this->twitterConnection->get('account/verify_credentials');
	}
	
	public function sendTweet($body){
		//if (IS_TESTING) { echo "TESTING - NO TWEETS"; return ; }
		if (strlen($body) > 0){
			$postinfo = $this->twitterConnection->post('statuses/update', array('status' => $body));
		}
	}
	
	public function newTweet($tradeType, $action, $symbol, $last, $optionalMessage=""){
		$tweetInfo = email::GetEmailText($tradeType, $action, $symbol, $last, $optionalMessage);
		$this->sendTweet($tweetInfo["body"]);
	}

	public function sendWatchlistUpdate(){
	 	$body = "The BioBounce.com Watchlist and Holdings list are up to date for this evening.";
		$this->sendTweet($body);		
	}
	
/*
	public function sell1($tickername, $sellprice, $tradeType=LONG_TRADE){
		$str1 = GetTradeTypeConstantNameSingular($tradeType);
	 	$body = $str1 . ":" . strtoupper($tickername) . " just passed Target 1 and is trading at $" . number_format($sellprice, 2) . ".";
		$this->sendTweet($body);
	}
		
	public function sell2($tickername, $sellprice, $tradeType=LONG_TRADE){
		$str1 = GetTradeTypeConstantNameSingular($tradeType);
	 	$body = $str1 . ":" . strtoupper($tickername) . " just passed Target 2 and is trading at $" . number_format($sellprice, 2) . ".";
		$this->sendTweet($body);
	}
		
	public function sell3($tickername, $sellprice, $tradeType=LONG_TRADE){
		$str1 = GetTradeTypeConstantNameSingular($tradeType);
	 	$body = $str1 . ":" . strtoupper($tickername) . " just passed Target 3 and is trading at $" . number_format($sellprice, 2) . ".";
		$this->sendTweet($body);
	}
		
	public function buy1($tickername, $tickerprice, $tradeType){
		$str1 = GetTradeTypeConstantNameSingular($tradeType);
		$body =  $str1 . ":" . strtoupper($tickername) . " passed T1 and is down to " . number_format($tickerprice, 2) . ", buy to cover.";
		$this->sendTweet($body);
	}
	public function buy2($tickername, $tickerprice, $tradeType){
		$str1 = GetTradeTypeConstantNameSingular($tradeType);
		$body = $str1 . ":" . strtoupper($tickername) . " passed T2 and is down to " . number_format($tickerprice, 2) . ", buy to cover.";
		$this->sendTweet($body);
	}
	public function buy3($tickername, $tickerprice, $tradeType){
		$str1 = GetTradeTypeConstantNameSingular($tradeType);
		$body =  $str1 . ":" . strtoupper($tickername) . " passed T3 and is down to " . number_format($tickerprice, 2) . ", buy to cover.";
		$this->sendTweet($body);
	}


	public function abandon($tickername, $sellprice, $stop_limit, $tradeType = LONG_TRADE){
		$str1 = GetTradeTypeConstantNameSingular($tradeType);
		switch ($tradeType){
			case BREAKDOWN_TRADE:
			case LONG_TRADE:
			case REVERSAL_TRADE:
			 	$body = str1 . "::ABANDON TICKER " . strtoupper($tickername) . ". It closed below its ABANDON price of $" . number_format($stop_limit, 2) . ".";
				break;
			case BREAKOUT_TRADE:
			case SHORT_TRADE:
			 	$body = str1 . "::ABANDON TICKER " . strtoupper($tickername) . ". It closed above its ABANDON price of $" . number_format($stop_limit, 2) . ".";
				break;
		}

		$this->sendTweet($body);
	}
		
	public function warning($tickername, $sellprice, $stop_limit){
	 	$body = "Just a warning:Ticker " . strtoupper($tickername) . " temporarily fell below its abandon price of $" . number_format($stop_limit, 2) . ".";
		$this->sendTweet($body);
	}
		
	public function buy($tickername, $sellprice, $buyzone_low, $buyzone_high){
	 	$body = strtoupper($tickername) . " just fell into its entry zone and is trading at $" . number_format($sellprice, 2) . ".";
		$this->sendTweet($body);
	}
	public function buy_breakout($tickername, $sellprice, $buyzone_low, $buyzone_high){
	 	$body = strtoupper($tickername) . " just rose to the indicated entry price and is trading at $" . number_format($sellprice, 2) . ".";
		$this->sendTweet($body);
	}
	public function sell_breakdown($tickername, $sellprice, $buyzone_low, $buyzone_high){
	 	$body = strtoupper($tickername) . " just fell below the indicated entry price and is trading at $" . number_format($sellprice, 2) . ".";
		$this->sendTweet($body);
	}

	
	////// REVERSALS
	public function sell1_reversal($tickername, $sellprice){
	 	$body = "REVERSAL:" . strtoupper($tickername) . " just passed Target 1 and is trading at $" . number_format($sellprice, 2) . ".";
		$this->sendTweet($body);
	}
		
	public function sell2_reversal($tickername, $sellprice){
	 	$body = "REVERSAL:" . strtoupper($tickername) . " just passed Target 2 and is trading at $" . number_format($sellprice, 2) . ".";
		$this->sendTweet($body);
	}
		
	public function sell3_reversal($tickername, $sellprice){
	 	$body = "REVERSAL:" . strtoupper($tickername) . " just passed Target 3 and is trading at $" . number_format($sellprice, 2) . ".";
		$this->sendTweet($body);
	}
		
	public function abandon_reversal($tickername, $sellprice, $stop_limit){
	 	$body = "REVERSAL:" . "ABANDON TICKER " . strtoupper($tickername) . ". It closed below its ABANDON price of $" . number_format($stop_limit, 2) . ".";
		$this->sendTweet($body);
	}
		
	public function warning_reversal($tickername, $sellprice, $stop_limit){
	 	$body = "REVERSAL:" . "Just a warning:Ticker " . strtoupper($tickername) . " temporarily fell below its abandon price of $" . number_format($stop_limit, 2) . ".";
		$this->sendTweet($body);
	}
	public function warning_breakout($tickername, $sellprice, $stop_limit){
	 	$body = "BREAKOUT:" . "Just a warning:Ticker " . strtoupper($tickername) . " temporarily fell below its abandon price of $" . number_format($stop_limit, 2) . ".";
		$this->sendTweet($body);
	}
	public function warning_breakdown($tickername, $sellprice, $stop_limit){
	 	$body = "BREAKDOWN:" . "Just a warning:Ticker " . strtoupper($tickername) . " temporarily rose above its abandon price of $" . number_format($stop_limit, 2) . ".";
		$this->sendTweet($body);
	}
		
	public function buy_reversal($tickername, $sellprice, $buyzone_low, $buyzone_high){
	 	$body = "REVERSAL:" . strtoupper($tickername) . " just hit the entry point and is trading at $" . number_format($sellprice, 2) . ".";
		$this->sendTweet($body);
	}
		
	public function shortBuy1($tickername, $tickerprice){
		$body = "SHORT::".strtoupper($tickername) . " passed T1 and is down to " . number_format($tickerprice, 2) . ", buy to cover.";
		$this->sendTweet($body);
	}
	public function shortBuy2($tickername, $tickerprice){
		$body = "SHORT::".strtoupper($tickername) . " passed T2 and is down to " . number_format($tickerprice, 2) . ", buy to cover.";
		$this->sendTweet($body);
	}
	public function shortBuy3($tickername, $tickerprice){
		$body = "SHORT::".strtoupper($tickername) . " passed T3 and is down to " . number_format($tickerprice, 2) . ", buy to cover.";
		$this->sendTweet($body);
	}
	public function shortZone($tickername, $tickerprice){
		$body = "SHORT::".strtoupper($tickername) . " is trading in the zone at " . number_format($tickerprice, 2) . ". Short sell this stock now.";
		$this->sendTweet($body);
	}
	
	public function shortAbandon($tickername, $sellprice, $stop_limit){
	 	$body = "SHORT::ABANDON TICKER " . strtoupper($tickername) . ". It closed below its ABANDON price of $" . number_format($stop_limit, 2) . ".";
		$this->sendTweet($body);
	}
		
	public function shortWarning($tickername, $sellprice, $stop_limit){
	 	$body = "SHORT::Just a warning:Ticker " . strtoupper($tickername) . " temporarily crossed above its abandon price of $" . number_format($stop_limit, 2) . ".";
		$this->sendTweet($body);
	}
	
	public function abandonAtClose($tickername, $sellprice, $stop_limit, $tradeType){
		$str1 = GetTradeTypeConstantNameSingular($tradeType);
	 	$body = $str1 . "::ABANDON " . strtoupper($tickername) . " NOW! New information has led us to recommend that you abandon the stock BEFORE the markets close.";
		$this->sendTweet($body);		
	}
	
	public function hitTarget($tradeType, $targetNum, $symbol, $last){
		switch ($tradeType){
			case LONG_TRADE:
			case REVERSAL_TRADE:
			case BREAKOUT_TRADE:
				$functionName = "sell" . $targetNum;
				$this->$functionName($symbol, $last, $tradeType);
				break;
			case SHORT_TRADE:
			case BREAKDOWN_TRADE:
				$functionName = "buy" . $targetNum;
				$this->$functionName($symbol, $last, $tradeType);
				break;
		}
	}
*/
}