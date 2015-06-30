<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	session_start();
	
	if (!isset($_SESSION['userid'])){
		header('Location: /');
	}
	$username = "UserName";
	$user = new user();
	$uid = $_SESSION['userid'];
	$user->set_variable('users_id', $uid);
	$userCreationDate;
	if ($user->load()){
		$username = $user->get_variable('users_username');
		$userCreationDate = $user->get_variable('users_creationdate');
	}
	$admin = new admins();
	$admin->set_variable('admin_user_id', $uid);
	$isAdmin = false;
	if ($admin->load()){
		$isAdmin = true;
	}
	
	if (isset($_GET['lo'])){
		session_destroy();
		header('Location: /');
	}

	$showClass = "";
	$trialTimeClass = "none";
	$showData = true;
	$paymentDates = payment_info::getPaymentDates($uid);
	$trialMessage = "";
	$lastPaymentDate = "";
	$nextPaymentDate = "";
	$useSubscriptionText = true;
	
	$expirationInfo = user::getUserExpirationDate($uid);
	$expDate = new DateTime($expirationInfo['date']);
	$now = new DateTime(date("Y-m-d"));
	$diff = intval($now->diff($expDate)->format("%r%a"));
	$nextPaymentDate = $expDate->format("F dS, Y");
	$isTrial = $expirationInfo['type'] == user::EXP_TYPE_TRIAL;
	if ($isTrial){
		$useTrialEnd = true;
		if ($diff < 0){
			$trialTimeClass = "";
			$trialMessage = "**** YOUR TRIAL MEMBERSHIP HAS EXPIRED! ****";
			$showData = false;
			$showClass = "none";
		} else if ($diff <= 15) {
			$trialTimeClass = "";
			if ($diff == 0)
				$trialMessage = "**** TRIAL MEMBERSHIP EXPIRES TODAY! ****";
			else
				$trialMessage = "**** TRIAL MEMBERSHIP EXPIRES IN ". $diff . " DAYS! ****";
		}		
	} else {
		if ($diff >= -3 ){
			$useSubscriptionText = false;
		} else {
			$trialTimeClass = "";
			$trialMessage = "**** YOUR SUBSCRIPTION HAS EXPIRED! ****";
			$showData = false;
			$showClass = "none";
		}
	}
	
	$watchlist = ticker_group_info::retrieveWatchlistArray(SHORT_TRADE);
	$holdings = ticker_group_info::retrieveHoldingsArray(SHORT_TRADE);
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
<link href="css/responsive_shorts.css" rel="stylesheet" type="text/css">
<link href="css/jquery.qtip.min.css" rel="stylesheet" type="text/css">
<link href="css/uniform.aristo.min.css" rel="stylesheet" type="text/css">

<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>

</head>

<body class="shortList">
<!-- Panel -->
<?php include 'navBar1.php'; ?>
<?php include 'navBar2.php'; ?>

<div class = "newwrap">
	<div class="section n0 <?php echo $trialTimeClass; ?>" >
		<div id="trialMessageTop"></div >
		<div class="desc trialMessage">
			<?php echo $trialMessage; ?> 
		</div>
	</div>
</div>
<div class = "newwrap small_bottom_margin">
	<div class="section n0 <?php echo $showClass; ?>">
		<div id="maintitle">
			Watchlist
		</div >
		<div class="tablegroup">
			<table id="watchTable" class="watchtable" cellspacing="0" cellpadding ="0" border="0">
				<thead>
					<tr class="table_head">
						<td class="new"></td>
						<th class="bottom left"><span class="hastooltip" title="Ticker Symbol">Symbol</span><div class="none">The ticker symbol of a BioBounce stock to watch.</div></th>
						<th class="bottom"><span class="hastooltip" title="Last Price">Last</span><div class="none">The most recent sell price.</div></th>
						<th class="bottom"><span class="hastooltip" title="Percent Change">Today</span><div class="none">The percent change of this ticker today.</div></th>
						<th class="bottom"><span class="hastooltip" title="Entry Price">Short Price</span><div class="none">The short price specifies the ideal price to short this stock.</div></th>
						<th class="bottom"><span class="hastooltip" title="Percent From Entry">% from Entry</span><div class="none">The percentage difference from the last sell price and the entry price.</div></th>
						<th class="bottom"><span class="hastooltip" title="First Target">Target 1</span><div class="none">The First BioBounce Target. This is the first buy to cover point should this stock climb past the entry price.</div></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
<?php
if ($showData){
	$tablestate = "row_odd";
	foreach($watchlist as $watch){
		$id = $watch['watchlist_id'];
		$highlights = highlights::getWatchlistHighlights($id);
		$fromZone = 100 * (($watch['watchlist_bottom'] - $watch['last'])/$watch['last']);
		$isZoned = $watch['watchlist_is_zoned'];
		$t1 = $watch['watchlist_target1'];
		$zonedString = '';
		if (!empty($isZoned)){
			$tablestate .= " zoned ";
			$zonedString = " <span class='zonedText'>(SHORTED)</span>";
		}
		echo '
		<tr class="table_row '. $tablestate. ' ' . $highlights[W_ROW]. ' ' . $highlights[W_ROW_DELETE] . '">';
		$symbol = strtoupper($watch['ticker_symbol']); 
		
		$percent = floatval($watch['change_today_percent']);
		$changeClass = "";
		if ($percent > 0 ) $changeClass = "percent_plus";
		if ($percent < 0 ) $changeClass = "percent_minus";
		$zoneClass = "zone_none";
		if ($fromZone < 10 ) $zoneClass = "zone_ten";
		if ($fromZone < 5 ) $zoneClass = "zone_five";
		if ($highlights[W_ROW_DELETE] != "")
			echo '	<td class="new_'.$highlights[W_ROW_DELETE].'"></td>';
		else if ($highlights[W_ROW] != "")
			echo '	<td class="new_'.$highlights[W_ROW].'"></td>';
		else 
			echo '	<td></td>';
			
			
		$hasToolTipString = "";	
		if (strlen($watch['watchlist_tooltip'])>0){
			$hasToolTipString = "hastooltip";
		} 			
		echo '
			<td class="left"><a class="stocklink '. $hasToolTipString.'" target="_blank" href="https://www.google.com/finance?q=' . $symbol . '" >' . $symbol . '</a><div class="none">'. $watch["watchlist_tooltip"] .'</div>' . $zonedString. '</td>		
			<td>' . number_format($watch['last'], 2) . '</td>
			<td class="'.$changeClass.'">' . $watch['change_today_percent'] . '%</td>
			<td class="zonecolumn"><div class="'. $highlights[W_ENTRY_ZONE] .'"><span class="topzone">' . number_format($watch['watchlist_bottom'], 2) . '</span></div></td>
			<td class="'.$zoneClass.'">' . number_format($fromZone, 2). '%</td>
			<td>' . number_format($t1, 2). '</td>
			<td></td>
		</tr>';
		if ($tablestate=='row_odd') $tablestate = 'row_even';
		else $tablestate = 'row_odd';
	}
}
?>					
				</tbody>
			</table>
		</div>

	</div>
</div>
<div class = "newwrap">
	<div class="section n2 <?php echo $showClass; ?>">
		<div class="title1">
			Holdings
		</div >
		<div class="tablegroup">
			<table id="holdingsTable" class="holdingstable" cellspacing="0" cellpadding ="0" border="0">
				<thead>
					<tr class="table_head">
						<th class="bottom lowpriority"> <span class="hastooltip" title="Is Owned">Own</span><div class="none">Check the boxes of the stocks you have a position in.</div></th>
						<th class="bottom lowpriority"> <span class="hastooltip" title="Entry Date">Entry Date</span><div class="none">The date that this stock climbed above the entry price.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Ticker Symbol">Symbol </span><div class="none">The ticker symbol of this BioBounce holding.</div></th>
						<th class="bottom lowpriority"> <span class="hastooltip" title="Percent Change">Today </span><div class="none">The percent change of this ticker today.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Last Price">Last </span><div class="none">The most recent sell price.</div></th>
						<th class="bottom"> <span class="hastooltip" title="First Target">Target 1 </span><div class="none">The First BioBounce Target. Cover 33% of your holdings at this price.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Second Target">Target 2 </span><div class="none">The Second BioBounce Target. Cover 33% of your original holdings at this price.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Third Target">Target 3 </span><div class="none">The Third BioBounce Target.  Buy to cover the rest of your original holdings at this price..</div></th>
						<th class="bottom"> <span class="hastooltip" title="Abandon Price">Abandon Price</span><div class="none">The  end of day price limit. Cover the next day if this stock CLOSES above this price.</div><div class="note">(cover short if closes above)</div></th>
						<th class="bottom lowpriority"> <span class="hastooltip" title="Entry Price">Entry Price</span><div class="none">The price when this stock was sold short.</div> </th>
						<th class="bottom "> <span class="hastooltip" title="BioBounce Return">Return</span><div class="none">The current return percent of this stock based on the original price.</div> </th>
						<th class="bottom"> <span class="hastooltip" title="Last Action Taken">Last Action</span><div class="none">The last BioBounce action that was taken.</div> </th>
					</tr>
				</thead>
				<tbody>
<?php
if ($showData){
	
	$tablestate = "row_odd";
	$someHoldingHidden = false;
	foreach($holdings as $holding){
		$hid = $holding['holdings_id'];
		$highlights = highlights::getHoldingsHighlights($hid);		
		$last = $holding['last'] ;
		$lastAction = "NONE";
		$abandonClass ="";
		switch ($holding['holdings_last_action']){
			case BUY:
				$lastAction = "SELL SHORT";
				break;
			case SELL1:
				$lastAction = "COVER 1";
				break;
			case SELL2:
				$lastAction = "COVER 2";
				break;
			case SELL3:
				$lastAction = "COVER 3";
				break;
			case ABANDON:
				$abandonClass = " abandonRow ";
				$lastAction = "ABANDON";
				break;
			case ABANDON_AT_CLOSE:
				$abandonClass = " abandonRow ";
				$lastAction = "ABANDON AT CLOSE";
				break;
			case WARNING:
				$lastAction = "WARNING";
				break;
			case STOCH_BUY:
				$lastAction = "BUY";
				break;			
			case NONE:
				$lastAction = "NONE";
				break;
			default:
				$lastAction = "WARNING";
				break;
		}
		
		$hasToolTipString = "";	
		if (strlen($holding['holdings_tooltip'])>0){
			$hasToolTipString = "hastooltip";
		} 
		
		$iAmHolding = personal_holdings::iAmHolding($uid, $hid);
		if (!$iAmHolding) $someHoldingHidden = true;
		
		$checkedString = $iAmHolding ? "checked" : "";
		$noneString = $iAmHolding ? "" : " none ";
		$return_percent = -100* (($last -  $holding['holdings_top_price'])/$holding['holdings_top_price'] );
		echo '<tr class="table_row holdings_row '. $tablestate. $abandonClass. $noneString . ' ">';
		echo '<td class="lowpriority">
			<div class="squaredOne">
				<input type="checkbox" value="None" class="personalHolding" name="'.$hid.'" '.$checkedString.' />
				<label for="squaredOne"></label>
			</div>
		</td>';
		echo '<td class="lowpriority">' . date ("Y-m-d", strtotime($holding['holdings_orig_date'])) . '</td>';
		$symbol = strtoupper($holding['ticker_symbol']);
		echo '<td><a class="stocklink '. $hasToolTipString.'" target="_blank" href="https://www.google.com/finance?q=' . $symbol . '" >' . $symbol . '</a> <div class="none">'. $holding["holdings_tooltip"] .'</div></td>';
		$percent = floatval($holding['change_today_percent']);
		$changeClass = "";
		if ($percent > 0 ) $changeClass = "percent_plus";
		if ($percent < 0 ) $changeClass = "percent_minus";
		echo '<td class="lowpriority '. $changeClass .'">' . $holding['change_today_percent'] . '%</td>';
		echo '<td>' . number_format($holding['last'], 2) . '</td>';
		if (!empty($holding['holdings_t1_marked'])){
			echo '<td class="'. $highlights[H_T1] . ' checked">';
			echo number_format($holding['holdings_t1'], 2) ;
			echo '<img class=\'checkmark\' src=\'images/checkmark.png\'/>';
			echo '</td>';
		}
		else
			echo '<td>' . number_format($holding['holdings_t1'], 2) . '</td>';
		if (!empty($holding['holdings_t2_marked'])){
			echo '<td class="'. $highlights[H_T2] . ' checked">';
			echo number_format($holding['holdings_t2'], 2) ;
			echo '<img class=\'checkmark\' src=\'images/checkmark.png\'/>';
			echo '</td>';
		}
		else
			echo '<td>' . number_format($holding['holdings_t2'], 2) . '</td>';
		if (!empty($holding['holdings_t3_marked'])){
			echo '<td class="'. $highlights[H_T3] . ' checked">';
			echo number_format($holding['holdings_t3'], 2) ;
			echo '<img class=\'checkmark\' src=\'images/checkmark.png\'/>';
			echo '</td>';
		}
		else
			echo '<td>' . number_format($holding['holdings_t3'], 2) . '</td>';
			
		if ($lastAction == "ABANDON"){
			echo '<td class="abandon">';
			echo number_format($holding['holdings_stop_price'], 2);
			echo '<img class=\'checkmark\' src=\'images/error.png\'/>';
			echo '</td>';
		} else {
			
 			echo '<td class="'.$highlights[H_ABANDON].'">' . number_format($holding['holdings_stop_price'], 2) . '</td>';
		}
		echo '<td class="lowpriority">' . number_format($holding['holdings_top_price'], 2) . '</td>';
		echo '<td class="">' . number_format($return_percent, 2) . '%</td>';
		echo '<td>' . $lastAction . '</td>';
		echo '</tr>';
		if ($tablestate=='row_odd') $tablestate = 'row_even';
		else $tablestate = 'row_odd';
	}
}
?>					
				</tbody>
			</table>
			<?php
				if ($someHoldingHidden)
					echo "<div class='showall'><a >SHOW ALL HOLDINGS</a></div>";
			?>
			
		</div>

	</div>
</div>


<div class = "newwrap">
	<div class="section n3 <?php echo $showClass; ?>">
		<div class="title1">
			Previous Positions 
		</div >
		<div class="tablegroup">
			<div class="monthSelect" id="monthSelect">
				<select>
				</select>
			</div>

			<table id="holdingsTable" class="holdingstable" cellspacing="0" cellpadding ="0" border="0">
				<thead>
					<tr class="table_head">
						<th class="bottom lowpriority"> <span class="hastooltip" title="Entry Date">Entry Date</span><div class="none">The date that this stock climbed above the entry price.</div></th>
						<th class="bottom lowpriority">Abandon Date</th>
						<th class="bottom"> <span class="hastooltip" title="Ticker Symbol">Symbol </span><div class="none">The ticker symbol of this BioBounce holding.</div></th>
						<th class="bottom"> <span class="hastooltip" title="First Target">Target 1 </span><div class="none">The First BioBounce Target. Buy to cover 33% of your holdings at this price.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Second Target">Target 2 </span><div class="none">The Second BioBounce Target. Buy to cover 33% of your original holdings at this price.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Third Target">Target 3 </span><div class="none">The Third BioBounce Target. Buy to cover the rest of your original holdings at this price.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Abandon Price">Abandon Price</span><div class="none">The end of day price limit. Buy to cover the next day if this stock CLOSES below this price.</div><div class="note">(cover short if closes above)</div></th>
						<th class="bottom"> <span class="hastooltip" title="Entry Price">Entry Price</span><div class="none">The price when this stock was shorted.</div> </th>
						<th class="bottom"> <span class="hastooltip" title="BioBounce Return">Return</span><div class="none">The current return percent of this stock based on the original price.</div> </th>
					</tr>
				</thead>
<?php
	$abandon = ticker_group_info::retrieveAbandonArray(SHORT_TRADE);

	$tablestate = "row_odd";
	$prevMonth = "";
	$allMonths = array();
	$first=true;
	foreach($abandon as $holding){
		$hid = $holding['holdings_id'];
		$counter = 0;
		$sum = 0;
		if ($holding['holdings_t1_marked']==1) {
			$counter++;
			$sum += $holding['holdings_t1'];
		} 
		if ($holding['holdings_t2_marked']==1) {
			$counter++;
			$sum += $holding['holdings_t2'];
		} 
		if ($holding['holdings_t3_marked']==1) {
			$counter++;
			$sum += $holding['holdings_t3'];
		} 

		//$sum += (4-$counter) * $holding['holdings_stop_price'];
		///$return_percent = -100* (($last -  $holding['holdings_top_price'])/$holding['holdings_top_price'] );

		//$return_percent = -100* (($sum  - $orig)/$orig);

		$sum += (3-$counter) * $holding['holdings_stop_price'];
		$orig = 3*$holding['holdings_top_price'];
		$return_percent = -100* (($sum  - $orig)/$orig);
		
		// process months into array for select
		$abandonMonth = date ("F Y", strtotime($holding['holdings_abandon_date'])); 
		if ($abandonMonth != $prevMonth){
			$prevMonth = $abandonMonth;
			$allMonths[] = $abandonMonth;
			if (!$first) {
				echo "</tbody>";
				$first = false;
			}
			echo "<tbody id=\"month_".str_replace(" ", "", $abandonMonth) ."\" class=\"monthTable\">";
		}
		

		echo '<tr class="table_row '. $tablestate. ' ">';
		echo '<td class="lowpriority">' . date ("Y-m-d", strtotime($holding['holdings_orig_date'])) . '</td>';
		echo '<td class="lowpriority">' . date ("Y-m-d", strtotime($holding['holdings_abandon_date'])) . '</td>';
		$symbol = strtoupper($holding['holdings_ticker_symbol']);
		echo '<td><a class="stocklink" target="_blank" href="https://www.google.com/finance?q=' . $symbol . '" >' . $symbol . '</a></td>';

		if (!empty($holding['holdings_t1_marked'])){
			echo '<td class=" checked">';
			echo number_format($holding['holdings_t1'], 2) ;
			echo '<img class=\'checkmark\' src=\'images/checkmark.png\'/>';
			echo '</td>';
		}
		else
			echo '<td>' . number_format($holding['holdings_t1'], 2) . '</td>';
		if (!empty($holding['holdings_t2_marked'])){
			echo '<td class=" checked">';
			echo number_format($holding['holdings_t2'], 2) ;
			echo '<img class=\'checkmark\' src=\'images/checkmark.png\'/>';
			echo '</td>';
		}
		else
			echo '<td>' . number_format($holding['holdings_t2'], 2) . '</td>';
		if (!empty($holding['holdings_t3_marked'])){
			echo '<td class="checked">';
			echo number_format($holding['holdings_t3'], 2) ;
			echo '<img class=\'checkmark\' src=\'images/checkmark.png\'/>';
			echo '</td>';
	 		echo '<td>-</td>';
		}
		else{
			echo '<td>' . number_format($holding['holdings_t3'], 2) . '</td>';
	 		echo '<td>' . number_format($holding['holdings_stop_price'], 2) . '</td>';
			
		}

			
		echo '<td class="">' . number_format($holding['holdings_top_price'], 2) . '</td>';
		echo '<td class="">' . number_format($return_percent, 2) . '%</td>';
		echo '</tr>';
		if ($tablestate=='row_odd') $tablestate = 'row_even';
		else $tablestate = 'row_odd';
	}
?>					
				</tbody>
			</table>
		</div>
	</div>
</div>

<div id="actions" class = "newwrap">
	<div class="section n4">

		<div class="title1">
			Short Actions
		</div >
		<div class="desc">
			<ul class="ruleslist">
				<li>SELL SHORT = Sell stock short when it climbs above the entry price indicated.</li>
				<li>ABANDON    = Cover the next day, if stock CLOSES above abandon price.</li>
				<li>ABANDON AT CLOSE= Cover before the end of the day regardless of price.</li>
				<li>COVER 1    = Cover 33% of your holdings when the price reaches target 1.</li>
				<li>COVER 2    = Cover 33% of your holdings when the price reaches target 2.</li>
				<li>COVER 3    = Cover the rest of your original holdings when the price reaches target 3.</li>
				<li>WARNING    = If stock rises above abandon price intra-day a warning is issued.</li>
			</ul>
		</div>
		<!-- BEGIN: Constant Contact Archive Homepage link -->
<div class="cc_archive">
<div align="center">
<table border="0" cellspacing="0" cellpadding="1" bgcolor="#999999"><tr><td>
<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td style="padding:3px 1px 3px 8px;" bgcolor="#FFFFFF"><table bgcolor="#0066CC" border="0" cellpadding="0" cellspacing="0"><tr><td><a target="_blank" href="http://archive.constantcontact.com/fs123/1114973701558/archive/1116308031364.html" rel="nofollow"><img src="https://imgssl.constantcontact.com/ui/images1/archive_icon_arrow.gif" border="0" width="8" height="9"/></a></td></tr></table></td>
<td style="padding:3px 8px 3px 0px;" bgcolor="#FFFFFF" nowrap="nowrap"><a target="_blank" href="http://archive.constantcontact.com/fs123/1114973701558/archive/1116308031364.html" style="font-family:Arial,Helvetica,sans-serif;font-size:10pt;color:#000000;text-decoration:none;"><i>View our</i></a></td>
<td style="padding:3px 8px 3px 8px;" bgcolor="#0066CC" nowrap="nowrap"><a target="_blank" href="http://archive.constantcontact.com/fs123/1114973701558/archive/1116308031364.html" style="font-family:Arial,Helvetica,sans-serif;font-size:11pt;color:#FFFFFF;text-decoration:none;"><strong>ARCHIVE<strong></a></td>
</tr>
</table>
</td></tr></table>
</div>
<div align="center" style="padding-top:5px;font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#999999;"><a target="_blank" href="http://www.constantcontact.com/index.jsp?cc=WidgNatArchLink" style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#999999;text-decoration:none;">Email Marketing</a> by<a target="_blank" href="http://www.constantcontact.com/index.jsp?cc=WidgNatArchLink" style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#999999;text-decoration:none;" rel="nofollow"> <strong>Constant Contact</strong></a>&reg;
</div>
</div>
<!-- END: Constant Contact Archive Homepage link -->

	</div>
</div>




<div class="disclaimer">
	<div class="disclaimer_inner">
		<div class="disclaimer_text">
AlgoSniffer, LLC is the Publisher of BioBounce.com and @BioBounceAlerts.  AlgoSniffer, LLC is not a Registered Investment Advisor.
</br></br>
BioBounce.com and @BioBounceAlerts are for informational purposes only and AlgoSniffer, LLC accepts no responsibility for any monetary losses incurred from use of the information provided.
</br></br>
Biotechnology is a highly volatile sector, and presents higher than average risk.  As a subscriber of our service, you are responsible for your own due diligence regarding any and all tickers we provide on our watchlist.
</br></br>
General Information on the Regulation of Investment Advisers</br>
Division of Investment Management</br>
Exclusions From the Definition</br>

Publishers of bona fide newspapers, news magazines, and business or financial publications of general and regular circulation. Under a decision of the United States Supreme Court, to enable a publisher to qualify for this exclusion, a publication must satisfy three elements: (1) the publication must offer only impersonal advice, i.e., advice not tailored to the individual needs of a specific client, group of clients, or portfolio; (2) the publication must be "bona fide," containing disinterested commentary and analysis rather than promotional material disseminated by someone touting particular securities, advertised lists of stocks "sure to go up," or information distributed as an incident to personalized investment services; and (3) the publication must be of general and regular circulation rather than issued from time to time in response to episodic market activity or events affecting the securities industry. See Lowe v. Securities and Exchange Commission, 472 U.S. 181 (1985).
</br>
</br>
source: http://www.sec.gov/divisions/investment/iaregulation/memoia.htm
		</div>
	</div>
</div>


<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script> 
<script type="text/javascript" src="js/jquery.qtip.min.js"></script> 
<script type="text/javascript" src="js/jquery.uniform.min.js"></script> 


<script type="text/javascript">
$(document).ready(function() {
<?php	
	echo "var items={";
	foreach ($allMonths as $month){
		echo str_replace(" ", "", $month) . ":'" . $month . "',";
	}
	echo "};";
?>


	$.each(items, function(key, value) {   
	     $('#monthSelect select')
	          .append($('<option>', { value : key })
	          .text(value)); 
	});

});
</script>
<script src="js/slide.js" type="text/javascript"></script>
<script src="js/pageInit.js" type="text/javascript"></script>

</body>
</html>