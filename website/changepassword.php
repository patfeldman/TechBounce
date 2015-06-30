<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	
	$error = "";
	session_start();
	if (isset($_POST['prevpwd']) && isset($_SESSION['userid'])){
		$prevpwd = $_POST['prevpwd'];
		$pwd = $_POST['newpwd'];
		$copypwd = $_POST['newpwd2'];

	
		if ($pwd != $copypwd){
			$error = "New password is not the same in both fields.";
		} else if( strlen($pwd) < 8 ) {
			$error = "Password must be 8 characters or more.";
		} else if( strlen($pwd) > 20 ) {
			$error = "Password must be 20 characters or less.";
		}
		
		if (strlen($error)==0){
			$user = new user();
			$user->set_variable('users_id', $_SESSION['userid']);
			if ($user->load()){
				if ($prevpwd == $user->get_variable('users_temppassword')){
					// need to md5 this
					$username = $user->get_variable("users_username");
					$user->set_variable('users_password', (md5($pwd)));
					$user->set_variable('users_referralid', md5($username));
					$user->set_variable('users_verified', 1);
					$date = date('Y-m-d H:i:s');
					$year = intval(date("Y", strtotime($user->get_variable('users_creationdate'))));
					if ($year < 2013){
						$user->set_variable('users_creationdate', $date);
					}
					$user->set_variable('users_lastlogindate', $date);
					$user->update();
					$_SESSION['verified'] = 1;
					header('Location: /lists.breakouts.php');
				} else {
					$error = "Current Password is incorrect.";
				}
				
			} else {
				$error .= " User not found.";
			}
		}	
		
	} else {
		if (isset($_SESSION['userid'])){
			if (isset($_SESSION['verified']) && $_SESSION['verified'] == 1){
				header('Location: /lists.breakouts.php');
			}
		} else {
			//header('Location: /');
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
<link href='http://fonts.googleapis.com/css?family=Della+Respira' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Josefin+Slab' rel='stylesheet' type='text/css'>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<!-- Sliding effect -->
<script src="js/slide.js" type="text/javascript"></script>
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
		<div align="center" >
			<h1>CHANGE PASSWORD</h1><br/>			
			<form class="clearfix" action="#" method="post">
				<label class="grey" for="log">Current Password:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
				<input class="field" type="password" name="prevpwd" id="prevpwd" value="" size="23" /><br/><br/>
				<label class="grey" for="pwd">Enter New Password:&nbsp;&nbsp;&nbsp;&nbsp;</label>
				<input class="field" type="password" name="newpwd" id="newpwd" size="23" /><br/><br/>
				<label class="grey" for="pwd">Retype New Password:&nbsp;</label>
				<input class="field" type="password" name="newpwd2" id="newpwd2" size="23" /><br/><br/>
				<div class="loginErrorRow pwderror 
				<?php 		
					if (strlen($error)==0)
						echo " none"
				 ?>">			
					<img src="images/error.png" class="errorimg" />
					<span class="errorMessage">&nbsp;&nbsp;<?php echo $error; ?></span>
				</div>
				<div class="clear"></div>
				<input type="submit" name="submit" value="Update Password" class="bt_login" />
			</form>
			

		</div>
	</div>
</div>

</body>
</html>