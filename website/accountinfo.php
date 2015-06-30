<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	$login = new login();

	$user = new user();
	$uid = $_SESSION['userid'];
	$user->set_variable('users_id', $uid);
	if ($user->load()){
		$username = $user->get_variable('users_username');
		$referralId = $user->get_variable('users_referralid');
		$email= $user->get_variable('users_email');
		$userCreationDate = $user->get_variable('users_creationdate');
	}

	$messageSentResponse = "";
	$to = "";
	$bodyText = "If you are interested in becoming a more profitable trader, or are just learning how to trade, I highly recommend BioBounce.com.  I&#39;ve been trading this system and have increased my profits substantially.

The system is easy to use and has easily paid for itself within a few trades.

The service is $49.99/month moving forward.  If you click the link below, you can sign up for a 14 day free trial to test out the site.  If you join, you will get $35 off the 1st month charge.  I will also get a referral fee!

Thanks and I hope you check it out!
";
	if (isset($_POST['email-body'])){
		$from = $_POST['email-from'];
		$to = $_POST['email-to'];
		$bodyText = $_POST['email-body'];

		if (strlen($bodyText) == 0){
			$messageSentResponse = "Your email cannot be sent:<br>INVALID MESSAGE BODY";
		} else if (strlen($to) == 0){
			$messageSentResponse = "Your email cannot be sent:<br>INVALID RECIPIENT LIST";
		} else {
			if (strlen($from) == 0) $from = $email;
			$bValid = true;
			$invalidList = "";
			foreach(explode(',', $to) AS $sEmailAddress){
				$sEmailAddress = trim($sEmailAddress);
				if (!filter_var($sEmailAddress, FILTER_VALIDATE_EMAIL)){
					$invalidList .= $sEmailAddress . ",";
					$bValid = false;
				}
			}
			if (!$bValid){
				$messageSentResponse = "Your email cannot be sent:<br>INVALID EMAIL ADDRESSES - " . $invalidList;
			} else {
				$subject = "BioBounce - REFERRAL";
				$link = "www.biobounce.com/?referralCode=". $referralId ;
				$template = file_get_contents('php/email_templates/referral_email.php');
			 	$body = str_replace('[[DYNAMIC_DATA1]]', $bodyText, $template);
			 	$body = str_replace('[[DYNAMIC_LINK]]', $link, $body);
			 	$body = str_replace('[[DYNAMIC_USERNAME]]', "<br>" . $username . " (". $email.")", $body);
				
				$headers = "From: gonzo@biobounce.com\r\n";
				$headers .= "Reply-To: gonzo@biobounce.com\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				if (mail($to, $subject, $body, $headers)) {
					$messageSentResponse = "Your emails have been sent. ";
					$to = "";
				} else {
					$messageSentResponse = "Your email cannot be sent:<br>CONTACT CUSTOMER SUPPORT";
				}
			}
		}
	}
	
?>


<!DOCTYPE html>
<html>
<head>
<title>BioBounce.com</title>

<meta name = "keywords" content = "biotech, stock, market, swing trading, stock trading" />
<meta name = "description" content = "" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, minimum-scale=1 user-scalable=no">
<!--
-->
<meta http-equiv="refresh" content="300" > 
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/navBar.js" type="text/javascript"></script>

<link href="css/biobounce.css" rel="stylesheet" type="text/css">
<link href="css/biomembers.css" rel="stylesheet" type="text/css">
<link href="css/biomembersnew.css" rel="stylesheet" type="text/css">

<link href="css/RESPONSIVE.css" rel="stylesheet" type="text/css">
<link href="css/responsive_pullbacks.css" rel="stylesheet" type="text/css">
<link href="css/jquery.qtip.min.css" rel="stylesheet" type="text/css">
<link href="css/uniform.aristo.min.css" rel="stylesheet" type="text/css">

<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
<script type="text/javascript">
$(document).ready(function() {

<?php
	if (strlen($messageSentResponse)>0){
?>
		setTimeout(function() {  
			$('.messagebox').fadeOut('slow');  
		}, 4000); // <-- time in milliseconds  
<?php 
	}	
?>

});	


</script>

</head>

<body id="accountPage">
<!-- Panel -->
<?php include 'navBarMain012715.php'; ?>
<?php include 'navBar3.php'; ?>

<?php 
	if (strlen($messageSentResponse)>0){
		echo '<div class="messagebox">' . $messageSentResponse . '</div>';  
	}
?>
<div class = "newwrap">
	<div class="section n0 <?php echo $login->trialTimeClass; ?>" >
		<div id="trialMessageTop"></div >
		<div class="desc trialMessage">
			<?php echo $login->trialMessage; ?> 
		</div>
	</div>
</div>

<?php
	$gettingEmailUpdates = $login->user->get_variable("users_send_email_updates");
	$gettingTextUpdates = $login->user->get_variable("users_send_text_updates");
	$gettingShortEmailUpdates = $login->user->get_variable("users_send_short_email_updates");
	$gettingShortTextUpdates = $login->user->get_variable("users_send_short_text_updates");
	$gettingReversalEmailUpdates = $login->user->get_variable("users_send_reversal_email_updates");
	$gettingReversalTextUpdates = $login->user->get_variable("users_send_reversal_text_updates");
	$textNumberStr = $login->user->get_variable("users_text_email_address");
	$textNumber = substr($textNumberStr, 0, 10);
	$textCarrierId = user::convertCarrierAddressToID(substr($textNumberStr, 10));
?>

<?php if ($login->isAdmin || USE_REFERRALS){ ?>
<div id="actions" class = "newwrap topSection">
	<div class="section n1">
		<div class="title1">
			REFERRALS
		</div >
		<div class="desc">
			<div>
Referrals are an integral part to any online service and we here at BioBounce are ready to recognize your role.
This is the start of a program to offer benefits to you for spreading the news of biobounce. 
				<div class="referralLeft">
                    <div class="referralTitle">Referral Rewards<br>Existing Members</div>
					<div class="referralItem">
						<div class="moneyHighlight" >$5.00 </div> 
						<div class="description">
							for any new users that sign up for a 14 Day Trial Period
						</div>
					</div>
					<div class="referralItem">
						<div class="moneyHighlight" >$35.00 </div> 
						<div class="description">
							for any new users that become BioBounce.com Subscribers
						</div>
					</div>
					<div class="clearer"></div>
                    <div class="referralTitle">New Members</div>
					<div class="referralItem2">
						<div class="moneyHighlight" >$35.00 </div> 
						<div class="description">
							off of the initial subscriber fee if using referral link.
						</div>
					</div>
					<div class="clearer"></div>
					<div class="note">
						* Maximum of $35.00 for per month. Payments will be refunded to you through paypal after the monthly payment has been made. 
					</div>
				</div>
				<div class="referralRight">
					<form class="referral_button" action="accountinfo.php" method="post" target="_top">
						<div class="referralEmailSection" name="Email Message">
			                <div id="email">
			                    <div class="referralTitle">Send Referral Email</div>
			                    <input class="malleable" id="email-to" name="email-to" placeholder="To : (Input comma-separated email addresses)" type="text" value="<?php echo $to; ?>">
			                    <input class="malleable" id="email-from"  name="email-from" placeholder="From : <?php echo $email; ?>" type="text" value="">
			                    <textarea class="referralMessage" id="email-body" name="email-body" placeholder="">
			                    	<?php echo $bodyText; ?>
								</textarea>
								<div class="btnWrapper">	
				                    <button id="email-send" class="btn" type="submit">Send Email</button>
				                </div>
			                </div>
			            </div>
				    </form>
					<div class="note">
						* Or send an email with the folling link included to receive the rewards<br><a href="www.biobounce.com/?referralCode=<?php echo $referralId; ?>">www.biobounce.com/?referralCode=<?php echo $referralId; ?></a> 
					</div>
				</div>

				<div class="clearer"></div>
				
				<div class="referrals">
                    <div class="referralTitle">Your Referrals</div>
					<div class="referralInfo">
						<div class="referralData referralHeader referralUserName"> User Name
						</div>
						<div class="referralData referralHeader referralUserType"> User Status
						</div>
						<div class="referralData referralHeader referralPay"> Value
						</div>
						<div class="referralData referralHeader referralPay"> Referral Payment
						</div>
					</div>
					<?php
						$referrals = new referral();
						$referrals->set_variable("referral_referred_by_userid", $login->userId);
						while($referrals->loadNext()){  
							$nuid = $referrals->get_variable("referral_referred_userid");
							$date = $referrals->get_variable("referral_date");
							$isPaid = $referrals->get_variable("referral_paid");
							$expireInfo = user::getUserExpirationDate($nuid);
							switch ($expireInfo['type']){
								case user::EXP_TYPE_MANUAL:
								case user::EXP_TYPE_PAID:
									$exptype = "Subscriber";
									$payVal = 35;
									break;
								case user::EXP_TYPE_TRIAL:
								default:
									$exptype = "Trial User";
									$payVal = 5;
									break;
							}
														
							$newUser = new user();
							$newUser->set_variable("users_id", $nuid);
							if ($newUser->load()){
								$newUserName = $newUser->get_variable("users_username");
								?>
									<div class="referralInfo">
										<div class="referralData referralUserName">
											<?php echo $newUserName; ?>
										</div>
										<div class="referralData referralUserType">
											<?php echo $exptype; ?>
										</div>
										<div class="referralData referralPay">
											<?php echo "$". $payVal . ".00"; ?>
										</div>
										<div class="referralData referralPay">
											<?php echo ($isPaid) ? "PAID" : "COMING SOON" ; ?>
										</div>
									</div>
								
								<?php
							}
						}
					?>
				</div>
				<div class="clearer"></div>
			</div>
		</div>
	</div>
</div>


<?php } ?>
<div id="settings" class = "newwrap">
	<div class="section n2 <?php echo $login->showClass; ?>">
		<div class="title1">
			SETTINGS
		</div >
		<div id="settingsInfo" class="desc">
			<div class="titleBoxes">
				<div class="shortWrap">
					<div class='longBox'>
						Breakouts/Pullbacks
					</div>
					<div class='shortBox'>
						Backdrafts
					</div>
				</div>
			</div>
			<div class="squaredOne">
				<div class="hardWidth">
					<div class='longBox'>
						<input type="checkbox" value="None" class="personalHolding" name="emailCheckBox" id="emailCheckBox" <?php if ($gettingEmailUpdates) echo "checked"; ?>/>
					</div>
					<div class='shortBox'>
						<input type="checkbox" value="None" class="personalHolding" name="shortEmailCheckBox" id="shortEmailCheckBox" <?php if ($gettingShortEmailUpdates) echo "checked"; ?>/>
					</div>
					<label for="squaredOne"> SUBSCRIBE TO EMAIL UPDATES<sup>*</sup></label>
				</div>
			</div>
			<div class="squaredOne">
				<div class="hardWidth">
					<div class='longBox'>
						<input type="checkbox" value="None" class="personalHolding" name="textCheckBox" id="textCheckBox" <?php if ($gettingTextUpdates) echo "checked"; ?>/>
					</div>
					<div class='shortBox '>
						<input type="checkbox" value="None" class="personalHolding" name="shortTextCheckBox" id="shortTextCheckBox" <?php if ($gettingShortTextUpdates) echo "checked"; ?>/>
					</div>
					<label for="squaredOne"> SUBSCRIBE TO TEXT MESSAGE UPDATES<sup>*</sup></label>
				</div>
			</div>
			<div id="phoneInfo" class="squaredOne none">
				<div class="hardWidth phoneInputRow">
					<span>Enter text information:</span>
					<input type="tel" id="telephoneNumber" name="telephoneNumber" class="telephone" value="<?php echo $textNumber;?>"></input>
					<select id="textNetwork" class="networkChoice">
						<option value="0" <?php if ($textCarrierId==0) echo "selected"; ?>>VERIZON</option>
						<option value="1" <?php if ($textCarrierId==1) echo "selected"; ?>>AT&T</option>
						<option value="2" <?php if ($textCarrierId==2) echo "selected"; ?>>SPRINT</option>
						<option value="3" <?php if ($textCarrierId==3) echo "selected"; ?>>T-MOBILE</option>
					</select>
				</div>
			</div>
			<div class="squaredOne btnRow">
				<a id="updateBtn" class="btn updateOff">Update Settings</a>
			</div>
			<div id="hiddenUpdate" class="hidden">
				<div id="savedInformation"></div>
			</div>
			
			<div class="notes">
				<div> * Email and Text updates will be sent for any watchlist item that falls into the zone, or any holdings item that hits a target.</div>
			</div>
		</div>
	</div>
</div>

<div id="subs" class = "newwrap last">
	<div class="section n3">
		<div class="title1">
			SUBSCRIPTIONS
		</div >
		<div class="desc">
<?php 
if ($login->useSubscriptionText){
?>
			<div class="subscribeInfo">
				<div class="subscribebold">$49.99 A MONTH</div>
				<div class="subscribemin"></div>
			</div>
			
			<form class="subscribe_button" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="2C7LYEVBBZJKY">
				<input type="hidden" name="custom" value="<?php echo $_SESSION['userid']; ?>">
				<input type="hidden" name="currency_code" value="USD">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
			<ul class="detailslist">
				<li>This subscription will give you monthly access to our Long Watch and Holdings lists and you may cancel at any time. 
					You will also be able to receive emails that provide a detailed summary of the daily activity and notifications on any actionable change in the data. 
					In addition you will be given access to our private twitter feed to keep you up to date on the latest news. </li>
			</ul>
<?php 
}else{
?>
			<div class="subscribeInfo">
				<div>Thank you for your subscription.</div>
				<div>Your next payment will be made on or around <?php echo $login->nextPaymentDate; ?>.</div>
				<A HREF="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=3SDW9BZTJQE6Y">
					<IMG SRC="https://www.paypalobjects.com/en_US/i/btn/btn_unsubscribe_SM.gif" BORDER="0">
				</A>
				</br>
				<div class="none">Follow us on our private twitter account</div>
				<a href="https://twitter.com/BioBounceAlerts" class="none twitter-follow-button" data-show-count="false" data-size="large">Follow @BioBounceAlerts</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
			</div>
<?php 
}
?>
		</div>
	</div>
</div>



<div class="disclaimer">
	<div class="disclaimer_inner">
		<div class="disclaimer_text">
AlgoSniffer, LLC is the Publisher of BioBounce.com and @BioBounceAlerts.  AlgoSniffer, LLC is not a Registered Investment Advisor.
</br></br>
BioBounce.com and @BioBounceAlerts are for informational purposes only and AlgoSniffer, LLC accepts no responsibility for any monetary losses incurred from use of the information provided.
</br></br>
Biotechnology is a highly volatile sector, and presents higher than average risk.  As a subscriber of our service, you are responsible for your own due diligence regarding any and all tickers we provide on our watchlist.
</br></br>
General Information on the Regulation of Investment Advisers</br>
Division of Investment Management</br>
Exclusions From the Definition</br>

Publishers of bona fide newspapers, news magazines, and business or financial publications of general and regular circulation. Under a decision of the United States Supreme Court, to enable a publisher to qualify for this exclusion, a publication must satisfy three elements: (1) the publication must offer only impersonal advice, i.e., advice not tailored to the individual needs of a specific client, group of clients, or portfolio; (2) the publication must be "bona fide," containing disinterested commentary and analysis rather than promotional material disseminated by someone touting particular securities, advertised lists of stocks "sure to go up," or information distributed as an incident to personalized investment services; and (3) the publication must be of general and regular circulation rather than issued from time to time in response to episodic market activity or events affecting the securities industry. See Lowe v. Securities and Exchange Commission, 472 U.S. 181 (1985).
</br>
</br>
source: http://www.sec.gov/divisions/investment/iaregulation/memoia.htm
		</div>
	</div>
</div>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script> 
<script type="text/javascript" src="js/jquery.qtip.min.js"></script> 
<script type="text/javascript" src="js/jquery.uniform.min.js"></script> 
<script src="js/pageInit.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.mask.min.js"></script> 
<script type="text/javascript" src="js/settings.js"></script> 


</body>
</html>