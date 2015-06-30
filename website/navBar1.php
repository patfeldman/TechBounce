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
			<?php if (strpos($uri, "members") !== FALSE){ ?>
				<div class='active pullbacks'>
					PULLBACKS
				</div>
			<?php } else if (strpos($uri, "shortlists") !== FALSE){ ?>
				<div class='active shorts'>
					SHORTS
				</div>
			<?php } else if (strpos($uri, "reversals") !== FALSE){ ?>
				<div class='active reversals'>
					REVERSALS
				</div>
			<?php } else if (strpos($uri, "history") !== FALSE){ ?>
				<div class='active history'>
					HISTORY
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
				<?php if (strpos($uri, "members") == FALSE){ ?>
					<li class='sideMenuOption'>
				   		<a class="leftBar" href='members.php'>Pullbacks</a>
				   	</li>
				<?php } ?> 
				<?php if (strpos($uri, "reversals") == FALSE){ ?>
					<li class='sideMenuOption'>
				   		<a href='reversals.php'>Reversals</a>
				   	</li>
				<?php } ?>
				<?php if (strpos($uri, "shortlists") == FALSE){ ?>
					<li class='sideMenuOption'>
				   		<a href='shortlists.php'>Shorts</a>
				   	</li>
				<?php } ?>
				<?php if (strpos($uri, "history") == FALSE){ ?>
					<li class='sideMenuOption'>
				   		<a href='history.php'>History</a>
				   	</li>
				<?php } ?>
				<li class='sideMenuOption2 topBreak'>
			   		<a class="leftBar" href='accountinfo.php'>Settings</a>
			   	</li>
		   		<?php if ($isAdmin ){ ?>
				
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
				<?php if (strpos($uri, "members") !== FALSE){ ?>
					<li class='active pullbacks'>
				<?php }else { ?>
					<li>
				<?php } ?>
			   		<a class="leftBar" href='members.php'>Pullbacks</a>
			   	</li>
				<?php if (strpos($uri, "reversals") !== FALSE){ ?>
					<li class='active reversals'>
				<?php }else { ?>
					<li>
				<?php } ?>
			   		<a href='reversals.php'>Reversals</a>
			   	</li>
				<?php if (strpos($uri, "shortlists") !== FALSE){ ?>
					<li class='active shorts'>
				<?php }else { ?>
					<li>
				<?php } ?>
			   		<a href='shortlists.php'>Shorts</a>
			   	</li>
				<?php if (strpos($uri, "history") !== FALSE){ ?>
					<li class='active history'>
				<?php }else { ?>
					<li>
				<?php } ?>
			   		<a href='history.php'>History</a>
			   	</li>
			</ul>			
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
		</div>

	</div>
</div>