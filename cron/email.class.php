<?php
class email{
	const ADDRESSES_ALL_ACTIVE = 1; 
	const ADDRESSES_ALL_ACTIVE_TRIAL = 2; 
	const ADDRESSES_ALL_CHOSEN = 3; 
	const ADDRESSES_PROVIDED = 4; 
	const ADDRESSES_SMS_ONLY = 5; 
	const ADDRESSES_TEST_ONLY = 6; 
	private $templateLocation;
	private $action_id;
	private $email_addresses;
	private $text_addresses;
	private $trade_type;
	public function __construct($useAddresses=email::ADDRESSES_ALL_ACTIVE, $tradeType=PULLBACK_TRADE, $hId=-1){		
		switch ($useAddresses){
			case email::ADDRESSES_ALL_ACTIVE:
				$this->email_addresses = user::getAllActiveUserEmailAddresses();
				break;
			case email::ADDRESSES_ALL_ACTIVE_TRIAL:
				$this->email_addresses = user::getAllTrialUserEmailAddresses();
				break;
			case email::ADDRESSES_ALL_CHOSEN:
				$this->email_addresses = user::getAllEmailAddressSetForUpdates($tradeType, $hId);
				$this->text_addresses = user::getAllTextAddressSetForUpdates($tradeType, $hId);
				break;
			case email::ADDRESSES_SMS_ONLY:
				$this->text_addresses = user::getAllTextAddressSetForUpdates($tradeType, $hId);
				break;
			case email::ADDRESSES_TEST_ONLY:
				$this->text_addresses = user::getAllTextAddressSetForUpdates($tradeType, -10000);
				break;
			case email::ADDRESSES_PROVIDED:
				break;
		}
		$this->trade_type = $tradeType;
		$this->templateLocation = LOCATION . "php/email_templates/";
	}
	public function sendMail($subject, $body, $useFancy=FALSE, $bcc=''){
		//if (IS_TESTING) { echo "TESTING - NO EMAILS"; return ; }
		if (empty($this->email_addresses)) return ;
		// PREVENT ALL EMAILS FOR NOW
		//if ($this->trade_type == BREAKOUT_TRADE || $this->trade_type == BREAKDOWN_TRADE) return;
		//$to = "gonzo@biobounce.com";
		//$to = "patfeldman@gmail.com";
		$to="";
		if ($useFancy){
			$headers = "From: gonzo@biobounce.com" . "\r\n";
			$headers .= "Reply-To: gonzo@biobounce.com \r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		} else {
			$headers = 'From: gonzo@biobounce.com' . "\r\n";
			
		}
		$commaSep = '';
		if ($bcc == ''){
			// GET ALL EMAIL ADDRESSES
			$commaSep = implode(", ", $this->email_addresses);
		} else {
			$commaSep = implode(", ", $bcc);
		}
		$headers .= 'Bcc: ' . $commaSep ;
		if (mail($to, $subject, $body, $headers)) {
			echo("<p>Email successfully sent!</p>");
			echo("Sent Emails to: " . $headers);
			echo("</br>Subject:" . $subject);
			echo("</br>Body:" . $body);
		} else {
			echo("<p>Email delivery failed…</p>");
		}
	}

	public function sendTextMail($subject, $body){
		//if (IS_TESTING) { echo "TESTING - NO TEXTS"; return ; }
		if (empty($this->text_addresses)) return ;
		//$to = "gonzo@biobounce.com";
		//$to = "patfeldman@gmail.com";
		$to="";
		$headers = 'From: gonzo@biobounce.com' . "\r\n";
		$commaSep = '';
		$commaSep = implode(", ", $this->text_addresses);
		$headers .= 'Bcc: ' . $commaSep ;
		if (mail($to, $subject, $body, $headers)) {
			echo("<p>Email successfully sent!</p>");
			echo("Sent Emails to: " . $headers);
			echo("</br>Subject:" . $subject);
			echo("</br>Body:" . $body);
			
		} else {
			echo("<p>Email delivery failed…</p>");
		}
	}
	
	public function newEmail($tradeType, $action, $symbol, $actionPrice, $optionalMessage=""){
		$emailInfo = email::GetEmailText($tradeType, $action, $symbol, $actionPrice, $optionalMessage);
		if (strlen($emailInfo["body"]) > 0){
			$this->sendMail($emailInfo["subject"], $emailInfo["body"], true);
			$this->sendTextMail($emailInfo["subject"], $emailInfo["body"]);
		}	
	}

	public function sendTestMail(){
		$to="";
		$headers = 'From: gonzo@biobounce.com' . "\r\n";
		$headers .= 'Bcc: 4193927803@vtext.com, 4434536745@tmomail.net'  ;
		if (mail($to, "TESTING", "IGNORE, just testing", $headers)) {
			echo("<p>Email successfully sent!</p>");
			echo("Sent Emails to: " . $headers);
			echo("</br>Subject:" . $subject);
			echo("</br>Body:" . $body);
		} else {
			echo("<p>Email delivery failed…</p>");
		}
	}


	public function sendWatchlistUpdate(){
		$subject = "BioBounce - WATCHLIST UPDATED";
		$template = file_get_contents($this->templateLocation . 'update_email.php');
	 	$bodyText = "The BioBounce.com Watchlist and Holdings list are up to date for this evening.";
  		$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "Update";
	 	$bodyText = "The BioBounce.com Watchlist and Holdings list are up to date for this evening.";
		$this->sendTextMail($subject, $bodyText);		
	}


	public function expirationEnding(){
		// 2 days left
		$expiringUsers = user::getExpiringUsersEmailAddresses(2);
		$bodyText = file_get_contents($this->templateLocation . 'expiring_email.php');
		$subject = "BioBounce - Trial EXPIRES Soon!";
		$this->sendMail($subject, $bodyText, TRUE, $expiringUsers);
	}

	public function sendDuplicateAccountExpired($emailAddressArray){
		$bodyText = file_get_contents($this->templateLocation . 'expiring_user.php');
		$subject = "BioBounce - Duplicate Account!";
		$this->sendMail($subject, $bodyText, TRUE, $emailAddressArray);
	}
	
	public function endOfDay($bodyText){
		$template = file_get_contents($this->templateLocation . 'eod_email.php');
		$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$subject = "BioBounce Daily Summary";
		$this->sendMail($subject, $bodyText, TRUE);
	}
	
	
	static public function GetEmailText($tradeType, $action, $symbol, $pricePoint1=0, $optionalMessage="", $includeParentheticals=true){
		$pricePoint1 = round($pricePoint1, 2);
		$symbol = strtoupper($symbol);
		$retVal["body"] = "";
		$retVal["subject"] = "";
		switch ($tradeType){
			case LONG_TRADE:
			case PULLBACK_TRADE:
				switch ($action){
					case NONE:				
						break;
					case BUY:
						$retVal["subject"] = "PULLBACK - ENTRY";
						$retVal["body"] = "Buy Long " . $symbol ." at entry price of ".$pricePoint1;
						$retVal["body"] = ($includeParentheticals) ? "(Pullback) " . $retVal["body"] : $retVal["body"];
						break;
					case SELL1:
						if ($tradeType == PULLBACK_TRADE){
							$retVal["subject"] = "PULLBACK - TARGET";
							$retVal["body"] = $symbol ." hit target of ".$pricePoint1.". Sell ALL of your position.";
							$retVal["body"] = ($includeParentheticals) ? "(Pullback) " . $retVal["body"] : $retVal["body"];
						}else {
							$retVal["subject"] = "PULLBACK - T1";
							$retVal["body"] = $symbol ." hit 1st target of ".$pricePoint1.". Sell 25% of your position.";
							$retVal["body"] = ($includeParentheticals) ? "(Pullback) " . $retVal["body"] : $retVal["body"];
						}
						break;
					case SELL2:
						$retVal["subject"] = "PULLBACK - T2";
						$retVal["body"] = $symbol ." hit 2nd target of ".$pricePoint1.". Sell 25% of your ORIGINAL position.";
						$retVal["body"] = ($includeParentheticals) ? "(Pullback) " . $retVal["body"] : $retVal["body"];
						break;
					case SELL3:
						$retVal["subject"] = "PULLBACK - T3";
						$retVal["body"] = $symbol ." hit 3rd target of ".$pricePoint1.". Sell 25% of your ORIGINAL position.";
						$retVal["body"] = ($includeParentheticals) ? "(Pullback) " . $retVal["body"] : $retVal["body"];
						break;
					case ABANDON:
						$retVal["subject"] = "PULLBACK - ABANDON";
						$retVal["body"] = $symbol ." closed below its ABANDON price. Sell your remaining position tomorrow.";
						$retVal["body"] = ($includeParentheticals) ? "(Pullback:ABANDON) " . $retVal["body"] : $retVal["body"];
						break;
					case COMMS_ABANDON_NOW:
					case ABANDON_AT_CLOSE:
					case COMMS_MANUAL_ABANDON_AT_CLOSE:
						$retVal["subject"] = "PULLBACK - ABANDON";
						$retVal["body"] = "ABANDON " . $symbol ." by today's market close. ";
						$retVal["body"] = ($includeParentheticals) ? "(Pullback:ABANDON) " . $retVal["body"] : $retVal["body"];
						break;
					case WARNING:
						$retVal["subject"] = "PULLBACK - WARNING";
						$retVal["body"] = $symbol ." temporarily fell below its ABANDON price.";
						$retVal["body"] = ($includeParentheticals) ? "(Pullback:WARNING) " . $retVal["body"] : $retVal["body"];
						break;
				}
				break;
			case SHORT_TRADE:
			case BACKDRAFT_TRADE:
				switch ($action){
					case NONE:				
						break;
					case BUY:
						$retVal["subject"] = "BACKDRAFT - ENTRY";
						$retVal["body"] = "Sell short " . $symbol ." at entry price of ".$pricePoint1;
						$retVal["body"] = ($includeParentheticals) ? "(Backdraft) " . $retVal["body"] : $retVal["body"];
						break;
					case SELL1:
						if ($tradeType == BACKDRAFT_TRADE){
							$retVal["subject"] = "BACKDRAFT - TARGET";
							$retVal["body"] = $symbol ." hit target of ".$pricePoint1.". Buy to cover ALL of your position.";
							$retVal["body"] = ($includeParentheticals) ? "(Backdraft) " . $retVal["body"] : $retVal["body"];
						}else{
							$retVal["subject"] = "BACKDRAFT - T1";
							$retVal["body"] = $symbol ." hit 1st target of ".$pricePoint1.". Buy to cover 33% of your position.";
							$retVal["body"] = ($includeParentheticals) ? "(Backdraft) " . $retVal["body"] : $retVal["body"];
						}
						break;
					case SELL2:
						$retVal["subject"] = "BACKDRAFT - T2";
						$retVal["body"] = $symbol ." hit 2nd target of ".$pricePoint1.". Buy to cover 33% of your ORIGINAL position.";
						$retVal["body"] = ($includeParentheticals) ? "(Backdraft) " . $retVal["body"] : $retVal["body"];
						break;
					case SELL3:
						$retVal["subject"] = "BACKDRAFT - T3";
						$retVal["body"] = $symbol ." hit 3rd target of ".$pricePoint1.". Buy to cover 33% of your ORIGINAL position and close the trade.";
						$retVal["body"] = ($includeParentheticals) ? "(Backdraft) " . $retVal["body"] : $retVal["body"];
						break;
					case ABANDON:
						$retVal["subject"] = "BACKDRAFT - ABANDON";
						$retVal["body"] = $symbol ." closed above its ABANDON price. Buy to cover your remaining position tomorrow.";
						$retVal["body"] = ($includeParentheticals) ? "(Backdraft:ABANDON) " . $retVal["body"] : $retVal["body"];
						break;
					case COMMS_ABANDON_NOW:
					case ABANDON_AT_CLOSE:
					case COMMS_MANUAL_ABANDON_AT_CLOSE:
					case ABANDON_HARD_STOP:							
						$retVal["subject"] = "BACKDRAFT - ABANDON";
						$retVal["body"] = "ABANDON " . $symbol ." by today's market close. ";
						$retVal["body"] = ($includeParentheticals) ? "(Backdraft:ABANDON) " . $retVal["body"] : $retVal["body"];
						break;
					case WARNING:
						$retVal["subject"] = "BACKDRAFT - WARNING";
						$retVal["body"] = $symbol ." temporarily rose above its ABANDON price.";
						$retVal["body"] = ($includeParentheticals) ? "(Backdraft:WARNING) " . $retVal["body"] : $retVal["body"];
						break;
				}
				break;
			case BREAKOUT_TRADE:
				switch ($action){
					case NONE:				
					case SELL1:
					case SELL2:
						break;
					case BUY:
						$retVal["subject"] = "BREAKOUT - ENTRY";
						$retVal["body"] = "Buy Long " . $symbol ." at entry price of ".$pricePoint1;
						$retVal["body"] = ($includeParentheticals) ? "(Breakout) " . $retVal["body"] : $retVal["body"];
						break;
					case SELL3:
						$retVal["subject"] = "BREAKOUT - TARGET";
						$retVal["body"] = $symbol ." hit its target of ".$pricePoint1.". Sell 50% of your position.";
						$retVal["body"] = ($includeParentheticals) ? "(Breakout) " . $retVal["body"] : $retVal["body"];
						break;
					case ABANDON:
						$retVal["subject"] = "BREAKOUT - ABANDON";
						$retVal["body"] = $symbol ." closed below its ABANDON price. Sell your remaining position tomorrow.";
						$retVal["body"] = ($includeParentheticals) ? "(Breakout:ABANDON) " . $retVal["body"] : $retVal["body"];
						break;
					case COMMS_ABANDON_NOW:
					case ABANDON_HARD_STOP:						
						$retVal["subject"] = "BREAKOUT - HARD STOP";
						$retVal["body"] = $symbol ." hit its HARD STOP price of " . $pricePoint1 . ". Sell your remaining position immediately and close the trade.";
						$retVal["body"] = ($includeParentheticals) ? "(Breakout:HARD STOP) " . $retVal["body"] : $retVal["body"];
						break;
					case ABANDON_AT_CLOSE:
					case COMMS_MANUAL_ABANDON_AT_CLOSE:
						$retVal["subject"] = "BREAKOUT - ABANDON";
						$retVal["body"] = "ABANDON " . $symbol ." by today's market close. ";
						$retVal["body"] = ($includeParentheticals) ? "(Breakout:ABANDON) " . $retVal["body"] : $retVal["body"];
						break;
					case WARNING:
						$retVal["subject"] = "BREAKOUT - WARNING";
						$retVal["body"] = $symbol ." temporarily fell below its ABANDON price.";
						$retVal["body"] = ($includeParentheticals) ? "(Breakout:WARNING) " . $retVal["body"] : $retVal["body"];
						break;
				}
				break;
			case BREAKDOWN_TRADE:
				switch ($action){
					case NONE:				
						break;
					case BUY:
						$retVal["subject"] = "BREAKDOWN - ENTRY";
						$retVal["body"] = "Sell short " . $symbol ." at entry price of ".$pricePoint1;
						$retVal["body"] = ($includeParentheticals) ? "(Breakdown) " . $retVal["body"] : $retVal["body"];
						break;
					case SELL3:
						$retVal["subject"] = "BREAKDOWN - TARGET";
						$retVal["body"] = $symbol ." hit target of ".$pricePoint1.". Buy to cover ALL of your position.";
						$retVal["body"] = ($includeParentheticals) ? "(Breakdown) " . $retVal["body"] : $retVal["body"];
						break;
					case ABANDON:
						$retVal["subject"] = "BREAKDOWN - ABANDON";
						$retVal["body"] = $symbol ." closed above its ABANDON price. Buy to cover ALL remaining position tomorrow.";
						$retVal["body"] = ($includeParentheticals) ? "(Breakdown:ABANDON) " . $retVal["body"] : $retVal["body"];
						break;
					case COMMS_ABANDON_NOW:
					case ABANDON_HARD_STOP:						
						$retVal["subject"] = "BREAKDOWN - HARD STOP";
						$retVal["body"] = $symbol ." hit its HARD STOP price of " . $pricePoint1 . ". Buy to cover remaining position immediately and close the trade.";
						$retVal["body"] = ($includeParentheticals) ? "(Breakdown:HARD STOP) " . $retVal["body"] : $retVal["body"];
						break;
					case ABANDON_AT_CLOSE:
					case COMMS_MANUAL_ABANDON_AT_CLOSE:
						$retVal["subject"] = "BREAKDOWN - ABANDON";
						$retVal["body"] = "ABANDON " . $symbol ." by today's market close. ";
						$retVal["body"] = ($includeParentheticals) ? "(Breakdown:ABANDON) " . $retVal["body"] : $retVal["body"];
						break;
					case WARNING:
						$retVal["subject"] = "BREAKDOWN - WARNING";
						$retVal["body"] = $symbol ." temporarily rose above its ABANDON price.";
						$retVal["body"] = ($includeParentheticals) ? "(Breakdown:WARNING) " . $retVal["body"] : $retVal["body"];
						break;
				}
				break;
			case REVERSAL_TRADE:
				break;
		}
		$retVal["body"] .= " " . $optionalMessage;
		return $retVal;
	}
	

/*







	public function sell1($tickername, $sellprice, $tradeType=PULLBACK_TRADE){
		$subject = "BioBounce - TARGET ONE";
	 	$bodyText = strtoupper($tickername) . " just passed $" . number_format($sellprice, 2) . " which is the first target. Now would be a good time to sell 25% of your current position.";
		if ($tradeType==REVERSAL_TRADE) {
			$subject = "BioBounce - REVERSAL TARGET ONE";
		 	$bodyText = strtoupper($tickername) . " just passed $" . number_format($sellprice, 2) . " which is the first target. Now would be a good time to sell 33% of your current position.";
		}
		$template = file_get_contents($this->templateLocation . 'update_email.php');
	 	$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "T1";
	 	$bodyText = strtoupper($tickername) . " just passed Target 1, current trading at $" . number_format($sellprice, 2);
		if ($tradeType==REVERSAL_TRADE) {
			$subject = "REVERSAL T1";
		 	$bodyText = strtoupper($tickername) . " just passed Target 1, current trading at $" . number_format($sellprice, 2);
		}
		$this->sendTextMail($subject, $bodyText);
	}
		
	public function sell2($tickername, $sellprice, $tradeType=PULLBACK_TRADE){
		$subject = "BioBounce - TARGET TWO";
	 	$bodyText = strtoupper($tickername) . " just passed $" . number_format($sellprice, 2) . " which is the second target. Now would be a good time to sell the next 25% of your original holdings.";
		if ($tradeType==REVERSAL_TRADE) {
			$subject = "BioBounce - REVERSAL - TARGET TWO";
		 	$bodyText = strtoupper($tickername) . " just passed $" . number_format($sellprice, 2) . " which is the second target. Now would be a good time to sell the next 33% of your original holdings.";
		}
		$template = file_get_contents($this->templateLocation . 'update_email.php');
	 	$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "T2";
	 	$bodyText = strtoupper($tickername) . " just passed Target 2, current trading at $" . number_format($sellprice, 2);
		if ($tradeType==REVERSAL_TRADE) {
			$subject = "REVERSAL T2";
		 	$bodyText = strtoupper($tickername) . " just passed Target 2, current trading at $" . number_format($sellprice, 2);
		}
		$this->sendTextMail($subject, $bodyText);
		
	}
		
	public function sell3($tickername, $sellprice, $tradeType=PULLBACK_TRADE){
		$template = file_get_contents($this->templateLocation . 'update_email.php');
		$subject = "BioBounce - FINAL TARGET";
	 	$bodyText = strtoupper($tickername) . " just passed $" . number_format($sellprice, 2) . " which is the final target. Now would be a good time to sell the next 25% of your original holdings.";
		if ($tradeType==REVERSAL_TRADE) {
			$subject = "BioBounce - REVERSAL - FINAL TARGET";
		 	$bodyText = strtoupper($tickername) . " just passed $" . number_format($sellprice, 2) . " which is the final target. Now would be a good time to sell your remaining holdings.";
		}
	 	$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "T3";
	 	$bodyText = strtoupper($tickername) . " just passed Target 3, current trading at $" . number_format($sellprice, 2);
		if ($tradeType==REVERSAL_TRADE) {
			$subject = "REVERSAL FINAL";
		 	$bodyText = strtoupper($tickername) . " just passed Target 3, current trading at $" . number_format($sellprice, 2);
		}
		$this->sendTextMail($subject, $bodyText);
	}

	public function breakoutSell($tickername, $sellprice, $tradeType=PULLBACK_TRADE){
		$template = file_get_contents($this->templateLocation . 'update_email.php');
		$subject = "BioBounce - BREAKOUT - HIT TARGET";
	 	$bodyText = strtoupper($tickername) . " just passed $" . number_format($sellprice, 2) . " which is the target. Now would be a good time to sell all of your holdings.";
	 	$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "BREAKOUT::TARGET";
	 	$bodyText = strtoupper($tickername) . " just passed the target, current trading at $" . number_format($sellprice, 2);
		$this->sendTextMail($subject, $bodyText);
	}
		
	public function abandon($tickername, $sellprice, $stop_limit, $tradeType=PULLBACK_TRADE){
		switch($tradeType){
			case LONG_TRADE: 
			case PULLBACK_TRADE: 
				return $this->longAbandon($tickername, $sellprice, $stop_limit);
			case SHORT_TRADE:
			case BACKDRAFT_TRADE:
				return $this->shortAbandon($tickername, $sellprice, $stop_limit);
			case REVERSAL_TRADE:
				return $this->revesalAbandon($tickername, $sellprice, $stop_limit);
			case BREAKOUT_TRADE:
				return $this->breakoutAbandon($tickername, $sellprice, $stop_limit);
			case BREAKDOWN_TRADE:
				return $this->breakdownAbandon($tickername, $sellprice, $stop_limit);			
		}
	}
	public function longAbandon($tickername, $sellprice, $stop_limit){
		$subject = "BioBounce - ABANDON";
	 	$body = strtoupper($tickername) . " just fell to $" . number_format($sellprice, 2) . " which is below our advised stop limit ($". number_format($stop_limit, 2) ."). We are uncertain as to when or if this stock will recover so we recommended relinquishing all holdings and we will be removing this stock from the watchlist and holdings shortly\n Regards, \n Biobounce.com";
		$this->sendMail($subject, $body);
		
		$subject = "ABANDON";
	 	$bodyText = strtoupper($tickername) . " closed at $" . number_format($sellprice, 2);
		$this->sendTextMail($subject, $bodyText);
	}

	public function reversalAbandon($tickername, $sellprice, $stop_limit){
		$subject = "BioBounce - Reversal ABANDON";
	 	$body = strtoupper($tickername) . " just fell to $" . number_format($sellprice, 2) . " which is below our advised stop limit ($". number_format($stop_limit, 2) ."). We are uncertain as to when or if this stock will recover so we recommended relinquishing all holdings and we will be removing this stock from the reversal watchlist and holdings shortly\n Regards, \n Biobounce.com";
		$this->sendMail($subject, $body);
		
		$subject = "Reversal ABANDON";
		$bodyText = strtoupper($tickername) . " closed at $" . number_format($sellprice, 2);
		$this->sendTextMail($subject, $bodyText);
	}
	
	public function breakoutAbandon($tickername, $sellprice, $stop_limit){
		$subject = "BioBounce - Breakout ABANDON";
	 	$body = strtoupper($tickername) . " just fell to $" . number_format($sellprice, 2) . " which is below our advised stop limit ($". number_format($stop_limit, 2) ."). We are uncertain as to when or if this stock will recover so we recommended relinquishing all holdings and we will be removing this stock from the breakout watchlist and holdings shortly\n Regards, \n Biobounce.com";
		$this->sendMail($subject, $body);
		
		$subject = "Breakout ABANDON";
		$bodyText = strtoupper($tickername) . " closed at $" . number_format($sellprice, 2);
		$this->sendTextMail($subject, $bodyText);
	}

	public function breakdownAbandon($tickername, $sellprice, $stop_limit){
		$subject = "BioBounce - Breakdown ABANDON";
	 	$body = strtoupper($tickername) . " just climbed above $" . number_format($sellprice, 2) . " which is above our advised stop limit ($". number_format($stop_limit, 2) ."). We are uncertain as to when or if this stock will recover so we recommended relinquishing all holdings and we will be removing this stock from the watchlist and holdings shortly\n Regards, \n Biobounce.com";
		$this->sendMail($subject, $body);
		
		$subject = "Breakdown ABANDON";
	 	$bodyText = strtoupper($tickername) . " closed at $" . number_format($sellprice, 2);
		$this->sendTextMail($subject, $bodyText);
		
	}

		
	public function buy($tickername, $sellprice, $buyzone_low, $buyzone_high, $tradeType=PULLBACK_TRADE){
		$template = file_get_contents($this->templateLocation . 'update_email.php');

		$subject = "BioBounce - BUY ZONE ENTERED";
		$bodyText = strtoupper($tickername) . " just hit $" . number_format($sellprice, 2) . " which means it is now in the buy zone ($" . number_format($buyzone_low, 2) . " - $" . number_format($buyzone_high, 2) ."). Now would be a good time to invest in this stock. It has been added to BioBOUNCE holdings list.";
		if ($tradeType==REVERSAL_TRADE) {
			$subject = "BioBounce - REVERSAL ENTRY HIT";
			$bodyText = strtoupper($tickername) . " just hit $" . number_format($sellprice, 2) . " which means it has hit the entry point of $" . number_format($buyzone_low, 2) . ". Now would be a good time to invest in this stock. It has been added to BioBOUNCE Reversal holdings list.";
		}
		
	 	$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "BUY";
		$bodyText = strtoupper($tickername) . " dropped into the zone and is trading at $" . number_format($sellprice, 2);
		if ($tradeType==REVERSAL_TRADE) {
			$subject = "REVERSAL:BUY";
			$bodyText = strtoupper($tickername) . " dropped to the entry point of $" . number_format($buyzone_low, 2);
		}
		$this->sendTextMail($subject, $bodyText);		
	}

	public function buy_breakout($tickername, $sellprice, $buyzone_low, $buyzone_high, $tradeType=PULLBACK_TRADE){
		$template = file_get_contents($this->templateLocation . 'update_email.php');

		$subject = "BioBounce - BREAKOUT ENTRY HIT";
		$bodyText = strtoupper($tickername) . " just hit $" . number_format($sellprice, 2) . " which means it has hit the entry point of $" . number_format($buyzone_low, 2) . ". Now would be a good time to invest in this stock. It has been added to BioBOUNCE Breakout holdings list.";
	 	$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "BREAKOUT:BUY";
		$bodyText = strtoupper($tickername) . " rose to the entry point of $" . number_format($buyzone_low, 2);
		$this->sendTextMail($subject, $bodyText);		
	}

	public function sell_breakdown($tickername, $sellprice, $buyzone_low, $buyzone_high, $tradeType=PULLBACK_TRADE){
		$template = file_get_contents($this->templateLocation . 'update_email.php');

		$subject = "BioBounce - BREAKDOWN ENTRY HIT";
		$bodyText = strtoupper($tickername) . " just hit $" . number_format($sellprice, 2) . " which means it has hit the entry point of $" . number_format($buyzone_low, 2) . ". Now would be a good time to short sell this stock. It has been added to BioBOUNCE Breakdown holdings list.";
	 	$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "BREAKDOWN:SHORT SELL";
		$bodyText = strtoupper($tickername) . " fell to the entry point of $" . number_format($buyzone_low, 2);
		$this->sendTextMail($subject, $bodyText);		
	}


	public function testStochBuyNow($tickerSymbol){
		$subject = "BioBounce - TEST - STOCHASTIC BOTTOM";
		$body = "Buy " . $tickerSymbol . " now. It was being oversold and is currently getting back to normal.";
		$this->sendMail($subject, $bodyText);
	}


	/// SHORT EMAILS
	public function shortBuy1($tickername, $sellprice){
		$subject = "BioBounce - TARGET ONE";
	 	$bodyText = strtoupper($tickername) . " just dropped passed $" . number_format($sellprice, 2) . " which is the first target. Now would be a good time to buy to cover 25% of your current position.";
		$template = file_get_contents($this->templateLocation . 'update_email.php');
	 	$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "SHORT:T1";
	 	$bodyText = strtoupper($tickername) . " just dropped passed Target 1, current trading at $" . number_format($sellprice, 2);
		$this->sendTextMail($subject, $bodyText);
	}
		
	public function shortBuy2($tickername, $sellprice){
		$subject = "BioBounce - TARGET TWO";
	 	$bodyText = strtoupper($tickername) . " just dropped passed $" . number_format($sellprice, 2) . " which is the second target. Now would be a good time to buy to cover the next 25% of your original holdings.";
		$template = file_get_contents($this->templateLocation . 'update_email.php');
	 	$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "SHORT:T2";
	 	$bodyText = strtoupper($tickername) . " just dropped passed Target 2, current trading at $" . number_format($sellprice, 2);
		$this->sendTextMail($subject, $bodyText);
		
	}
		
	public function shortBuy3($tickername, $sellprice){
		$subject = "BioBounce - FINAL TARGET";
	 	$bodyText = strtoupper($tickername) . " just passed $" . number_format($sellprice, 2) . " which is the final target. Now would be a good time to buy to cover the next 25% of your original holdings.";
		$template = file_get_contents($this->templateLocation . 'update_email.php');
	 	$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "SHORT:T3";
	 	$bodyText = strtoupper($tickername) . " just dropped passed Target 3, current trading at $" . number_format($sellprice, 2);
		$this->sendTextMail($subject, $bodyText);
	}

	public function breakdownBuy($tickername, $sellprice){
		$subject = "BioBounce - BREAKDOWN - HIT TARGET";
	 	$bodyText = strtoupper($tickername) . " just passed $" . number_format($sellprice, 2) . " which is the target. Now would be a good time to buy to cover all of your holdings.";
		$template = file_get_contents($this->templateLocation . 'update_email.php');
	 	$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "BREAKDOWN:TARGET";
	 	$bodyText = strtoupper($tickername) . " just dropped passed the target, current trading at $" . number_format($sellprice, 2);
		$this->sendTextMail($subject, $bodyText);
	}
		
	public function shortAbandon($tickername, $sellprice, $stop_limit){
		$subject = "BioBounce - ABANDON";
	 	$body = strtoupper($tickername) . " just climbed above $" . number_format($sellprice, 2) . " which is above our advised stop limit ($". number_format($stop_limit, 2) ."). We are uncertain as to when or if this stock will recover so we recommended relinquishing all holdings and we will be removing this stock from the watchlist and holdings shortly\n Regards, \n Biobounce.com";
		$this->sendMail($subject, $body);
		
		$subject = "SHORT:ABANDON";
	 	$bodyText = strtoupper($tickername) . " closed at $" . number_format($sellprice, 2);
		$this->sendTextMail($subject, $bodyText);
		
	}
		
	public function shortZone($tickername, $sellprice, $buyzone_low, $buyzone_high){
		$subject = "BioBounce - SHORT SELL ZONE ENTERED";
		$template = file_get_contents($this->templateLocation . 'update_email.php');
	 	$bodyText = strtoupper($tickername) . " just hit $" . number_format($sellprice, 2) . " which means it is now in the short sell zone ($" . number_format($buyzone_low, 2) . " - $" . number_format($buyzone_high, 2) ."). Now would be a good time to short sell this stock. It has been added to BioBOUNCE holdings list.";
		$bodyText = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
		$this->sendMail($subject, $bodyText, TRUE);

		$subject = "SHORT SELL";
	 	$bodyText = strtoupper($tickername) . " climbed into the zone and is trading at $" . number_format($sellprice, 2);
		$this->sendTextMail($subject, $bodyText);		
	}
	
	
	public function abandonAtCloseEmail($tickername, $sellprice, $stop_limit, $tradeType=PULLBACK_TRADE){
		$str1 = GetTradeTypeConstantNameSingular($tradeType);
		
		$subject = "BioBounce - ABANDON TODAY";
		$body = "Some information about ". strtoupper($tickername) . " from the " . $str1 ." holdings list has been released and we recommend that you abandon this stock BEFORE the close of the markets today. \n Regards, \n Biobounce.com";
		$this->sendMail($subject, $body);
		
		$subject = $str1 . ":ABANDON TODAY::";
	 	$bodyText = "Abandon ". strtoupper($tickername) . " BEFORE market close today.";
		$this->sendTextMail($subject, $bodyText);
		
	}

	public function warningEmail($tickername, $sellprice, $stop_limit, $DEPRECATED = PULLBACK_TRADE){ // 
		$str1 = GetTradeTypeConstantNameSingular($this->trade_type);
		$subjectEmail = "BioBounce - Warning";
		$subjectText = "";
		switch ($this->trade_type){
			case LONG_TRADE:
			case PULLBACK_TRADE:
			case REVERSAL_TRADE:
			case BREAKOUT_TRADE:
				$bodyEmail = "Just a warning:Ticker " . strtoupper($tickername) . " from the " . $str1 . " List temporarily fell below its abandon price of $" . number_format($stop_limit, 2) . ".";
				$subjectText = $str1 . ":Warning::";
	 			$bodyText = strtoupper($tickername) . " temporarily fell below its abandon price.";
				break;
			case BREAKDOWN_TRADE:
			case SHORT_TRADE:
			case BACKDRAFT_TRADE:
				$bodyEmail = "Just a warning:Ticker " . strtoupper($tickername) . " from the " . $str1 . " List temporarily climbed above its abandon price of $" . number_format($stop_limit, 2) . ".";
				$subjectText = $str1 . ":Warning::";
	 			$bodyText = strtoupper($tickername) . " temporarily climbed above its abandon price.";
				break;
		}
		

		$this->sendMail($subjectEmail, $bodyEmail);
		$this->sendTextMail($subjectText, $bodyText);
		
	}
	
	public function hitTarget($tradeType, $targetNum, $symbol, $last){
		switch ($tradeType){
			case LONG_TRADE:
			case PULLBACK_TRADE:
			case REVERSAL_TRADE:
				$functionName = "sell" . $targetNum;
				$this->$functionName($symbol, $last, $tradeType);
				break;
			case SHORT_TRADE:
			case BACKDRAFT_TRADE:
				$functionName = "shortBuy" . $targetNum;
				$this->$functionName($symbol, $last);
				break;
			case BREAKOUT_TRADE:
				if ($targetNum < 3) return;
				$this->breakoutSell($symbol, $last);
				break;
			case BREAKDOWN_TRADE:
				if ($targetNum < 3) return;
				$this->breakdownBuy($symbol, $last);
				break;
		}
	}
	
	*/
}