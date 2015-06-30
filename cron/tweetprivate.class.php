<?php
class tweetprivate{
	private $twitterConnection;
	private $twitterContent;
	public function __construct(){
		$this->twitterConnection = new TwitterOAuth(CONSUMER_KEY2, CONSUMER_SECRET2, OAUTH_TOKEN2, OAUTH_SECRET2);
		$this->twitterContent = $this->twitterConnection->get('account/verify_credentials');
	}
	
	public function sendTweet($body){
		//if (IS_TESTING) { echo "TESTING - NO TWEETS"; return ; }
		$postinfo = $this->twitterConnection->post('statuses/update', array('status' => $body));
	}

	public function stoch_state_buy_wait($tickername, $tickerprice, $stochaction){
		$body = strtoupper($tickername) . " just entered STOCH_STATE_BUY_WAIT. It is oversold(down passed 20), wait till breakout changes.";
		$this->sendTweet($body);
	}
	public function stoch_state_buy($tickername, $tickerprice, $stochaction){
		$body = strtoupper($tickername) . " just entered STOCH_STATE_BUY. Breakout shifted, buy now. (up passed 20)";
		$this->sendTweet($body);
	}
	public function stoch_state_sell_wait($tickername, $tickerprice, $stochaction){
		$body = strtoupper($tickername) . " just entered STOCH_STATE_SELL_WAIT. It is overbought(up passed 80), wait till breakout changes.";
		$this->sendTweet($body);
	}
	public function stoch_state_sell($tickername, $tickerprice, $stochaction){
		$body = strtoupper($tickername) . " just entered STOCH_STATE_SELL. Breakout shifted, SELL now. (down passed 80)";
		$this->sendTweet($body);
	}
	
	public function sendWatchlistUpdate(){
	 	$body = "Changes have been made to the watchlist. Check at www.biobounce.com";
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
	
	
}