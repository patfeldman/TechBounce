<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	$login = new login(true);
	
	if (isset($_GET['tt'])){
		$tradeType = intval($_GET['tt']);
	}else{
		$tradeType = BREAKOUT_TRADE;
	}
	

	$counter = 1;
	while (true){
		if (!isset($_POST[$counter])){
			break;
		}
		$watchId = $_POST[$counter];
		$watchlist = new watchlist();
		$watchlist->set_variable('watchlist_id', $watchId);
		if ($watchlist->load()){
			$low = floatval($_POST['low_' . $watchId ]);
			$high = floatval($_POST['high_' . $watchId ]);
			$oldLow = floatval($watchlist->get_variable('watchlist_low'));
			$oldHigh = floatval($watchlist->get_variable('watchlist_high'));
			if ($oldLow != $low || $oldHigh != $high){
				$className = adminControlWatchlist::GetWatchClassName($tradeType);
				$updateWatch = new $className();
				$updateWatch->UpdateWatch($watchlist, $low, $high);	
			}
		}
		$counter++;
	}


	$watchlists =  array();
	$watchlists = ticker_group_info::retrieveAdminWatchlistArray($tradeType);
?>


<!DOCTYPE html>
<html>
<head>
<title>BioBounce.com</title>

<meta name = "keywords" content = "biotech, stock, market, swing trading, stock trading" />
<meta name = "description" content = "" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, minimum-scale=1 user-scalable=no">

<link href="css/biobounce.css" rel="stylesheet" type="text/css">
<link href="css/biomembers.css" rel="stylesheet" type="text/css">
<link href="css/bioadmin.css" rel="stylesheet" type="text/css">
<link href="css/biomembersnew.css" rel="stylesheet" type="text/css">
<link href="css/RESPONSIVE.css" rel="stylesheet" type="text/css">
<link href="css/responsive_admin.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Della+Respira' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Josefin+Slab' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Artifika' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Radley' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>

<body id="EditAllWatches">
<!-- Panel -->
<?php include 'navBarMain012715.php'; ?>
<?php include 'navSecBarAdminNew.php'; ?>

<div class = "newwrap">
	<div class="section n0 n1">
		<div id="maintitle">
			Admin Watchlists<br/>
		</div >
		<div class="sectionTitle <?php echo GetTradeTypeConstantName($tradeType); ?>">
			- <?php echo GetTradeTypeConstantName($tradeType); ?>
		</div >
		<div class="tablegroup" id="Table<?php echo GetTradeTypeConstantName($tradeType); ?>" >
			<form class="clearfix" action="" method="post">
				<table class="watchtable" cellspacing="0" cellpadding ="0" border="0">
					<thead>
						<tr class="table_head"> <th colspan=11><?php GetTradeTypeConstantName($tradeType); ?></th></tr>
						<tr class="table_head">
							<th class="bottom left"> symbol </th>
							<th class="bottom"> last </th>
							<?php
								if ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE){
									echo '<th class="bottom"> entry </th>';
									echo '<th class="bottom"> range </th>';
									echo '<th class="bottom"> hard stop </th>';
									echo '<th class="bottom"> target </th>';
								}else if ($tradeType == PULLBACK_TRADE || $tradeType == BACKDRAFT_TRADE){
									echo '<th class="bottom"> low </th>';
									echo '<th class="bottom"> high </th>';
									echo '<th class="bottom"> top </th>';
									echo '<th class="bottom"> bottom </th>';
									echo '<th class="bottom"> target </th>';
								} else {
									echo '<th class="bottom"> low </th>';
									echo '<th class="bottom"> high </th>';
									echo '<th class="bottom"> top </th>';
									echo '<th class="bottom"> bottom </th>';
									echo '<th class="bottom"> t1 </th>';
									echo '<th class="bottom"> t2 </th>';
									echo '<th class="bottom"> t3 </th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
<?php
	$tablestate = "row_odd";
	$counter = 0;
	foreach($watchlists as $watch){
		$counter ++;
		$id = $watch['watchlist_id'];
		$zoneStr = ($watch['watchlist_is_zoned']) ? "  (ZONED)" : "";
		echo '<input type="hidden" name="'.$counter.'" value="'.$id.'"/>' . "\n";
		echo '<tr class="table_row '. $tablestate. ' ">'. "\n";
		echo '<td class="left">' . strtoupper($watch['ticker_symbol']) . $zoneStr . '</td>'. "\n";
		echo '<td>' . number_format($watch['last'], 2) . '</td>'. "\n";
		echo '<td><input class="field" type="text" name="low_'. $id. '" value="' . number_format($watch['watchlist_low'], 2) . '" size="5" /></td>'. "\n";
		echo '<td><input class="field" type="text" name="high_'. $id. '" value="' . number_format($watch['watchlist_high'], 2) . '" size="5" /></td>'. "\n";
		echo '<td>' . number_format($watch['watchlist_top'], 2) . '</td>'. "\n";
		if ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE){
			echo '<td>' . number_format($watch['watchlist_target3'], 2) . '</td>'. "\n";
		}else if ($tradeType == PULLBACK_TRADE || $tradeType == BACKDRAFT_TRADE){
			echo '<td>' . number_format($watch['watchlist_bottom'], 2) . '</td>'. "\n";	
			echo '<td>' . number_format($watch['watchlist_target1'], 2) . '</td>'. "\n";
		} else {
			echo '<td>' . number_format($watch['watchlist_bottom'], 2) . '</td>'. "\n";	
			echo '<td>' . number_format($watch['watchlist_target1'], 2) . '</td>'. "\n";
			echo '<td>' . number_format($watch['watchlist_target2'], 2) . '</td>'. "\n";
			echo '<td>' . number_format($watch['watchlist_target3'], 2) . '</td>'. "\n";
		}	
		echo '</tr>'. "\n";
		if ($tablestate=='row_odd') $tablestate = 'row_even';
		else $tablestate = 'row_odd';
	}

	echo '</tr>'. "\n";

?>					
						
					</tbody>
				</table>
				
				<div class="bt_wrap"><a href="admin.editall.php" class="buttonLook editLink">BACK</a><input type="submit" name="submit" value="Save Changes" class="bt_login" /></div>

			</form>
		</div>
	</div>
</div>
<div class='emptyBottom'></div>

<script src="js/navBar.js" type="text/javascript"></script>
<script src="js/pageInit.js" type="text/javascript"></script>
<script src="js/admin.js" type="text/javascript"></script>

</body>
</html>
