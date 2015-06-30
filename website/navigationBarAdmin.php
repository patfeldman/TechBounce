<?php
$uri = $_SERVER['REQUEST_URI'];
?>
<div id="toppanel">
	<!-- The tab on top -->	
	<div class="tabNew" >
		<span class="login">
			<div class="left">	&nbsp;	</div>
			<div class="title item">
		 		<a href="#" id="n0" class="siteLink">
		 			BioBOUNCE
		 		</a> 
			</div>
			<div class="item menu">
				<span class="mainNav">
					<ul class="login">
						<li>
							<a href="members.php" class="navigator">
								Long Lists
							</a>
						</li>
						<li>
							<a href="shortlists.php" class="navigator">
								Short Lists
							</a>
						</li>
						<li>
							<a href="reversals.php" class="navigator">
								Reversal Lists
							</a>
						</li>
						<li>
						</li>
					</ul> 
					
				</span>
				<div>
					<div class="subNav">
						<ul class="login">
							<li>
								<a href="admin.php" class="navigatorAdmin <?php if (strstr($uri, "/admin.php")!== FALSE) echo "selected"; ?>">
									L-Admin
								</a>
							</li>
							<li>
								<a href="admin.short.php" class="navigatorAdmin <?php if (strstr($uri, "short")!== FALSE) echo "selected"; ?>">
									S-Admin
								</a>
							</li>
							<li>
								<a href="admin.reversals.php" class="navigatorAdmin <?php if (strstr($uri, "reversals")!== FALSE) echo "selected"; ?>">
									R-Admin
								</a>
							</li>
							<li>
								<a href="useradmin.php" class="navigatorAdmin <?php if (strstr($uri, "useradmin")!== FALSE) echo "selected"; ?>">
									UserInfo
								</a>
							</li>
							<li>
								<a href="referraladmin.php" class="navigatorAdmin <?php if (strstr($uri, "useradmin")!== FALSE) echo "selected"; ?>">
									Referrals
								</a>
							</li>
						</ul> 
					</div>
				</div>
			</div>
			<div class="account item">
				<div class="helloName">Hello <?php echo $username; ?></div>
				<div class="logoutDiv">
					<a href="accountinfo.php" class="logout">
						Settings
					</a>
<?php 
	if ($isAdmin ){ 
?>
					<span class="divider"></span>
					<a href="admin.php" class="logout">
						Admin
					</a>
<?php
						}
					?>
					<span class="divider"></span>
					<a class="logout" href="?lo">Log Out</a>
				</div>
			</div>
			<div class="right">	&nbsp;	</div>
		</span>
	</div>
</div>
