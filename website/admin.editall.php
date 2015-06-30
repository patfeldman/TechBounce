<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	$login = new login(true);
	
	if (isset($_POST['sendTweet'])){
		comms::ListUpdatedSendMessages();
	}
	
/// REMOVE SOMETHING
	if (isset($_GET['delete_id'])){
		$adminList = new adminControlWatchlist($_GET['delete_id']);
		$adminList->Remove();
		highlights::watchlistHighlight($_GET['delete_id'], W_ROW_DELETE, 0, highlights::EVENT_START_DAY);
	}

// Create all new tickers
	$counter = 1;
	while (true) {
		if (!isset($_POST["ticker" . $counter]) || strlen(trim($_POST["ticker" . $counter])) <= 0 ) {
			break;
		}
		$tradeType = $_POST["type" . $counter];
		$tickerName = $_POST["ticker" . $counter];
		$tickerLow = floatval($_POST["low" . $counter]);
		$tickerHigh = floatval($_POST["high" . $counter]);
		$className = adminControlWatchlist::GetWatchClassName($tradeType);
		$newTicker = new $className();
		$newTicker->AddNewTickerAndWatch($tickerName, $tickerLow, $tickerHigh);	
		$counter++;
	}
	



// Load the information from the database
	$tradeTypesToUseInDropdown = array(PULLBACK_TRADE, BACKDRAFT_TRADE, BREAKOUT_TRADE, BREAKDOWN_TRADE);	
	$tradeTypesToUse = array(LONG_TRADE,  PULLBACK_TRADE, SHORT_TRADE, BACKDRAFT_TRADE, BREAKOUT_TRADE, BREAKDOWN_TRADE);	
	$watchlists =  array();
	foreach ($tradeTypesToUse as $tradeType){
		$watchlists[$tradeType] = ticker_group_info::retrieveAdminWatchlistArray($tradeType);
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
<script type="text/javascript">
$(document).ready(function() {
<?php
	if (isset($_POST['sendTweet'])){
?>
		setTimeout(function() {  
			$('.messagebox').fadeOut('slow');  
		}, 4000); // <-- time in milliseconds  
<?php 
	}	
?>

});	


function sendTweet(){
    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('action', 'admin.editall.php');
    form.style.display = 'hidden';
    
    var data = document.createElement('input');
    data.setAttribute("type", "hidden");
    data.setAttribute("name", "sendTweet");
    data.setAttribute("value", "true");
    form.appendChild(data);
    document.body.appendChild(form)
    form.submit();	
}
</script>

</head>

<body id="EditAll">
<!-- Panel -->
<?php include 'navBarMain012715.php'; ?>
<?php include 'navSecBarAdminNew.php'; ?>
<?php
	if (isset($_POST['sendTweet'])){
?>
<div class="messagebox">Watchlist Update Tweet was sent. </div>;  
<?php
	}
?>


<div class = "newwrap">
	<div class="section n0 n1">
		<div id="maintitle">
			Admin Watchlists<br/>
		</div >
		<div class="inputGroup">
			<form class="clearfix"  action="admin.editall.php" method="post">
				<div id="InputHeaderGroup" class="columnGroup">
					<div class="column column1">Trade Type<div id="lockBoxSection"><input type="checkbox" value="lock" name="lockbox" class="lockbox" checked/>lock</div></div>
					<div class="column column2">Ticker</div>
					<div class="column column3" id="LowBox">Low</div>
					<div class="column column4" id="HighBox">High</div>
					<div style="clear:both"></div>
				</div>

<?php 
for ($i = 1; $i <= 4; $i++){
?>
				<div id="InputBodyGroup" class= "columnGroup">
					<div class="column column1">
						<select name="type<?php echo $i; ?>" class="enteredValues">
							
<?php 
							foreach  ($tradeTypesToUseInDropdown as $index){
								echo "<option value=\"". $index . "\">" . GetTradeTypeConstantName($index) . "</option>";
							}
?>
						</select>
					</div>
					<div class="column column2"><input type="text" name="ticker<?php echo $i; ?>" class="enteredValues"></input></div>
					<div class="column column3"><input type="number" step="any" name="low<?php echo $i; ?>" class="enteredValues"></input></div>
					<div class="column column4"><input type="number" step="any" name="high<?php echo $i; ?>" class="enteredValues"></input></div>
					<div style="clear:both"></div>				
				</div>
<?php
}
?>
				<div class="bt_wrap"><input type="submit" name="submit" value="Save Changes" class="bt_login" /><input type="button" value="Send New Tickers Tweet" class="bt_tweet" onclick="sendTweet();"/></div>
			</form>
		</div>
<?php
foreach  ($tradeTypesToUse as $index){
	if (sizeof($watchlists[$index]) == 0) continue; 
?>
		<div class="sectionTitle" id="Header<?php echo GetTradeTypeConstantName($index); ?>">
			- <?php echo GetTradeTypeConstantName($index); ?>
		</div >
		<div class="tablegroup" id="Table<?php echo GetTradeTypeConstantName($index); ?>" >
				<table class="watchtable" cellspacing="0" cellpadding ="0" border="0">
					<thead>
						<tr class="table_head"> <th colspan=11><?php GetTradeTypeConstantName($index); ?></th></tr>
						<tr class="table_head">
							<th class="bottom left"> symbol </th>
							<th class="bottom"> last </th>
							<?php
								if ($index == BREAKOUT_TRADE || $index == BREAKDOWN_TRADE){
									echo '<th class="bottom"> entry </th>';
									echo '<th class="bottom"> range </th>';
									echo '<th class="bottom"> hard stop </th>';
									echo '<th class="bottom"> target </th>';
								}else if ($index == PULLBACK_TRADE || $index == BACKDRAFT_TRADE){
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

							<th class="bottom"> remove </th>
							<th class="bottom"> edit </th>
						</tr>
					</thead>
					<tbody>
<?php
	$tablestate = "row_odd";
	$counter = 0;
	foreach($watchlists[$index] as $watch){
		$counter ++;
		$id = $watch['watchlist_id'];
		$zoneStr = ($watch['watchlist_is_zoned']) ? "  (ZONED)" : "";
		echo '<input type="hidden" name="'.$counter.'" value="'.$id.'"/>' . "\n";
		echo '<tr class="table_row '. $tablestate. ' ">'. "\n";
		echo '<td class="left">' . strtoupper($watch['ticker_symbol']) . $zoneStr . '</td>'. "\n";
		echo '<td>' . number_format($watch['last'], 2) . '</td>'. "\n";
		echo '<td>' . number_format($watch['watchlist_low'], 2) . '</td>'. "\n";
		echo '<td>' . number_format($watch['watchlist_high'], 2) . '</td>'. "\n";
		if ($index == BREAKOUT_TRADE || $index == BREAKDOWN_TRADE){
			echo '<td>' . number_format($watch['watchlist_top'], 2) . '</td>'. "\n";
			echo '<td>' . number_format($watch['watchlist_target3'], 2) . '</td>'. "\n";
		}else if ($index == PULLBACK_TRADE || $index == BACKDRAFT_TRADE){
			echo '<td>' . number_format($watch['watchlist_top'], 2) . '</td>'. "\n";
			echo '<td>' . number_format($watch['watchlist_bottom'], 2) . '</td>'. "\n";	
			echo '<td>' . number_format($watch['watchlist_target1'], 2) . '</td>'. "\n";
		} else {
			echo '<td>' . number_format($watch['watchlist_top'], 2) . '</td>'. "\n";
			echo '<td>' . number_format($watch['watchlist_bottom'], 2) . '</td>'. "\n";	
			echo '<td>' . number_format($watch['watchlist_target1'], 2) . '</td>'. "\n";
			echo '<td>' . number_format($watch['watchlist_target2'], 2) . '</td>'. "\n";
			echo '<td>' . number_format($watch['watchlist_target3'], 2) . '</td>'. "\n";
		}	
		echo '<td><a class="delete_link" href="?delete_id='. $id . '">remove</a></td>'. "\n";
		echo '<td><a class="edit_link" href="admin.editwatch.php?edit_id='. $id . '">edit</a></td>'. "\n";
		echo '</tr>'. "\n";
		if ($tablestate=='row_odd') $tablestate = 'row_even';
		else $tablestate = 'row_odd';
	}

	echo '</tr>'. "\n";

?>					
						
					</tbody>
				</table>

			<div class="editAll <?php echo GetTradeTypeConstantName($index); ?>">
				<a href="admin.editwatchentries.php?tt=<?php echo $index; ?>" class="buttonLook editLink">EDIT ALL <?php echo GetTradeTypeConstantName($index); ?></a>
			</div>
		</div>
<?php } ?>
	</div>
</div>
<div class='emptyBottom'></div>

<script src="js/navBar.js" type="text/javascript"></script>
<script src="js/pageInit.js" type="text/javascript"></script>
<script src="js/admin.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
});	


</script>

</body>
</html>

<?php
	//print_r($_GET);
	//print_r($_POST);

?>