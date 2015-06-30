<?php
	require_once('./php/homePageBase.php');
?>


<!DOCTYPE html>
<html>
<head>
<title>BioBounce.com</title>

<meta name = "keywords" content = "biotech, stock, market, swing trading, stock trading" />
<meta name = "description" content = "" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, minimum-scale=1 user-scalable=no">
<link rel="shortcut icon" href="images/arrow.ico?v2">

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/navBar.js" type="text/javascript"></script>
<script src="js/simpleLogin.js" type="text/javascript"></script>

<link href="css/biobounce.css" rel="stylesheet" type="text/css">
<link href="css/biomembers.css" rel="stylesheet" type="text/css">
<link href="css/RESPONSIVE.css" rel="stylesheet" type="text/css">

<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
</head>
<body id="simpleLogin">
<!-- Panel -->
<div id="toppanel">
	<div id="panel">
		<div class="content clearfix">
			<div class="left alignCenter">
				<h1>Welcome to BioBOUNCE</h1>
				<h2>A stock market analyzer of volatile BioTech stocks. </h2>		
				<p class="grey">BioBounce has a proven track record for correctly predicting the time to purchase a selected biotech stock.</p>
				<p class="grey">Register for a new account now and get a 21 day free trial.</p>
			</div>
			<div id="LoginSection" class="left <?php echo ($useLogin) ? "" : "none"; ?>">
				<!-- Login Form -->
				<form class="clearfix border" action="#" method="post">
					<h1>Member Login</h1>
					<div class="alignCenter loginErrorRow left <?php if (!$errors2Exist){ echo 'none' ;} ?>" >
						<img src="images/error.png" class="errorimg" />
						<span class="errorMessage">&nbsp;&nbsp;<?php echo $errorString2; ?></span>
					</div>
					<label class="grey" for="log">Username:</label>
					<input class="field" type="text" name="log" id="log" value="<?php echo $previousUserName; ?>" size="23" />
        			<div class="clear"></div>
					<label class="grey" for="pwd">Password:</label>
					<input class="field" type="password" name="pwd" id="pwd" size="23" />
	            	<!--<label><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> &nbsp;Remember me</label> -->
        			<div class="clear"></div>
					<a class="lost-pwd" href="forgot.php">Forgot password or username?</a>
        			<div class="buttonRow">
						<input type="submit" name="submit" value="Login" class="bt_login" />
        			</div>
				</form>
			</div>
			<div id="RegisterSection" class="alignCenter left right <?php echo ($useRegister) ? "" : "none"; ?>">			
				<!-- Register Form -->
				<form class="border" action="#" method="post">
					<h1>Not a member yet? Sign Up!</h1>	
					<div class="alignCenter loginErrorRow left right <?php if (!$errors1Exist){ echo 'hidden' ;} ?>">			
						<img src="images/error.png" class="errorimg" />
						<span class="errorMessage">&nbsp;&nbsp;<?php echo $errorString1; ?></span>
					</div>
					<label class="grey" for="signup">Username:</label>
					<input class="field" type="text" name="signup" id="signup" value="<?php echo $previousSignupName; ?>" size="23" />
        			<div class="clear"></div>
					<label class="grey" for="email">Email:</label>
					<input class="field" type="text" name="email" id="email" value="<?php echo $previousEmailAddress; ?>" size="23" />
					<label>A password will be e-mailed to you.</label>
        			<div class="clear"></div>
        			<div class="buttonRow">
						<input type="submit" name="submit" value="Register" class="bt_register" />
					</div>
        			<div class="clear"></div>
				</form>
			</div>
		</div>		
		<div id="ButtonRow" class="<?php echo ($useLogin || $useRegister) ? "none" : ""; ?>">
			<button class="bt_register" id="RegisterOn">Register</button>
			<button class="bt_login" id="LoginOn">Login</button>
			<div class="clearer"></div>
		</div>
		<div class="clearer"></div>
		<div id="ButtonRow2">
			<a href="./">
				<button class="bt_login" id="closeClick">Close</button>
			</a>
			<div class="clearer"></div>
		</div>
	</div> <!-- /login -->	

</div>
</body>
</html>