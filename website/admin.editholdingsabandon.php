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
		$holdId = $_POST[$counter];
		$holding = new holdings();
		$holding->set_variable('holdings_id', $holdId);
		if ($holding->load()){
			$oldAbandon = floatval($holding->get_variable('holdings_stop_price'));
			$oldHard = floatval($holding->get_variable('holdings_top_price'));
			$isEdited = false;
			if (isset($_POST['abandon_' . $holdId ])){
				$abandon = floatval($_POST['abandon_' . $holdId ]);
				if ($abandon != $oldAbandon){
					$holding->set_variable('holdings_stop_price', $abandon);
					highlights::holdingsHighlight($holdId, H_ABANDON, 0, highlights::EVENT_START_DAY);				
					$isEdited = true;
				}
			}
			if (isset($_POST['hardstop_' . $holdId ])){
				$hardstop = floatval($_POST['hardstop_' . $holdId ]);
				if ($hardstop != $oldHard){
					$holding->set_variable('holdings_top_price', $hardstop);
					highlights::holdingsHighlight($holdId, H_HARDSTOP, 0, highlights::EVENT_START_DAY);				
					$isEdited = true;
				}
			}
			if ($isEdited){
				$holding->update();	
			}
		}
		$counter++;
	}


	$holdings =  array();
	$holdings = ticker_group_info::retrieveHoldingsArray($tradeType);
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
							<th class="bottom">Symbol</th>
							<th class="bottom">Last</th>
							<?php
								if ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE){
									echo '<th class="bottom">Orig</th>';
									echo '<th class="bottom">Target</th>';
									echo '<th class="bottom">Abandon</th>';
									echo '<th class="bottom">Hard Stop</th>';
								} else if ($tradeType == BACKDRAFT_TRADE || $tradeType == PULLBACK_TRADE){
									echo '<th class="bottom">Orig</th>';
									echo '<th class="bottom">Target</th>';
									echo '<th class="bottom">Abandon</th>';
								} else {
									echo '<th class="bottom">Orig</th>';
									echo '<th class="bottom">T1</th>';
									echo '<th class="bottom">T2 </th>';
									echo '<th class="bottom">T3 </th>';
									echo '<th class="bottom">Abandon</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
<?php
	$tablestate = "row_odd";
	$holdCounter = 0;
	foreach($holdings as $holding){
		$holdCounter++;
		$holdId = $holding['holdings_id'];
		$tId = $holding['ticker_id'];
		$last = $holding['last'];
		$hitT1 = $holding['holdings_t1_marked'];
		$hitT2 = $holding['holdings_t2_marked'];
		$hitT3 = $holding['holdings_t3_marked'];
		$t1 = floatval($holding['holdings_t1']);
		$t2 = floatval($holding['holdings_t2']);
		$t3 = floatval($holding['holdings_t3']);
		$start = $holding['holdings_top_price'];			
		if ($tradeType == BREAKDOWN_TRADE || $tradeType == BREAKOUT_TRADE){
			$start = $holding['holdings_orig_price'];
		}	
	
		$t1Class = ($hitT1) ? "owned" : "";
		$t2Class = ($hitT2) ? "owned" : "";
		$t3Class = ($hitT3) ? "owned" : "";
		
		echo '<input type="hidden" name="'.$holdCounter.'" value="'.$holdId.'"/>' . "\n";
		echo '<tr class="table_row '. $tablestate. ' ">';
		echo '<td><a class="stocklink" target="_blank" href="https://www.google.com/finance?q=' . strtoupper($holding['ticker_symbol'])  . '" >' . strtoupper($holding['ticker_symbol']) . '</a></td>';
		echo '<td>' . number_format($holding['last'], 2) . '</td>';
		echo '<td>' . number_format($holding['holdings_orig_price'], 2) . '</td>';
		if ($tradeType != BREAKOUT_TRADE && $tradeType != BREAKDOWN_TRADE && $tradeType != BACKDRAFT_TRADE && $tradeType != PULLBACK_TRADE){
			echo '<td class="'.$t1Class.'">' . number_format($holding['holdings_t1'], 2) . '</td>';
			echo '<td class="'.$t2Class.'">' . number_format($holding['holdings_t2'], 2) . '</td>';
		}
		echo '<td class="'.$t3Class.'">' . number_format($holding['holdings_t3'], 2) . '</td>';

		echo '<td><input class="field" type="text" name="abandon_'. $holdId. '" value="' . number_format($holding['holdings_stop_price'], 2) . '" size="5" /></td>'. "\n";
		if ($tradeType == BREAKOUT_TRADE || $tradeType == BREAKDOWN_TRADE){
			echo '<td><input class="field" type="text" name="hardstop_'. $holdId. '" value="' . number_format($holding['holdings_top_price'], 2) . '" size="5" /></td>'. "\n";
		}

		echo '</tr>';
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
