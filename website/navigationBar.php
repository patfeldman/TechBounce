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
							<?php if (strpos($uri, "members") !== FALSE){ ?>
								<a class="navigator selected">
							<?php }else { ?>
								<a href="members.php" class="navigator">
							<?php } ?>
								Pullbacks
							</a>
						</li>
						<li>
							<?php if (strstr($uri, "reversals")!== FALSE) { ?>
								<a class="navigator selected">
							<?php }else { ?>
								<a href="reversals.php" class="navigator">
							<?php } ?>
								Reversals
							</a>
						</li>
						<li>
							<?php if (strstr($uri, "short")!== FALSE){ ?>
								<a class="navigator selected">
							<?php }else { ?>
								<a href="shortlists.php" class="navigator">
							<?php } ?>
								Shorts
							</a>
						</li>
						<li>
						</li>
					</ul> 
					
				</span>
				<div>
					<div class="subNav">
						<ul class="login">
<?php if (strstr($uri, "accountinfo")!== FALSE) { ?>

		<?php if (USE_REFERRALS || $isAdmin){ ?>
							<li>
								<a href="#" id="n1" class="navigator2">
									Referrals
								</a>
							</li>
		<?php } ?>
							<li>
								<a href="#" id="n2" class="navigator2">
									Settings
								</a>
							</li>
							<li>
								<a href="#" id="n3" class="navigator2">
									Subscriptions
								</a>
							</li>

<?php } else { ?>
							<li>
								<a href="#" id="n1" class="navigator2">
									Watchlist
								</a>
							</li>
							<li>
								<a href="#" id="n2" class="navigator2">
									Holdings
								</a>
							</li>
							<li>
								<a href="#" id="n3" class="navigator2">
									Previous
								</a>
							</li>

							<li>
								<a href="#" id="n4" class="navigator2">
									Actions
								</a>
							</li>
<?php } ?>
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

<?php 
	if ((USE_REFERRALS || $isAdmin) && (strstr($uri, "accountinfo")== FALSE)){ 
?>
<div class = "newwrap">
	<div class="section n0" >
		<div id="title1">NEW REFERRAL REWARDS!</div >
		<div class="desc trialMessage">
			<a class="referralLink" href="accountinfo.php">Head to settings to learn more!</a>
		</div>
	</div>
</div>
<?php 
	}
?>
