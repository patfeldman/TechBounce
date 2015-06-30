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
	$showToAdminClass = '';
	if ($admin->load()){
		$isAdmin = true;
		$showToAdminClass = '';
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
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/navBar.js" type="text/javascript"></script>

<link href="css/biobounce.css" rel="stylesheet" type="text/css">
<link href="css/biomembers.css" rel="stylesheet" type="text/css">
<link href="css/RESPONSIVE.css" rel="stylesheet" type="text/css">
<link href="css/responsive_history.css" rel="stylesheet" type="text/css">
<link href="css/jquery.qtip.min.css" rel="stylesheet" type="text/css">
<link href="css/uniform.aristo.min.css" rel="stylesheet" type="text/css">

<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>

</head>

<body id="HistoryPage">
<!-- Panel -->
<?php include 'navBar1.php'; ?>
<?php include 'navBarHistory.php'; ?>

<div class = "newwrap">
	<div class="section n0 <?php echo $trialTimeClass; ?>" >
		<div id="trialMessageTop"></div >
		<div class="desc trialMessage">
			<?php echo $trialMessage; ?> 
		</div>
	</div>
</div>

<div class = "newwrap">
	<div class="section n0 n1">
		<div id="maintitle" class="title1">
			Historical Charts
		</div >
		<div  class="historyNote">
			(all valuations are based on a $5000.00 per trade) 
		</div >
		<div id="HistoryCharts" class="desc trialMessage"></div>
		<div class="summary <?php echo $showToAdminClass; ?>">
			<div id="MonthSummary" class="summaryHeader summaryDiv">
				<div id="SummaryTitle" class="summaryHeaderTitle">Summary for</div> 
				<div class="monthSelect " id="monthSelect">
					<select>
					</select>
				</div>
				
			</div>
			
			<div id="PullbackSummary" class="summaryDiv"><div class="summaryTitle">Pullbacks Total:</div><div class="summaryData"></div><div class="clearer"></div></div>
			<div id="ReversalSummary" class="summaryDiv"><div class="summaryTitle">Reversals Total:</div><div class="summaryData"></div><div class="clearer"></div></div>
			<div id="ShortSummary" class="summaryDiv"><div class="summaryTitle">Shorts Total:</div><div class="summaryData"></div><div class="clearer"></div></div>
			<div id="TotalSummary" class="summaryDiv"><div class="summaryTitle">Combination of Strategies:</div><div class="summaryData"></div><div class="clearer"></div></div>
			<div class="clearer"></div>
		</div>

		<div class="tablegroup <?php echo $showToAdminClass; ?>">

			<table id="holdingsTable" class="holdingstable" cellspacing="0" cellpadding ="0" border="0">
				<thead>
					<tr class="table_head">
						<th class="bottom lowpriority"> <span class="hastooltip" title="Zone Date">Orig Date</span><div class="none">The date that this stock fell into the zone.</div></th>
						<th class="bottom lowpriority">Abandon Date</th>
						<th class="bottom"> <span class="hastooltip" title="Ticker Symbol">Symbol </span><div class="none">The ticker symbol of this BioBounce holding.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Trade Type">Type</span><div class="none">The type of trade for this BioBounce holding.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Original Price">Orig Price</span><div class="none">The best possible price point that this stock was purchased.</div> </th>
						<th class="bottom"> <span class="hastooltip" title="Abandon Price">Sell Price</span><div class="none">The price that this stock was sold/covered.</div></th>
						<th class="bottom"> <span class="hastooltip" title="BioBounce Return">Return</span><div class="none">The current return percent of this stock based on the original price.</div> </th>
						<th class="bottom"> <span class="hastooltip" title="Valuation">Valuation</span><div class="none">The amount a $5000 investment returned.</div> </th>
					</tr>
				</thead>
				<tbody id="ChartTableBody"></tbody>
				
<?php
	$abandon = ticker_group_info::retrieveAllAbandonArray();

	$tablestate = "row_odd";
	$prevMonth = "";
	$allMonths = array();
	$sumMonths = array();
	$tablesByMonth = array();
	$first=true;
	foreach($abandon as $holding){
		$hid = $holding['holdings_id'];
		$tradeType = $holding['holdings_tradetype'];
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

		switch( $tradeType){
			case LONG_TRADE:
				$tradeClass = "pullback";
				$tradeString = "Pullback";
				$sum += (4-$counter) * $holding['holdings_stop_price'];
				$orig = 4*$holding['holdings_top_price'];
				$return_percent = 100* (($sum  - $orig)/$orig);
				break;
			case SHORT_TRADE:
				$tradeClass = "short";
				$tradeString = "Short";
				$sum += (3-$counter) * $holding['holdings_stop_price'];
				$orig = 3*$holding['holdings_orig_price'];
				$return_percent = -100* (($sum  - $orig)/$orig);
				break;
			case REVERSAL_TRADE:			
				$tradeClass = "reversal";
				$tradeString = "Reversal";
				$sum += (3-$counter) * $holding['holdings_stop_price'];
				$orig = 3*$holding['holdings_top_price'];
				$return_percent = 100* (($sum  - $orig)/$orig);		
				break;
		}
		$valuation = (5000 * $return_percent/100) ;

		
		// process months into array for select
		$abandonMonth = date ("F Y", strtotime($holding['holdings_abandon_date'])); 
		if ($abandonMonth != $prevMonth){
			$prevMonth = $abandonMonth;
			$allMonths[] = $abandonMonth;
			if (!$first) {
				//echo "</tbody>";
				$first = false;
			}
			//echo "<tbody id=\"month_".str_replace(" ", "", $abandonMonth) ."\" class=\"monthTable\">";
		}
		$monthKey = str_replace(" ", "", $abandonMonth);
		$monthArray = array();
		$prevTotal = 0;
		$typeTotal = 0;
		if (array_key_exists($monthKey, $sumMonths)){
			$monthArray = $sumMonths[$monthKey];
			$prevTotal = $monthArray["total"];
			if (array_key_exists($tradeType, $monthArray)){
				$typeTotal = $monthArray[$tradeType];	
			}		
		} else {
			$monthArray["0"] = 0;
			$monthArray["1"] = 0;
			$monthArray["2"] = 0;
			$monthArray["count0"] = 0;
			$monthArray["count1"] = 0;
			$monthArray["count2"] = 0;
		}
		$monthArray["monthString"] = $abandonMonth;
		$monthArray["total"] = $prevTotal + $valuation;
		$monthArray[$tradeType] = $typeTotal + $valuation;
		$monthArray["count" . $tradeType] += 1;
		
		$sumMonths[$monthKey] = $monthArray;
		if (!array_key_exists($monthKey, $tablesByMonth))$tablesByMonth[$monthKey] = "";
		$tablesByMonth[$monthKey] .= "<tr class='table_row ". $tradeClass. " '>";
		$tablesByMonth[$monthKey] .= "<td class='lowpriority'>" . date ('Y-m-d', strtotime($holding["holdings_orig_date"])) . "</td>";
		$tablesByMonth[$monthKey] .= "<td class='lowpriority'>" . date ('Y-m-d', strtotime($holding["holdings_abandon_date"])) . "</td>";
		$symbol = strtoupper($holding["holdings_ticker_symbol"]);
		$tablesByMonth[$monthKey] .= "<td><a class='stocklink' target='_blank' href='https://www.google.com/finance?q=" . $symbol . "' >" . $symbol . "</a></td>";

	
		$tablesByMonth[$monthKey] .= "<td class='".$tradeClass."'>" . $tradeString . "</td>";
			
		$tablesByMonth[$monthKey] .= "<td>" . number_format($holding["holdings_top_price"], 2) . "</td>";
 		$tablesByMonth[$monthKey] .= "<td>" . number_format($holding["holdings_stop_price"], 2) . "</td>";
		$tablesByMonth[$monthKey] .= "<td class=''>" . number_format($return_percent, 2) . "%</td>";
		$tablesByMonth[$monthKey] .= "<td class=''>$" . number_format($valuation, 2) . "</td>";
		$tablesByMonth[$monthKey] .= "</tr>";

/*		$tablesByMonth[$monthKey] = '<tr class="table_row '. $tradeClass. ' ">';
		$tablesByMonth[$monthKey] .= '<td class="lowpriority">' . date ("Y-m-d", strtotime($holding['holdings_orig_date'])) . '</td>';
		$tablesByMonth[$monthKey] .= '<td class="lowpriority">' . date ("Y-m-d", strtotime($holding['holdings_abandon_date'])) . '</td>';
		$symbol = strtoupper($holding['holdings_ticker_symbol']);
		$tablesByMonth[$monthKey] .= '<td><a class="stocklink" target="_blank" href="https://www.google.com/finance?q=' . $symbol . '" >' . $symbol . '</a></td>';

	
		$tablesByMonth[$monthKey] .= '<td class="'.$tradeClass.'">' . $tradeString . '</td>';
			
		$tablesByMonth[$monthKey] .= '<td>' . number_format($holding['holdings_top_price'], 2) . '</td>';
 		$tablesByMonth[$monthKey] .= '<td>' . number_format($holding['holdings_stop_price'], 2) . '</td>';
		$tablesByMonth[$monthKey] .= '<td class="">' . number_format($return_percent, 2) . '%</td>';
		$tablesByMonth[$monthKey] .= '<td class="">$' . number_format($valuation, 2) . '</td>';
		$tablesByMonth[$monthKey] .= '</tr>';
 * 
 */
		if ($tablestate=='row_odd') $tablestate = 'row_even';
		else $tablestate = 'row_odd';
	}
?>					
			</table>
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
<!-- CHARTS JAVASCRIPT -->
<script src="http://code.highcharts.com/highcharts.js"></script>


<script src="js/slide.js" type="text/javascript"></script>
<script src="js/jquery.formatCurrency-1.4.0.min.js" type="text/javascript"></script>

<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script> 
<script type="text/javascript" src="js/jquery.qtip.min.js"></script> 
<script type="text/javascript" src="js/jquery.uniform.min.js"></script> 
<script type="text/javascript" src="js/jquery.mask.min.js"></script> 


<script type="text/javascript">
var items;
var totals;
var months;
var pullbacksByMonth;
var shortsByMonth;
var reversalsByMonth;
var totalsByMonth;
var tablesByMonth;
$(document).ready(function() {
<?php	
	$itemsJS = "items={";
	$monthsJS = "";
	$pullbacksJS = "";
	$shortsJS = "";
	$reversalsJS = "";
	$totalsJS = "";
	foreach ($allMonths as $month){
		$monthKey = str_replace(" ", "", $month) ;
		$itemsJS .= $monthKey . ":'" . $month . "',";
		$monthsJS = "'" . $month ."'," . $monthsJS;
		$pullbacksJS = "{y:" . $sumMonths[$monthKey][0] . ", numTrades:". $sumMonths[$monthKey]["count0"] ."}," . $pullbacksJS;
		$shortsJS = "{y:" . $sumMonths[$monthKey][1] . ", numTrades:". $sumMonths[$monthKey]["count1"] ."}," . $shortsJS;
		$reversalsJS ="{y:" . $sumMonths[$monthKey][2]. ", numTrades:". $sumMonths[$monthKey]["count2"] ."},"  . $reversalsJS;
		$totalsJS = $sumMonths[$monthKey]["total"]."," . $totalsJS;
	}
	$itemsJS .= "};\n\n";
	$monthsJS = "months=[" . $monthsJS . "];\n\n";
	$reversalsJS = "reversalsByMonth=[" . $reversalsJS. "];\n\n";
	$shortsJS = "shortsByMonth=[" . $shortsJS . "];\n\n";
	$pullbacksJS = "pullbacksByMonth=[" . $pullbacksJS . "];\n\n";
	$totalsJS = "totalsByMonth=[" . $totalsJS . "];\n\n";
	

	echo $itemsJS;
	echo $monthsJS;
	echo $reversalsJS;
	echo $shortsJS;
	echo $pullbacksJS;
	echo $totalsJS;
	echo "totals=" . json_encode($sumMonths) . ";";
	echo "tablesByMonth=" . json_encode($tablesByMonth) . ";";

?>


	$.each(items, function(key, value) {   
	     $('#monthSelect select')
	          .append($('<option>', { value : key })
	          .text(value)); 
	});
	
});
</script>
<script src="js/pageInit.js" type="text/javascript"></script>
<script type="text/javascript" src="js/historycharts.js"></script> 
</body>
</html>