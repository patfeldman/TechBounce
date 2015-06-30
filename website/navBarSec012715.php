<?php
$uri = $_SERVER['REQUEST_URI'];
?>

<?php if (strpos($uri, "history") !== FALSE){ ?>
	<div id="submenu" class="history" >
<?php }else if (strpos($uri, "reversal") !== FALSE){ ?>
	<div id="submenu" class="reversals" >
<?php }else if (strpos($uri, "short") !== FALSE){ ?>
	<div id="submenu" class="shorts" >
<?php }else if (strpos($uri, "breakout") !== FALSE){ ?>
	<div id="submenu" class="breakouts" >
<?php }else if (strpos($uri, "breakdown") !== FALSE){ ?>
	<div id="submenu" class="breakdowns" >
<?php }else{ ?>
	<div id="submenu" class="pullbacks" >
<?php } ?>

	<div class="subNav">
		<ul class="subList">
			<li >
				<a href="#" id="n1" class="leftBar navigator2">
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
		</ul> 
	</div>
</div>

<?php 
	if (FALSE && (USE_REFERRALS || $isAdmin) && (strstr($uri, "accountinfo")== FALSE)){ 
?>
<div class = "newwrap">
	<div class="section n0" >
		<div class="title1">New Referral Rewards!</div >
		<div class="desc trialMessage">
			<a class="referralLink" href="accountinfo.php">Head to settings to learn more!</a>
		</div>
	</div>
</div>
<?php 
	}
?>
