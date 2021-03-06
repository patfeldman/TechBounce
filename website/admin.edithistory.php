<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	
	$login = new login(true);
	if (isset($_GET['edit_id'])){
		$holdingId= intval($_GET['edit_id']);
	}else if (isset($_POST['holding_id'])){
		$holdingId= intval($_POST['holding_id']);	
	} else {
		header( 'Location: admin.editallhistory.php' ) ;
	}
	
	
	// load holding data 
	$holdingItem = new holdings();
	$holdingItem->set_variable('holdings_id', $holdingId); 
	if($holdingItem->load()){
		$abandonDate = strtotime($holdingItem->get_variable('holdings_abandon_date'));
		$tickerId = $holdingItem->get_variable('holdings_ticker_id');
		$ticker = new ticker();
		$ticker->set_variable('ticker_id', $tickerId);
		if ($ticker->load()){
			$tickerSymbol = $ticker->get_variable('ticker_symbol');
			$last = $ticker->get_variable('last');
		} else {			
		}		

		// Mark Targets
		if (isset($_GET['markTarget'])){
			$holdingItem->set_variable('holdings_t'.$_GET['markTarget'].'_marked', 1);
			$holdingItem->update();
		}
		
		// update with the post variables
		if (isset($_POST['origPrice'])){
			$holdingItem->set_variable('holdings_t1', floatval($_POST['t1']));
			$holdingItem->set_variable('holdings_t2', floatval($_POST['t2']));
			$holdingItem->set_variable('holdings_t3', floatval($_POST['t3']));
			$holdingItem->set_variable('holdings_tooltip', $_POST['tip']);
			$holdingItem->set_variable('holdings_orig_price', floatval($_POST['origPrice']));
			$holdingItem->set_variable('holdings_stop_price', floatval($_POST['abandonPrice']));
			$holdingItem->set_variable('holdings_top_price', floatval($_POST['hardStopPrice']));
			$holdingItem->set_variable('holdings_orig_date', $_POST['origdate']);
			if ($abandonDate != null){
				$holdingItem->set_variable('holdings_abandon_date', $_POST['abandondate']);
			}
			$holdingItem->update();
		}
		
		$origDate = strtotime( $holdingItem->get_variable('holdings_orig_date'));
		
		$tickerSymbol = $holdingItem->get_variable('holdings_ticker_symbol');
		$action = $holdingItem->get_variable('holdings_last_action');
		$t0 = $holdingItem->get_variable('holdings_t0');
		$t1 = $holdingItem->get_variable('holdings_t1');
		$t2 = $holdingItem->get_variable('holdings_t2');
		$t3 = $holdingItem->get_variable('holdings_t3');
		$hitT0 = $holdingItem->get_variable('holdings_t0_marked');
		$hitT1 = $holdingItem->get_variable('holdings_t1_marked');
		$hitT2 = $holdingItem->get_variable('holdings_t2_marked');
		$hitT3 = $holdingItem->get_variable('holdings_t3_marked');
		$tip = $holdingItem->get_variable('holdings_tooltip');
		$origPrice = $holdingItem->get_variable('holdings_orig_price');
		$abandonPrice = $holdingItem->get_variable('holdings_stop_price');
		$hardStopPrice = $holdingItem->get_variable('holdings_top_price');
		$tradeType = $holdingItem->get_variable('holdings_tradetype');
		$tradeTypeName = GetTradeTypeConstantName($tradeType);
		
		

	} else{
		//header( 'Location: admin.editallhistory.php' ) ;
	}
	
	$t0ClassName = ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE) ? "" : "none";
	$hardStopClassName = ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE) ? "" : "none";
	$t0HitClassName = (($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE) && !$hitT0) ? "" : "none";
	$t3Title = ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE) ? "Target" : "T3";
	$t1Title = ($tradeType == BACKDRAFT_TRADE || $tradeType == PULLBACK_TRADE) ? "Target" : "T1";
	$abandonClassName = (IsAbandoned($action)) ? "none" : "";
	$targetsHitStr = (!$hitT1) ? " <div class='note2'>(no targets hit)</div>" : "";
	$t1HitClassName = (!$hitT1) ? "" : "none";
	$t2HitClassName = ($hitT1 && !$hitT2) ? "" : "none";
	$t3HitClassName = ($hitT1 && $hitT2 && !$hitT3) ? "" : "none";
	
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

<body id="EditHolding">
<!-- Panel -->
<?php include 'navBarMain012715.php'; ?>
<?php include 'navSecBarAdminNew.php'; ?>
<div class = "newwrap">
	<div class="section n0 n1">
		<div id="maintitle">
			Admin History Item
			<div id="TickerName">Symbol : <?php echo $tickerSymbol . $targetsHitStr; ?></div>
			<div id="TickerType" class="ticker<?php echo $tradeTypeName; ?>"><?php echo $tradeTypeName; ?></div>
		</div >
		<div class="inputGroup ticker<?php echo $tradeTypeName; ?>" >
			<form class="clearfix"  action="admin.edithistory.php" method="post">
				<input type="hidden" name="holding_id" value="<?php echo $holdingId; ?>"/>
				<div id="Dates" class="editSection">
					<div class="group"><div class="title">Original Date</div><input class="inputBox" type="date" name="origdate" value="<?php echo date ('Y-m-d', $origDate);?>"/></div>
<?php		if ($abandonDate!=null) { ?> 
					<div class="group"><div class="title">Abandon Date</div><input class="inputBox" type="date" name="abandondate" value="<?php echo  date ('Y-m-d',$abandonDate);?>"/></div>
<?php 		} ?>
					
					<div class="group"><div class="title">Tip</div><input class="inputBox" type="text" name="tip" value="<?php echo $tip;?>"/></div>		
					<div class="group"><div class="title">Last Action</div><div class="textBox"><?php echo GetLastActionString($tradeType, $action); ?></div></div>		
					<div style="clear:both"></div>
				</div>
				<div id="Prices" class="editSection">
					<div class="group"><div class="title">Original Price</div><input class="inputBox" type="number" step="any" name="origPrice" value="<?php echo $origPrice;?>"/></div>
					<div class="group"><div class="title">Abandon Price</div><input class="inputBox" type="number" step="any" name="abandonPrice" value="<?php echo $abandonPrice;?>"/></div>	
					<div class="group <?php echo $hardStopClassName;?>"><div class="title">Hard Stop Price</div><input class="inputBox" type="number" step="any" name="hardStopPrice" value="<?php echo $hardStopPrice;?>"/></div>				
					<div style="clear:both"></div>
				</div>
				<div id="Targets" class="editSection">
					<?php if ($showT1) { ?><div class="group"><div class="title"><?php echo $t1Title;?><?php if ($hitT1) echo " - HIT"; ?></div><input class="inputBox" type="number" step="any" name="t1" value="<?php echo $t1;?>"/></div><?php } ?>	
					<?php if ($showT2) { ?><div class="group"><div class="title">T2<?php if ($hitT2) echo " - HIT"; ?></div><input class="inputBox" type="number" step="any" name="t2" value="<?php echo $t2;?>"/></div><?php } ?>
					<?php if ($showT3) { ?><div class="group"><div class="title"><?php echo $t3Title;?><?php if ($hitT3) echo " - HIT"; ?></div><input class="inputBox" type="number" step="any" name="t3" value="<?php echo $t3;?>"/></div><?php } ?>
					<div style="clear:both"></div>
				</div>
				<div style="clear:both"></div>

				<div class="bt_wrap">
					<input type="submit" name="submit" value="Save Changes" class="bt_login" />
				</div>
			</form>
		</div>
		<div class="buttonBottom">
			<a href="JavaScript:window.close()" class="buttonLook">Close Tab</a>		
			<a class="buttonLook <?php echo $t1HitClassName; ?>" href="?markTarget=1&edit_id=<?php echo $holdingId; ?>">Mark T1</a>
			<a class="buttonLook <?php echo $t2HitClassName; ?>" href="?markTarget=2&edit_id=<?php echo $holdingId; ?>">Mark T2</a>
			<a class="buttonLook <?php echo $t3HitClassName; ?>" href="?markTarget=3&edit_id=<?php echo $holdingId; ?>">Mark T3</a>
		</div>

	</div>
</div>
<div class='emptyBottom'></div>

<script src="js/navBar.js" type="text/javascript"></script>
<script src="js/pageInit.js" type="text/javascript"></script>
<script src="js/admin.js" type="text/javascript"></script>

</body>
</html>