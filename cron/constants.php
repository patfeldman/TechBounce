<?php

	const LONG_TRADE=0;
	const SHORT_TRADE=1;
	const REVERSAL_TRADE=2;
	const BREAKOUT_TRADE=3;
	const BREAKDOWN_TRADE=4;
	const PULLBACK_TRADE=5; // New long strategy
	const BACKDRAFT_TRADE=6; // new short strategy
	
	const NONE = 0;
	const BUY = 1;
	const SELL1 = 2;
	const SELL2 = 3;
	const SELL3 = 4; 
	const ABANDON = 5;
	const WARNING = 6;
	const ABANDON_AT_CLOSE = 7;
	const SELL0 = 8;
	const ABANDON_HARD_STOP = 9;

	// comms action, only used in email.class.php
	const COMMS_ABANDON_NOW = 9;
	const COMMS_MANUAL_ABANDON_AT_CLOSE = 10;
	
	const STOCH_PENDING = 0;
	const STOCH_STATE_BUY_WAIT = 1;
	const STOCH_STATE_BUY = 2;
	const STOCH_STATE_SELL1_WAIT = 3;
	const STOCH_STATE_SELL1 = 4;
	const STOCH_STATE_SELL2_WAIT = 5;
	const STOCH_STATE_SELL2 = 6;
	const STOCH_STATE_SELL3_WAIT = 7;
	const STOCH_STATE_SELL3 = 8;
	
	
	
	// HIGHLIGHT FIELDS ENUM
	const W_ROW_DELETE = 0;
	const W_ROW = 1;
	const W_SYMBOL=2;
	const W_LAST=3;
	const W_TODAY=4;
	const W_ENTRY_ZONE=5;
	const W_PERCENT_ZONE=6;
	const W_T1=7;
	
	const H_ROW = 8;
	const H_ZONED_ON=9;
	const H_SYMBOL=10;
	const H_TODAY=11;
	const H_LAST=12;
	const H_T1=13;
	const H_T2=14;
	const H_T3=15;
	const H_ABANDON=16;
	const H_ORIG=17;
	const H_RETURN=18;
	const H_LAST_ACTION=19;
	const H_T0=20;
	const H_HARDSTOP=21;
	
	function GetTradeTypeConstantName($tradeType){
		switch ($tradeType){
			case LONG_TRADE:
			case PULLBACK_TRADE:
				return "PULLBACKS";
			case SHORT_TRADE:
			case BACKDRAFT_TRADE:
				return "BACKDRAFTS";
			case REVERSAL_TRADE:
				return "REVERSALS";
			case BREAKOUT_TRADE:
				return "BREAKOUTS";
			case BREAKDOWN_TRADE:
				return "BREAKDOWNS";
		}
	}
	
	function GetTradeTypeConstantNameSingular($tradeType){
		switch ($tradeType){
			case LONG_TRADE:
			case PULLBACK_TRADE:
				return "PULLBACK";
			case SHORT_TRADE:
			case BACKDRAFT_TRADE:
				return "BACKDRAFT";
			case REVERSAL_TRADE:
				return "REVERSAL";
			case BREAKOUT_TRADE:
				return "BREAKOUT";
			case BREAKDOWN_TRADE:
				return "BREAKDOWN";
		}
	}
	
	function GetLastActionString($tradeType, $lastAction){
		switch ($tradeType){
			case LONG_TRADE:
			case REVERSAL_TRADE:
				switch ($lastAction){
					case BUY:
						return "BUY";
					case SELL1:
						return "SELL1";
					case SELL2:
						return "SELL2";
					case SELL3:
						return "SELL3";
					case ABANDON:
						return "ABANDON";
					case ABANDON_AT_CLOSE:
						return "ABANDON AT CLOSE";
					case ABANDON_HARD_STOP:
						return "HARD STOP";
					case WARNING:
						return "WARNING";
				}
				return "NONE";
			case PULLBACK_TRADE:
				switch ($lastAction){
					case BUY:
						return "BUY";
					case SELL1:
						return "SELL ALL";
					case ABANDON:
						return "ABANDON";
					case ABANDON_AT_CLOSE:
						return "ABANDON AT CLOSE";
					case ABANDON_HARD_STOP:
						return "HARD STOP";
					case WARNING:
						return "WARNING";
				}
				return "NONE";
			case BACKDRAFT_TRADE:
				switch ($lastAction){				
					case BUY:
						return "SELL SHORT";
					case SELL1:
						return "COVER ALL";
					case ABANDON:
						return "ABANDON";
					case ABANDON_AT_CLOSE:
						return "ABANDON AT CLOSE";
					case ABANDON_HARD_STOP:
						return "HARD STOP";
					case WARNING:
						return "WARNING";
				}
				return "NONE";
			case BREAKOUT_TRADE:
				switch ($lastAction){
					case SELL3:
						return "SELL HALF";
					case ABANDON:
						return "ABANDON";
					case ABANDON_AT_CLOSE:
						return "ABANDON AT CLOSE";
					case ABANDON_HARD_STOP:
						return "HARD STOP";
					case WARNING:
						return "WARNING";
					default:
						return "BUY";
						
				}
			case BREAKDOWN_TRADE:
				switch ($lastAction){				
					case SELL3:
						return "COVER HALF";
					case ABANDON:
						return "ABANDON";
					case ABANDON_AT_CLOSE:
						return "ABANDON AT CLOSE";
					case ABANDON_HARD_STOP:
						return "HARD STOP";
					case WARNING:
						return "WARNING";
					default:
						return "SELL SHORT";
				}
			case SHORT_TRADE:
				switch ($lastAction){				
					case BUY:
						return "SELL SHORT";
					case SELL1:
						return "COVER1";
					case SELL2:
						return "COVER2";
					case SELL3:
						return "COVER3";
					case ABANDON:
						return "ABANDON";
					case ABANDON_AT_CLOSE:
						return "ABANDON AT CLOSE";
					case ABANDON_HARD_STOP:
						return "HARD STOP";
					case WARNING:
						return "WARNING";
				}
				return "NONE";
		}		
	}
	
	function GetActionByTarget($targetNum){
		switch ($targetNum){
			case 0:
				return SELL0;
			case 1:
				return SELL1;
			case 2:
				return SELL2;
			case 3:
				return SELL3;
		}
	}

	function GetHighlightByTarget($targetNum){
		switch ($targetNum){
			case 0:
				return H_T0;
			case 1:
				return H_T1;
			case 2:
				return H_T2;
			case 3:
				return H_T3;
		}
	}
	
	function UsesAllTargets($tradeType){
		switch ($tradeType){
			case LONG_TRADE:
			case SHORT_TRADE:
			case REVERSAL_TRADE:
				return true;
			default: 
				return false;
		}
	}
	
	function GetTradeIndex ($tradeType){
		switch ($tradeType){
			case BREAKOUT_TRADE:
				return 1; 
			case LONG_TRADE:
			case PULLBACK_TRADE:
				return 2; 
			case BREAKDOWN_TRADE:
				return 3; 
			case SHORT_TRADE:
			case BACKDRAFT_TRADE:
				return 4; 
			case REVERSAL_TRADE:
				return -1; 
		}
		reutrn -1;
	}
	
	function IsAbandoned($action){
			return ($action == ABANDON_AT_CLOSE || $action == ABANDON || $action == ABANDON_HARD_STOP);
	}
	
	
	