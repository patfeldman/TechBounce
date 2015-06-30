<?php
$uri = $_SERVER['REQUEST_URI'];
?>
<div id='cssmenu'>
	<div id="littleMenu">
		<div class="wrapper">
			<div class="title item">
		 		<a href="#" id="n0" class="siteLink">
		 			BioBounce
		 		</a> 
			</div>
			<?php if (strpos($uri, "breakout") !== FALSE){ ?>
				<div class='active breakouts'>
					BREAKOUTS
				</div>
			<?php } else if (strpos($uri, "pullback") !== FALSE){ ?>
				<div class='active pullbacks'>
					PULLBACKS
				</div>
			<?php } else if (strpos($uri, "breakdown") !== FALSE){ ?>
				<div class='active breakdowns'>
					BREAKDOWNS
				</div>
			<?php } else if (strpos($uri, "short") !== FALSE){ ?>
				<div class='active shorts'>
					BACKDRAFTS
				</div>
			<?php }  ?>
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
			   		<a href='lists.breakouts.php'><?php echo GetTradeTypeConstantName(BREAKOUT_TRADE); ?></a>
			   	</li>
				<li class='sideMenuOption'>
			   		<a class="leftBar" href='lists.pullbacks.php'><?php echo GetTradeTypeConstantName(PULLBACK_TRADE); ?></a>
			   	</li>
				<li class='sideMenuOption'>
			   		<a href='lists.breakdowns.php'><?php echo GetTradeTypeConstantName(BREAKDOWN_TRADE); ?></a>
			   	</li>
				<li class='sideMenuOption'>
			   		<a href='lists.shorts.php'><?php echo GetTradeTypeConstantName(BACKDRAFT_TRADE); ?></a>
			   	</li>
				<li class='sideMenuOption2 topBreak'>
			   		<a class="leftBar" href='history.php'>History</a>
			   	</li>
				<li class='sideMenuOption2'>
			   		<a class="leftBar" href='accountinfo.php'>Settings</a>
			   	</li>
		   		<?php if ($login->isAdmin ){ ?>
				
					<li class='sideMenuOption2'>
				   		<a class="leftBar" href='admin.editall.php'>Admin</a>
				   	</li>
				<?php } ?>
				   	
				<li class='sideMenuOption2'>
			   		<a class="leftBar" href='?lo'>Log Out</a>
			   	</li>
			</ul>			

		</div>
	</div>
	<div id="bigMenu">
		<div class="wrapper">
			<div class="title item">
		 		<a href="#" id="n0" class="siteLink">
		 			BioBounce
		 		</a> 
			</div>
			<ul>
				<?php if (strpos($uri, "breakout") !== FALSE){ ?>
					<li class='active breakouts'>
				<?php }else { ?>
					<li>
				<?php } ?>
			   		<a href='lists.breakouts.php'><?php echo GetTradeTypeConstantName(BREAKOUT_TRADE); ?></a>
			   	</li>
				<?php if (strpos($uri, "pullback") !== FALSE){ ?>
					<li class='active pullbacks'>
				<?php }else { ?>
					<li>
				<?php } ?>
			   		<a class="leftBar" href='lists.pullbacks.php'><?php echo GetTradeTypeConstantName(PULLBACK_TRADE); ?></a>
			   	</li>
				<?php if (strpos($uri, "breakdown") !== FALSE){ ?>
					<li class='active breakdowns'>
				<?php }else { ?>
					<li>
				<?php } ?>
			   		<a href='lists.breakdowns.php'><?php echo GetTradeTypeConstantName(BREAKDOWN_TRADE); ?></a>
			   	</li>
				<?php if (strpos($uri, "short") !== FALSE){ ?>
					<li class='active shorts'>
				<?php }else { ?>
					<li>
				<?php } ?>
			   		<a href='lists.shorts.php'><?php echo GetTradeTypeConstantName(BACKDRAFT_TRADE); ?></a>
			   	</li>
			</ul>			
			<div class="account item">
				<div class="helloName">Hello <?php echo $login->username; ?></div>
				<div class="logoutDiv">
					<a href="history.php" class="logout">
						History
					</a>
					<span class="divider"></span>
					<a href="accountinfo.php" class="logout">
						Settings
					</a>
	<?php 
	if ($login->isAdmin ){ 
	?>
					<span class="divider"></span>
					<a href="admin.editall.php" class="logout">
						Admin
					</a>
	<?php
						}
					?>
					<span class="divider"></span>
					<a class="logout" href="?lo">Log Out</a>
				</div>
			</div>
		</div>

	</div>
</div>