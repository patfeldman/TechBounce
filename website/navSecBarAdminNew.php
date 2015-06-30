<?php
$uri = $_SERVER['REQUEST_URI'];
?>

	<div id="submenu" class="admin" >
	<div class="subNav">
		<ul class="subList">
			<li>
				<a href="admin.editall.php" class="navigatorAdmin <?php if (strstr($uri, "admin")!== FALSE) echo "selected"; ?>">
					Admin Watch
				</a>
			</li>
			<li>
				<a href="admin.editallholdings.php" class="navigatorAdmin <?php if (strstr($uri, "admin")!== FALSE) echo "selected"; ?>">
					Admin Hold
				</a>
			</li>
			<li>
				<a href="admin.editallhistory.php" class="navigatorAdmin <?php if (strstr($uri, "admin")!== FALSE) echo "selected"; ?>">
					Admin Hist
				</a>
			</li>
			<li>
				<a href="useradmin.php" class="navigatorAdmin <?php if (strstr($uri, "useradmin")!== FALSE) echo "selected"; ?>">
					Users
				</a>
			</li>
			<li>
				<a href="referraladmin.php" class="navigatorAdmin <?php if (strstr($uri, "useradmin")!== FALSE) echo "selected"; ?>">
					Refs
				</a>
			</li>
		</ul> 
	</div>
</div>