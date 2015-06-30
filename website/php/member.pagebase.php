<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	$login = new login();
	$tradeTypes = array();
	$uri = $_SERVER['REQUEST_URI'];

	if (strpos($uri, "reversal") !== FALSE){
		array_push($tradeTypes, intval(REVERSAL_TRADE));
		$pageTitle = "Reversals";		
	}else if (strpos($uri, "short") !== FALSE){
		array_push($tradeTypes, intval(SHORT_TRADE));
		array_push($tradeTypes, intval(BACKDRAFT_TRADE));
		$pageTitle = "Shorts";				
		$typeStrings2 = new tradeTypeStrings($tradeTypes[1]);
	}else if (strpos($uri, "breakout") !== FALSE){
		array_push($tradeTypes, intval(BREAKOUT_TRADE));
		$pageTitle = "Breakouts";		
	}else if (strpos($uri, "breakdown") !== FALSE){
		array_push($tradeTypes, intval(BREAKDOWN_TRADE));
		$pageTitle = "Breakdowns";		
	}else if (strpos($uri, "pullback") !== FALSE){
		array_push($tradeTypes, intval(LONG_TRADE));
		//array_push($tradeTypes, intval(SHORT_TRADE));
		array_push($tradeTypes, intval(PULLBACK_TRADE));
		$pageTitle = "Pullbacks";
		$typeStrings2 = new tradeTypeStrings($tradeTypes[1]);
	}
	$typeStrings = new tradeTypeStrings($tradeTypes[0]);
	
?>


<!DOCTYPE html>
<html>
<head>
<title>BioBounce.com</title>

<meta name = "keywords" content = "biotech, stock, market, swing trading, stock trading" />
<meta name = "description" content = "" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, minimum-scale=1 user-scalable=no">
<!--
-->
<meta http-equiv="refresh" content="300" > 
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/navBar.js" type="text/javascript"></script>

<link href="css/biobounce.css" rel="stylesheet" type="text/css">
<link href="css/biomembers.css" rel="stylesheet" type="text/css">
<link href="css/RESPONSIVE.css" rel="stylesheet" type="text/css">
<link href="css/responsive.<?php echo strtolower($pageTitle); ?>.css" rel="stylesheet" type="text/css">
<link href="css/jquery.qtip.min.css" rel="stylesheet" type="text/css">
<link href="css/uniform.aristo.min.css" rel="stylesheet" type="text/css">

<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>

</head>

<body id="<?php echo $pageTitle; ?>">
<!-- Panel -->
<?php include 'navBarMain012715.php'; ?>
<?php include 'navBarSec012715.php'; ?>

<div class = "newwrap">
	<div class="section n0 <?php echo $login->trialTimeClass; ?>" >
		<div id="trialMessageTop"></div >
		<div class="desc trialMessage">
			<?php echo $login->trialMessage; ?> 
		</div>
	</div>
</div>
