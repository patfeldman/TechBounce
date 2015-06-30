<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	
	$login = new login(true);
	
/// REMOVE SOMETHING
	if (isset($_GET['delete_hid'])){
		$holdings = new holdings();
		$holdings->set_variable('holdings_id', $_GET['delete_hid']);
		if ($holdings->load()){
			$holdings->delete();
		}
	}

// Load the information from the database
	$tradeTypesToUse = array(LONG_TRADE,  PULLBACK_TRADE, SHORT_TRADE, BACKDRAFT_TRADE, BREAKOUT_TRADE, BREAKDOWN_TRADE);	
	$holdings=  array();
	foreach ($tradeTypesToUse as $tradeType){
		$holdings[$tradeType] = ticker_group_info::retrieveHoldingsArray($tradeType);
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

<body id="EditAll">
<!-- Panel -->
<?php include 'navBarMain012715.php'; ?>
<?php include 'navSecBarAdminNew.php'; ?>
<div class = "newwrap">
	<div class="section n0 n1">
		<div id="maintitle">
			Admin Holdings
		</div >
<?php
foreach  ($tradeTypesToUse as $index){
		if (sizeof($holdings[$index]) == 0) continue; 
	
?>
		<div class="sectionTitle" id="Header<?php echo GetTradeTypeConstantName($index); ?>">
			- <?php echo GetTradeTypeConstantName($index); ?>
		</div >
		<div class="tablegroup" id="Table<?php echo GetTradeTypeConstantName($index); ?>">
				<table class="watchtable" cellspacing="0" cellpadding ="0" border="0">
					<thead>
						<tr class="table_head"> <th colspan=11><?php GetTradeTypeConstantName($index); ?></th></tr>
						<tr class="table_head">
							<th class="bottom">Orig Date</th>
							<th class="bottom">Symbol</th>
							<th class="bottom">Last</th>
							<?php
								if ($index == BREAKOUT_TRADE || $index == BREAKDOWN_TRADE){
									echo '<th class="bottom">Target</th>';
									echo '<th class="bottom">Abandon</th>';
									echo '<th class="bottom">Hard Stop</th>';
									echo '<th class="bottom">Orig</th>';
								} else if ($index == BACKDRAFT_TRADE || $index == PULLBACK_TRADE){
									echo '<th class="bottom">Target</th>';
									echo '<th class="bottom">Abandon</th>';
									echo '<th class="bottom">Orig</th>';
								} else {
									echo '<th class="bottom">T1</th>';
									echo '<th class="bottom">T2 </th>';
									echo '<th class="bottom">T3 </th>';
									echo '<th class="bottom">Abandon</th>';
									echo '<th class="bottom">Orig</th>';
								}
							?>
							<th class="bottom">Return</th>
							<th class="bottom">LastAction</th>
							<th class="bottom">remove</th>
							<th class="bottom">edit</th>
						</tr>
					</thead>
					<tbody>
<?php
	$tablestate = "row_odd";
	$holdCounter = 0;
	foreach($holdings[$index] as $holding){
		$holdCounter++;
		$holdId = $holding['holdings_id'];
		$tId = $holding['ticker_id'];
		$lastActionStr = GetLastActionString($index, $holding['holdings_last_action']);
		$last = $holding['last'];
		$hitT1 = $holding['holdings_t1_marked'];
		$hitT2 = $holding['holdings_t2_marked'];
		$hitT3 = $holding['holdings_t3_marked'];
		$t1 = floatval($holding['holdings_t1']);
		$t2 = floatval($holding['holdings_t2']);
		$t3 = floatval($holding['holdings_t3']);
		$start = $holding['holdings_top_price'];			
		if ($index == BREAKDOWN_TRADE || $index == BREAKOUT_TRADE){
			$start = $holding['holdings_orig_price'];
		}	
		$return_percent = holdings::GetReturnPercent($index, $last, $start, $t1, $t2, $t3, $hitT1, $hitT2, $hitT3 );
	
		$t1Class = ($hitT1) ? "owned" : "";
		$t2Class = ($hitT2) ? "owned" : "";
		$t3Class = ($hitT3) ? "owned" : "";
		
		echo '<tr class="table_row '. $tablestate. ' ">';
		echo '<td>' . date("Y-m-d", strtotime($holding['holdings_orig_date'])) . '</td>';
		echo '<td><a class="stocklink" target="_blank" href="https://www.google.com/finance?q=' . strtoupper($holding['ticker_symbol'])  . '" >' . strtoupper($holding['ticker_symbol']) . '</a></td>';
		echo '<td>' . number_format($holding['last'], 2) . '</td>';
		if ($index == BREAKOUT_TRADE || $index == BREAKDOWN_TRADE){
			echo '<td class="'.$t3Class.'">' . number_format($holding['holdings_t3'], 2) . '</td>';			
		} else if ($index == BACKDRAFT_TRADE || $index == PULLBACK_TRADE){
			echo '<td class="'.$t1Class.'">' . number_format($holding['holdings_t1'], 2) . '</td>';
		} else {
			echo '<td class="'.$t1Class.'">' . number_format($holding['holdings_t1'], 2) . '</td>';
			echo '<td class="'.$t2Class.'">' . number_format($holding['holdings_t2'], 2) . '</td>';
			echo '<td class="'.$t3Class.'">' . number_format($holding['holdings_t3'], 2) . '</td>';
		}
		
		echo '<td>' . number_format($holding['holdings_stop_price'], 2) . '</td>';
		if ($index == BREAKOUT_TRADE || $index == BREAKDOWN_TRADE){
			echo '<td>' . number_format($holding['holdings_top_price'], 2) . '</td>';
		}
		echo '<td>' . number_format($holding['holdings_orig_price'], 2) . '</td>';
		echo '<td>' . number_format($return_percent, 2) . '</td>';
		echo '<td>' . $lastActionStr . '</td>';
		echo '<td><a class="delete_link" href="?delete_hid='. $holdId . '">remove</a></td>'. "\n";
		echo '<td><a class="edit_link" href="admin.editholding.php?edit_id='. $holdId . '">edit</a></td>'. "\n";

		echo '</tr>';
		if ($tablestate=='row_odd') $tablestate = 'row_even';
		else $tablestate = 'row_odd';
	}

	echo '</tr>'. "\n";

?>					
						
					</tbody>
				</table>
				<div class="editAll <?php echo GetTradeTypeConstantName($index); ?>">
					<a href="admin.editholdingsabandon.php?tt=<?php echo $index; ?>" class="buttonLook editLink">EDIT ABANDON ON <?php echo GetTradeTypeConstantName($index); ?></a>
				</div>

		</div>
<?php } ?>
	</div>
</div>
<div class='emptyBottom'></div>

<script src="js/navBar.js" type="text/javascript"></script>
<script src="js/pageInit.js" type="text/javascript"></script>
<script src="js/admin.js" type="text/javascript"></script>
</body>
</html>