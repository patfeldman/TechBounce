<div id='cssmenu'>
	<div id="littleMenu" class="indexMenu">
		<div class="wrapper">
			<div class="title item">
		 		<a href="#" id="n0" class="siteLink">
		 			BioBounce
		 		</a> 
			</div>
			<ul>
				<li>
			   		<a class="leftBar" href='login.php?id=register'>Register</a>
			   	</li>
				<li>
			   		<a href='login.php?id=login'>Log In</a>
			   	</li>
			</ul>			

			<div id="menu-button">
				<img class="menubutton openMenu" src="/images/mobile-menu-icon.png" />
			</div>
		</div>
		<div id="sideMenu">
			<ul>
				<li class='sideMenuOption0 bottomBreak'>
			   		<div class="leftBar closeMenu">MENU</div>
			   	</li>
				<li class='sideMenuOption'>
			   		<a class="leftBar navigator2" id="n1" href='#'>What</a>
			   	</li>
				<li class='sideMenuOption'>
			   		<a id="n2" class="navigator2" href='#'>Who</a>
			   	</li>
				<li class='sideMenuOption'>
			   		<a id="n3" class="navigator2" href='#'>Why</a>
			   	</li>
				<li class='sideMenuOption'>
			   		<a id="n4" class="navigator2" href='#'>How</a>
			   	</li>
			</ul>			

		</div>
	</div>
	<div id="bigMenu" class="indexMenu">
		<div id="panel">
			<div class="content clearfix">
				<div class="left">
					<h1>Welcome to BioBOUNCE</h1>
					<h2>A stock market analyzer of volatile BioTech stocks. </h2>		
					<p class="grey">BioBounce has a proven track record for correctly predicting the time to purchase a selected biotech stock.</p>
					<p class="grey">Register for a new account now and get a 14 day free trial.</p>
				</div>
				<div class="left">
					<!-- Login Form -->
					<form class="clearfix" action="#" method="post">
						<h1>Member Login</h1>
						<label class="grey" for="log">Username:</label>
						<input class="field" type="text" name="log" id="log" value="<?php echo $previousUserName; ?>" size="23" />
						<label class="grey" for="pwd">Password:</label>
						<input class="field" type="password" name="pwd" id="pwd" size="23" />
		            	<!--<label><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> &nbsp;Remember me</label> -->
	        			<div class="clear"></div>
						<input type="submit" name="submit" value="Login" class="bt_login" />
						<a class="lost-pwd" href="forgot.php">Forgot password or username?</a>
					</form>
				</div>
				<div class="left right">			
					<!-- Register Form -->
					<form action="#" method="post">
						<h1>Not a member yet? Sign Up!</h1>				
						<label class="grey" for="signup">Username:</label>
						<input class="field" type="text" name="signup" id="signup" value="<?php echo $previousSignupName; ?>" size="23" />
						<label class="grey" for="email">Email:</label>
						<input class="field" type="text" name="email" id="email" value="<?php echo $previousEmailAddress; ?>" size="23" />
						<label>A password will be e-mailed to you.</label>
						<input type="submit" name="submit" value="Register" class="bt_register" />
					</form>
				</div>
			</div>
			
			<div class="content loginErrorRow <?php if (!$errorsExist){ echo 'hidden' ;} ?>" >
				
				<div class="left" style="visibility:hidden">
					<span class="errorMessage" > Invalid UserName</span>
				</div>
				<div class="left <?php if (!$errors2Exist){ echo 'hidden' ;} ?>" >
					<img src="images/error.png" class="errorimg" />
					<span class="errorMessage">&nbsp;&nbsp;<?php echo $errorString2; ?></span>
				</div>
				<div class="left right <?php if (!$errors1Exist){ echo 'hidden' ;} ?>">			
					<img src="images/error.png" class="errorimg" />
					<span class="errorMessage">&nbsp;&nbsp;<?php echo $errorString1; ?></span>
				</div>
				
			</div>
		</div> <!-- /login -->	

		<div class="wrapper">
			<div class="title item">
		 		<a href="#" id="n0" class="siteLink navigator2">
		 			BioBounce
		 		</a> 
			</div>
			<ul>
				<li>
			   		<a class="leftBar navigator2" id="n1" href='#'>What</a>
			   	</li>
				<li>
			   		<a class="navigator2" id="n2" href='#'>Who</a>
			   	</li>
				<li>
			   		<a id="n3" class="navigator2" href='#'>Why</a>
			   	</li>
				<li>
			   		<a id="n4" class="navigator2" href='#'>How</a>
			   	</li>
				<li id="toggle">
					<a id="open" class="open" href="#">Log In | Register</a>
					<a id="close" style="display: none;" class="close" href="#">Close Panel</a>			
				</li> 
			</ul>			

		</div>
	</div>
</div>

<div id="toppanel" class="none">
	<div id="panel">
		<div class="content clearfix">
			<div class="left">
				<h1>Welcome to BioBOUNCE</h1>
				<h2>A stock market analyzer of volatile BioTech stocks. </h2>		
				<p class="grey">BioBounce has a proven track record for correctly predicting the time to purchase a selected biotech stock.</p>
				<p class="grey">Register for a new account now and get a 21 day free trial.</p>
			</div>
			<div class="left">
				<!-- Login Form -->
				<form class="clearfix" action="#" method="post">
					<h1>Member Login</h1>
					<label class="grey" for="log">Username:</label>
					<input class="field" type="text" name="log" id="log" value="<?php echo $previousUserName; ?>" size="23" />
					<label class="grey" for="pwd">Password:</label>
					<input class="field" type="password" name="pwd" id="pwd" size="23" />
	            	<!--<label><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> &nbsp;Remember me</label> -->
        			<div class="clear"></div>
					<input type="submit" name="submit" value="Login" class="bt_login" />
					<a class="lost-pwd" href="forgot.php">Forgot password or username?</a>
				</form>
			</div>
			<div class="left right">			
				<!-- Register Form -->
				<form action="#" method="post">
					<h1>Not a member yet? Sign Up!</h1>				
					<label class="grey" for="signup">Username:</label>
					<input class="field" type="text" name="signup" id="signup" value="<?php echo $previousSignupName; ?>" size="23" />
					<label class="grey" for="email">Email:</label>
					<input class="field" type="text" name="email" id="email" value="<?php echo $previousEmailAddress; ?>" size="23" />
					<label>A password will be e-mailed to you.</label>
					<input type="submit" name="submit" value="Register" class="bt_register" />
				</form>
			</div>
		</div>
		
		<div class="content loginErrorRow <?php if (!$errorsExist){ echo 'hidden' ;} ?>" >
			
			<div class="left" style="visibility:hidden">
				<span class="errorMessage" > Invalid UserName</span>
			</div>
			<div class="left <?php if (!$errors2Exist){ echo 'hidden' ;} ?>" >
				<img src="images/error.png" class="errorimg" />
				<span class="errorMessage">&nbsp;&nbsp;<?php echo $errorString2; ?></span>
			</div>
			<div class="left right <?php if (!$errors1Exist){ echo 'hidden' ;} ?>">			
				<img src="images/error.png" class="errorimg" />
				<span class="errorMessage">&nbsp;&nbsp;<?php echo $errorString1; ?></span>
			</div>
			
		</div>
	</div> <!-- /login -->	

	<!-- The tab on top -->	
	<div class="tab" id="tabslide">
		<ul class="login">
			<li class="left">&nbsp;</li>
			<li>
		 		<a href="#" id="n0" class="siteLink">
		 			BioBOUNCE
		 		</a> 
			</li>
			<li>
				<a href="#" id="n1" class="navigator">
					What&nbsp;
				</a>
			</li>
			<li>
				<a href="#" id="n2" class="navigator">
					Who&nbsp;
				</a>
			</li>
			<li>
				<a href="#" id="n3" class="navigator">
					Why
				</a>
			</li>
			<li>
				<a href="#" id="n4" class="navigator">
					How
				</a>
			</li>
			<li class="sep">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
			<li id="toggle">
				<a id="open" class="open" href="#">Log In | Register</a>
				<a id="close" style="display: none;" class="close" href="#">Close Panel</a>			
			</li> 
			<li class="right">&nbsp;</li>
		</ul> 
	</div> <!-- / top -->
</div>
