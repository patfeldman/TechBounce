<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	
	$login = new login(true);
	if (isset($_GET['edit_id'])){
		$watchlistId = intval($_GET['edit_id']);
	}else if (isset($_POST['watchlist_id'])){
		$watchlistId = intval($_POST['watchlist_id']);	
	}else if (isset($_GET['zone_id'])){
		$watchlistId = intval($_GET['zone_id']);	
	} else {
		header( 'Location: admin.editall.php' ) ;
	}
	$watchlistItem = new watchlist();
	$watchlistItem->set_variable('watchlist_id', $watchlistId); 
	if($watchlistItem->load()){
		$tradeType = $watchlistItem->get_variable('watchlist_tradetype');
	}
	// Save any edited data	
	
	if (isset($_POST["low"]) || isset($_GET['zone_id'])){
		$className = adminControlWatchlist::GetWatchClassName($tradeType);
		$watchlistAdmin = new $className($watchlistId);
	}
	if (isset($_POST["low"]) ) {
		$low = isset($_POST["low"]) ? $_POST["low"] : 0;
		$high = isset($_POST["high"]) ? $_POST["high"] : 0;
		$top = isset($_POST["top"]) ? $_POST["top"] : 0;
		$bottom = isset($_POST["bottom"]) ? $_POST["bottom"] : 0;
		$t1 = isset($_POST["t1"]) ? $_POST["t1"] : 0;
		$t2 = isset($_POST["t2"]) ? $_POST["t2"] : 0; 
		$t3 = isset($_POST["t3"]) ? $_POST["t3"] : 0; 
		$tip = isset($_POST["tip"]) ? $_POST["tip"] : 0;
		$watchlistAdmin->Edit($low, $high, $top, $bottom, $t1, $t2, $t3, $tip);
	}
	if (isset($_GET['zone_id'])){
		$watchlistAdmin->Zone();
	}	

// Load all new tickers
	if($watchlistItem->load()){
		$lowOrEntry = $watchlistItem->get_variable('watchlist_low');
		$highOrRange = $watchlistItem->get_variable('watchlist_high');
		$top = $watchlistItem->get_variable('watchlist_top');
		$bottom = $watchlistItem->get_variable('watchlist_bottom');
		$t0 = $watchlistItem->get_variable('watchlist_target0');
		$t1 = $watchlistItem->get_variable('watchlist_target1');
		$t2 = $watchlistItem->get_variable('watchlist_target2');
		$t3 = $watchlistItem->get_variable('watchlist_target3');
		$tickerId = $watchlistItem->get_variable('watchlist_ticker_id');
		$tip = $watchlistItem->get_variable('watchlist_tooltip');
		$isZoned = $watchlistItem->get_variable('watchlist_is_zoned');
		
		$ticker = new ticker();
		$ticker->set_variable('ticker_id', $tickerId);
		$tradeTypeName = GetTradeTypeConstantName($tradeType);
		if ($ticker->load()){
			$tickerSymbol = $ticker->get_variable('ticker_symbol');
		} else {
			$tickerSymbol = "ERROR";
		}		
	} else{
		//header( 'Location: admin.editall.php' ) ;
	}
	
	$lowTitleName = ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE) ? "Entry" : "Low";
	$highTitleName = ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE) ? "Range" : "High";
	$topTitleName = ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE) ? "Hard Stop" : "Top";
	$targetTitleName1 = ($tradeType == PULLBACK_TRADE || $tradeType == BACKDRAFT_TRADE) ? "Target" : "T1";
	$targetTitleName3 = ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE) ? "Target" : "T3";
	$t0ClassName = ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE) ? "" : "none";
	$bottomClassName = ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE) ? "none" : "";
	$zoneStr = ($isZoned) ? " <div class='note2'>(zoned)</div>" : "";
	$showT1 = $showT2 = $showT3 = true;	
	if ($tradeType == PULLBACK_TRADE || $tradeType == BACKDRAFT_TRADE){
		$showT2 = $showT3 = false;
	} else if ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE){
		$showT1 = $showT2 = false;
	}
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

<body id="EditWatch">
<!-- Panel -->
<?php include 'navBarMain012715.php'; ?>
<?php include 'navSecBarAdminNew.php'; ?>
<div class = "newwrap">
	<div class="section n0 n1">
		<div id="maintitle">
			Admin Watchlist Item
			<div id="TickerName">Symbol : <?php echo $tickerSymbol . $zoneStr; ?></div>
			<div id="TickerType" class="ticker<?php echo $tradeTypeName; ?>"><?php echo $tradeTypeName; ?></div>
		</div >
		<div class="inputGroup ticker<?php echo $tradeTypeName; ?>" >
			<form class="clearfix"  action="admin.editwatch.php" method="post">
				<input type="hidden" name="watchlist_id" value="<?php echo $watchlistId; ?>"/>
				<div id="LowHigh" class="editSection">
					<div class="group"><div class="title"><?php echo $lowTitleName; ?></div><input class="inputBox" type="number" step="any" name="low" value="<?php echo $lowOrEntry;?>"/></div>
					<div class="group"><div class="title"><?php echo $highTitleName; ?></div><input class="inputBox" type="number" step="any" name="high" value="<?php echo $highOrRange;?>"/></div>				
					<div class="group"><div class="title">Tip</div><input class="inputBox" type="text" name="tip" value="<?php echo $tip;?>"/></div>				
					<div style="clear:both"></div>
				</div>
				<div id="TopBottom" class="editSection">
					<div class="group"><div class="title"><?php echo $topTitleName;?></div><input class="inputBox" type="number" step="any" name="top" value="<?php echo $top;?>"/></div>
					<div class="group <?php echo $bottomClassName;?>"><div class="title">Bottom</div><input class="inputBox" type="number" step="any" name="bottom" value="<?php echo $bottom;?>"/></div>				
					<div style="clear:both"></div>
				</div>
				<div id="LowHigh" class="editSection">
					<?php if ($showT1) { ?><div class="group"><div class="title"><?php echo $targetTitleName1;?></div><input class="inputBox" type="number" step="any" name="t1" value="<?php echo $t1;?>"/></div><?php } ?>				
					<?php if ($showT2) { ?><div class="group"><div class="title">T2</div><input class="inputBox" type="number" step="any" name="t2" value="<?php echo $t2;?>"/></div><?php } ?>
					<?php if ($showT3) { ?><div class="group"><div class="title"><?php echo $targetTitleName3;?></div><input class="inputBox" type="number" step="any" name="t3" value="<?php echo $t3;?>"/></div><?php } ?>
					<div style="clear:both"></div>
				</div>
				<div style="clear:both"></div>

				<div class="bt_wrap">
					<input type="submit" name="submit" value="Save Changes" class="bt_login" />
				</div>
			</form>
		</div>
		<div class="buttonBottom">
			<a href="admin.editall.php" class="buttonLook">Back to Watchlists</a>
			<?php if (!$isZoned){ ?>
				<a class="buttonLook" href="?zone_id=<?php echo $watchlistId; ?>">ZONE NOW!</a>
			<?php } ?>
		</div>

	</div>
</div>
<div class='emptyBottom'></div>

<script src="js/navBar.js" type="text/javascript"></script>
<script src="js/pageInit.js" type="text/javascript"></script>
<script src="js/admin.js" type="text/javascript"></script>

</body>
</html>