<?php
class tradeTypeStrings{
	var $actionList; 
	var $entryWatchDesc;
	var $entryDateDesc;
	var $t1WatchDesc;
	var $t1Desc;
	var $t2Desc;
	var $t3Desc;
	var $targetDesc;
	var $abandonDesc;
	var $hardStopDesc;
	var $entryHoldingDesc;

	public function __construct($tradeType = LONG_TRADE){
		switch($tradeType){
			case LONG_TRADE:
			case REVERSAL_TRADE:
			case BREAKOUT_TRADE:
				$this->entryWatchDesc="The entry price specifies the ideal purchase price of this stock.";
				$this->t1WatchDesc="The First BioBounce Target. This is the first sell point should this stock drop past the entry price.";
				$this->entryDateDesc="The date that this stock fell below the entry price.";
				$this->t1Desc="The First BioBounce Target. Sell 25% of your holdings at this price.";
				$this->t2Desc ="The Second BioBounce Target. Sell 25% of your original holdings at this price.";
				$this->t3Desc ="The Third BioBounce Target. Sell 25% of your original holdings at this price.";
				$this->abandonDesc="The end of day price limit. Sell the next day if this stock CLOSES below this price.";
				$this->entryHoldingDesc="The price when this stock was purchased.";
				$this->hardStopDesc = "The hard stop price to sell all.";
				break;
			case SHORT_TRADE:
			case BREAKDOWN_TRADE:
				$this->entryWatchDesc="The short price specifies the ideal price to short this stock.";
				$this->t1WatchDesc="The First BioBounce Target. This is the first buy to cover point should this stock climb past the entry price.";
				$this->entryDateDesc="The date that this stock climbed above the entry price.";
				$this->t1Desc="The First BioBounce Target. Cover 33% of your holdings at this price.";
				$this->t2Desc ="The Second BioBounce Target. Cover 33% of your original holdings at this price.";
				$this->t3Desc ="The Third BioBounce Target.  Buy to cover the rest of your original holdings at this price.";
				$this->abandonDesc="The end of day price limit. Cover the next day if this stock CLOSES above this price.";
				$this->entryHoldingDesc="The price when this stock was sold short.";
				$this->hardStopDesc = "The hard stop price to cover all.";
				break;
			case PULLBACK_TRADE:
				$this->entryWatchDesc="The entry price specifies the ideal purchase price of this stock.";
				$this->t1WatchDesc="The BioBounce Target. This is the sell point should this stock drop past the entry price.";
				$this->entryDateDesc="The date that this stock fell below the entry price.";
				$this->t1Desc="The BioBounce Target. Sell 100% of your holdings at this price.";
				$this->t2Desc ="NOT USED IN THIS KIND OF TRADE!";
				$this->t3Desc ="NOT USED IN THIS KIND OF TRADE!";
				$this->abandonDesc="The end of day price limit. Sell the next day if this stock CLOSES below this price.";
				$this->entryHoldingDesc="The price when this stock was purchased.";
				$this->hardStopDesc = "The hard stop price to sell all.";
				break;
			case BACKDRAFT_TRADE:
				$this->entryWatchDesc="The short price specifies the ideal price to short this stock.";
				$this->t1WatchDesc="The BioBounce Target. This is the buy to cover point should this stock climb past the entry price.";
				$this->entryDateDesc="The date that this stock climbed above the entry price.";
				$this->t1Desc="The BioBounce Target. Cover 100% of your holdings at this price.";
				$this->t2Desc ="NOT USED IN THIS KIND OF TRADE!";
				$this->t3Desc ="NOT USED IN THIS KIND OF TRADE!";
				$this->abandonDesc="The end of day price limit. Cover the next day if this stock CLOSES above this price.";
				$this->entryHoldingDesc="The price when this stock was sold short.";
				$this->hardStopDesc = "The hard stop price to cover all.";
				break;
		}
		
		$functionName = strtolower(GetTradeTypeConstantName($tradeType)) . "GetStrings";
		$this->$functionName();
	}

	private function breakdownsGetStrings(){
		$this->t1WatchDesc="The BioBounce Target. This is the cover point should this stock climb past the entry price.";
		$this->t3Desc ="The BioBounce Target. Cover 50% of your original holdings at this price.";
		
		
		$this->actionList="
				<li>SELL SHORT = Sell stock short when it falls to the indicated entry price or lower. </li>
				<li>ABANDON    = Cover the next day, if stock CLOSES above abandon price.</li>
				<li>HARD STOP  = Cover immediately if stock trades above Hard Stop Price.</li>
				<li>ABANDON AT CLOSE= Cover before the end of the day regardless of price.</li>
				<li>COVER HALF = Cover 50% of your original holdings when the price reaches the target.</li>
				<li>WARNING    = If stock rises above abandon price intra-day a warning is issued.</li>
		";	
	}
	private function breakoutsGetStrings(){
		$this->t1WatchDesc="The BioBounce Target. This is the sell point should this stock drop past the entry price.";
		$this->t3Desc ="The BioBounce Target. Sell 50% of your original holdings at this price.";
		
		$this->actionList="
				<li>BUY     = Purchase stock when it rises to the indicated entry price or higher.</li>
				<li>ABANDON = Sell your remaining shares the next day, if stock CLOSES below abandon price.</li>
				<li>HARD STOP  = Sell immediately if stock trades below Hard Stop Price.</li>
				<li>ABANDON BY CLOSE = Sell the stock before the end of the day regardless of price.</li>
				<li>SELL HALF = Sell 50% of your holdings when the price reaches the target.</li>
				<li>WARNING = If stock falls below Abandon price intra-day a warning is issued.</li>
		";
	}
	
	private function backdraftsGetStrings(){
		$this->actionList="
			<li>SELL SHORT = Sell stock short when it rises to the indicated entry price or higher.</li>
			<li>ABANDON    = Cover the next day, if stock CLOSES above abandon price.</li>
			<li>ABANDON AT CLOSE= Cover before the end of the day regardless of price.</li>
			<li>COVER ALL  = Cover 100% of your holdings when the price reaches the target.</li>
			<li>WARNING    = If stock rises above abandon price intra-day a warning is issued.</li>
		";
	}
	
	private function pullbacksGetStrings(){
		$this->actionList="
				<li>BUY     = Purchase stock when it falls to the indicated entry price or lower.</li>
				<li>ABANDON = Sell the next day, if stock CLOSES below abandon price.</li>
				<li>ABANDON BY CLOSE = Sell the stock before the end of the day regardless of price.</li>
				<li>SELL ALL = Sell 100% of your holdings when the price reaches the target.</li>
				<li>WARNING = If stock falls below Abandon price intra-day a warning is issued.</li>
		";
	}
	private function reversalsGetStrings(){
		$this->actionList="
				<li>BUY     = Purchase stock when it hits the entry price indicated.</li>
				<li>ABANDON = Sell the next day, if stock CLOSES below abandon price.</li>
				<li>ABANDON BY CLOSE = Sell before the stock before the end of the day regardless of price.</li>
				<li>SELL1   = Sell 33% of your holdings when the price reaches target 1.</li>
				<li>SELL2   = Sell 33% of your holdings when the price reaches target 2.</li>
				<li>SELL3   = Sell remainder of your holdings when the price reaches target 3.</li>
				<li>WARNING = If stock falls below Abandon price intra-day a warning is issued.</li>
		";
			
		
	}
			
		
}