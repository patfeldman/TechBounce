<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	
	$userNameError = "";
	$emailAddressError = "";
	session_start();
	if (isset($_SESSION['userid'])){
		header('Location: /lists.pullbacks.php');
	}
	if (isset($_POST['username'])){
		$user = new user();
		$user->set_variable('users_username', $_POST['username']);
		if ($user->load()){
			$email = $user->get_variable('users_email');
			$randPassword  = user::randomPassword();
			// send email
			$to = $email;
			$subject = "BioBounce. New Password.";
			
			$body = "Your password for Biobounce.com has been reset.\nYour temporary password is\n". $randPassword 
			."\n\nGo to http://www.biobounce.com to login. You will be asked to change your password upon login.\n\nIf you have any questions please feel free to email us:"
			."\nBioBounce@biobounce.com or Gonzo@biobounce.com"
			."\nKind Regards,"
			."\nGonzo & BioBounce";
			$from = 'From: biobounce@biobounce.com';
 			if (mail($to, $subject, $body, $from)) {
				$user->set_variable('users_temppassword', $randPassword);
				$user->set_variable('users_verified', '0');
				$user->update();
				
				$messageSentResponse = "A temporary password has been sent to your registered email address.";
				header('Location: /index.php?msg=' . urlencode($messageSentResponse));
			} else {
				$userNameError = "Sorry, you password could not be emailed at this time. Please try again later. " . $email;
			}
		} else {
			$userNameError  .= " Username does not exist.";
		}
	} else if (isset($_POST['email'])) {
	
		$user = new user();
		$user->set_variable('users_email', $_POST['email']);
		if ($user->load()){
			$username = $user->get_variable("users_username");
			// send email
			$to = $_POST['email'];
			$subject = "BioBounce. REQUEST FOR USERNAME.";
			$body = "You have requested a copy of your username.\nYou username as it appears on our files is : ". $username." \n\nIf you have any questions please feel free to email us:"
			."\nBioBounce@biobounce.com or Gonzo@biobounce.com"
			."\nKind Regards,"
			."\nGonzo & BioBounce";
			$from = 'From: biobounce@biobounce.com';
			if (mail($to, $subject, $body, $from)) {
				$messageSentResponse = "Your username has been sent to your email.";
				header('Location: /index.php?msg=' . urlencode($messageSentResponse));
				// SEND BACK TO THE INDEX PAGE WITH NEW MESSAGE
			} else {
				$messageSentResponse = "Email could not be sent at this time, please try again later.";
				header('Location: /index.php?msg=' . urlencode($messageSentResponse));
			}
		} else {
			$emailAddressError .= "Email address not registered.";
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

<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
<link href="css/biobounce.css" rel="stylesheet" type="text/css">
<link href="css/bioforgot.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Della+Respira' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Josefin+Slab' rel='stylesheet' type='text/css'>

<script type="text/javascript">
$(document).ready(function() {

}
</script>

</head>

<body>
<!-- Panel -->

<div class = "newwrap">
	<div class="section n0">
		<div id="maintitle">
			BioBOUNCE
		</div >
		<div class="white">
			<div class="leftchange" >
				<h1>FORGOT PASSWORD</h1><br/>
				<form class="clearfix" action="#" method="post">
					<label class="grey" for="pwd">Enter Username:&nbsp;</label>
					<input class="field" type="text" name="username" id="username" size="23" />
					<div class="<?php if (strlen($userNameError)==0){ echo 'gone' ;} ?>">			
						<img src="images/error.png" class="errorimg" />
						<span class="errorMessage">&nbsp;&nbsp;<?php echo $userNameError; ?></span>
					</div>				
	
					<div class="clear"></div>
					<input type="submit" name="sendPassword" value="Send New Password" class="bt_change" />
				</form>
			</div>
			<div class="rightchange" >
				<h1>FORGOT USERNAME</h1><br/>			
				<form class="clearfix" action="#" method="post">
					<label class="grey" for="pwd">Enter Email Address:&nbsp;</label>
					<input class="field" type="text" name="email" id="email" size="23" />
					<div class="<?php if (strlen($emailAddressError)==0){ echo 'gone' ;} ?>">			
						<img src="images/error.png" class="errorimg" />
						<span class="errorMessage">&nbsp;&nbsp;<?php echo $emailAddressError; ?></span>
					</div>				
					<div class="clear"></div>
					<input type="submit" name="sendUsername" value="Send Username" class="bt_forgot" />
				</form>
			</div>
		</div>
		<div id="homelink">
			<a href="index.php">Home</a>
		</div >

	</div>
</div>

</body>
</html>