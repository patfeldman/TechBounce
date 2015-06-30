<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	$login = new login();

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
<?php include 'navBarMain012715.php'; ?>
<?php include 'navBarHistory.php'; ?>

<div class = "newwrap">
	<div class="section n0 <?php echo $login->trialTimeClass; ?>" >
		<div id="trialMessageTop"></div >
		<div class="desc trialMessage">
			<?php echo $login->trialMessage; ?> 
		</div>
	</div>
</div>

<div class = "newwrap">
	<div class="section n0 n1">
		<div id="maintitle" class="title1">
			Historical Charts
		</div >
		<div class="allNotes">
			<div  class="historyNote">
				NOTES :: 1. Change strategies by clicking the legend to the right of the chart
			</div >
			<div  class="historyNote">
				2. All profits are based on a $5000.00 per trade
			</div >
		</div>
		<div id="HistoryCharts" class="desc trialMessage"></div>
		<div class="summary <?php echo $showToAdminClass; ?>">
			<div id="MonthSummary" class="summaryHeader summaryDiv">
				<div id="SummaryTitle" class="summaryHeaderTitle">Summary for</div> 
				<div class="monthSelect " id="monthSelect">
					<select>
					</select>
				</div>
				
			</div>
			
			<div id="BreakoutSummary" class="summaryDiv"><div class="summaryTitle">Breakouts Total:</div><div class="summaryData"></div><div class="clearer"></div></div>
			<div id="PullbackSummary" class="summaryDiv"><div class="summaryTitle">Pullbacks Total:</div><div class="summaryData"></div><div class="clearer"></div></div>
			<div id="BreakdownSummary" class="summaryDiv"><div class="summaryTitle">Breakdowns Total:</div><div class="summaryData"></div><div class="clearer"></div></div>
			<div id="BackdraftSummary" class="summaryDiv"><div class="summaryTitle">Backdrafts Total:</div><div class="summaryData"></div><div class="clearer"></div></div>
			<div id="TotalSummary" class="summaryDiv"><div class="summaryTitle">Combination of Strategies:</div><div class="summaryData"></div><div class="clearer"></div></div>
			<div class="clearer"></div>
		</div>

		<div class="tablegroup <?php echo $showToAdminClass; ?>">

			<table id="holdingsTable" class="holdingstable" cellspacing="0" cellpadding ="0" border="0">
				<thead>
					<tr class="table_head">
						<th class="bottom lowpriority"> <span class="hastooltip" title="Entry Date">Entry Date</span><div class="none">The date that this stock passed the entry price.</div></th>
						<th class="bottom lowpriority">Abandon Date</th>
						<th class="bottom"> <span class="hastooltip" title="Ticker Symbol">Symbol </span><div class="none">The ticker symbol of this BioBounce holding.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Trade Type">Type</span><div class="none">The type of trade for this BioBounce holding.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Entry Price">Entry Price</span><div class="none">The price that this stock was purchased/shorted.</div> </th>
						<th class="bottom"> <span class="hastooltip" title="Abandon Price">Sell Price</span><div class="none">The price that this stock was sold/covered.</div></th>
						<th class="bottom"> <span class="hastooltip" title="BioBounce Return">Return</span><div class="none">The current return percent of this stock based on the original price.</div> </th>
						<th class="bottom"> <span class="hastooltip" title="Profit">Profit</span><div class="none">The amount a $5000 investment returned.</div> </th>
					</tr>
				</thead>
				<tbody id="ChartTableBody"></tbody>
				
<?php
	$abandon = ticker_group_info::retrieveAllAbandonArray(11);

	$tablestate = "row_odd";
	$prevMonth = "";
	$allMonths = array();
	$sumMonths = array();
	$tablesByMonth = array();
	$first=true;
	foreach($abandon as $holding){
		$hid = $holding['holdings_id'];
		$tradeType = $holding['holdings_tradetype'];
		if ($tradeType == REVERSAL_TRADE) continue;
		$hitT1 = $holding['holdings_t1_marked'];
		$hitT2 = $holding['holdings_t2_marked'];
		$hitT3 = $holding['holdings_t3_marked'];
		$t1 = floatval($holding['holdings_t1']);
		$t2 = floatval($holding['holdings_t2']);
		$t3 = floatval($holding['holdings_t3']);
		$start = $holding['holdings_orig_price'];
		$abandon = $holding['holdings_stop_price'];			

		$return_percent = holdings::GetReturnPercent($tradeType, 0, $start, $t1, $t2, $t3, $hitT1, $hitT2, $hitT3, $abandon );
		
		$tradeClass = strtolower(GetTradeTypeConstantNameSingular($tradeType));
		$tradeString = GetTradeTypeConstantNameSingular($tradeType);
		$ti = GetTradeIndex($tradeType);
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
			if (array_key_exists($ti, $monthArray)){
				$typeTotal = $monthArray[$ti];	
			}		
		} else {
			$monthArray["0"] = 0;
			$monthArray["1"] = 0;
			$monthArray["2"] = 0;
			$monthArray["3"] = 0;
			$monthArray["4"] = 0;
			$monthArray["count0"] = 0;
			$monthArray["count1"] = 0;
			$monthArray["count2"] = 0;
			$monthArray["count3"] = 0;
			$monthArray["count4"] = 0;
		}
		$monthArray["monthString"] = $abandonMonth;
		$monthArray["total"] = $prevTotal + $valuation;
		$monthArray[$ti] = $typeTotal + $valuation;
		$monthArray["count" . $ti] += 1;
		
		$sumMonths[$monthKey] = $monthArray;
		if (!array_key_exists($monthKey, $tablesByMonth))$tablesByMonth[$monthKey] = "";
		$tablesByMonth[$monthKey] .= "<tr class='table_row ". $tradeClass. " '>";
		$tablesByMonth[$monthKey] .= "<td class='lowpriority'>" . date ('Y-m-d', strtotime($holding["holdings_orig_date"])) . "</td>";
		$tablesByMonth[$monthKey] .= "<td class='lowpriority'>" . date ('Y-m-d', strtotime($holding["holdings_abandon_date"])) . "</td>";
		$symbol = strtoupper($holding["holdings_ticker_symbol"]);
		$tablesByMonth[$monthKey] .= "<td><a class='stocklink' target='_blank' href='https://www.google.com/finance?q=" . $symbol . "' >" . $symbol . "</a></td>";

	
		$tablesByMonth[$monthKey] .= "<td class='".$tradeClass."'>" . $tradeString . "</td>";
			
		$tablesByMonth[$monthKey] .= "<td>" . number_format($start, 2) . "</td>";
 		$tablesByMonth[$monthKey] .= "<td>" . number_format($abandon, 2) . "</td>";
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
var breakoutsByMonth;
var breakdownsByMonth;
var totalsByMonth;
var tablesByMonth;
$(document).ready(function() {
<?php	
	$pullbackTI = GetTradeIndex(PULLBACK_TRADE);
	$breakoutTI = GetTradeIndex(BREAKOUT_TRADE);
	$breakdownTI = GetTradeIndex(BREAKDOWN_TRADE);
	$backdraftTI = GetTradeIndex(BACKDRAFT_TRADE);

	$itemsJS = "items={";
	$monthsJS = "";
	$pullbacksJS = "";
	$shortsJS = "";
	$breakoutsJS = "";
	$breakdownsJS = "";
	$totalsJS = "";
	foreach ($allMonths as $month){
		$monthKey = str_replace(" ", "", $month) ;
		$itemsJS .= $monthKey . ":'" . $month . "',";
		$monthsJS = "'" . $month ."'," . $monthsJS;
		$pullbacksJS = "{y:" . $sumMonths[$monthKey][$pullbackTI] . ", numTrades:". $sumMonths[$monthKey]["count".$pullbackTI] ."}," . $pullbacksJS;
		$shortsJS = "{y:" . $sumMonths[$monthKey][$backdraftTI] . ", numTrades:". $sumMonths[$monthKey]["count".$backdraftTI] ."}," . $shortsJS;
		$breakoutsJS ="{y:" . $sumMonths[$monthKey][$breakoutTI]. ", numTrades:". $sumMonths[$monthKey]["count".$breakoutTI] ."},"  . $breakoutsJS;
		$breakdownsJS ="{y:" . $sumMonths[$monthKey][$breakdownTI]. ", numTrades:". $sumMonths[$monthKey]["count".$breakdownTI] ."},"  . $breakdownsJS;
		$totalTrades = $sumMonths[$monthKey]["count".$pullbackTI] + $sumMonths[$monthKey]["count".$backdraftTI] + $sumMonths[$monthKey]["count".$breakdownTI]+ $sumMonths[$monthKey]["count".$breakoutTI];
		$totalsJS = "{y:" . $sumMonths[$monthKey]["total"].
			",numTrades:" . $totalTrades.
			",numPullbackTrades:" . $sumMonths[$monthKey]["count".$pullbackTI].
			",numShortTrades:" . $sumMonths[$monthKey]["count".$backdraftTI].
			",numBreakoutTrades:" . $sumMonths[$monthKey]["count".$breakoutTI].  
			",numBreakdownTrades:" . $sumMonths[$monthKey]["count".$breakdownTI].  
			"}," .
			$totalsJS;
	}
	$itemsJS .= "};\n\n";
	$monthsJS = "months=[" . $monthsJS . "];\n\n";
	$shortsJS = "shortsByMonth=[" . $shortsJS . "];\n\n";
	$pullbacksJS = "pullbacksByMonth=[" . $pullbacksJS . "];\n\n";
	$breakoutsJS = "breakoutsByMonth=[" . $breakoutsJS . "];\n\n";
	$breakdownsJS = "breakdownsByMonth=[" . $breakdownsJS . "];\n\n";
	$totalsJS = "totalsByMonth=[" . $totalsJS . "];\n\n";
	

	echo $itemsJS;
	echo $monthsJS;
	echo $shortsJS;
	echo $pullbacksJS;
	echo $breakoutsJS;
	echo $breakdownsJS;
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
<script type="text/javascript" src="js/historyCharts012715.js"></script> 
</body>
</html>