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
		$hitT1 = $holding['holdings_t1_marked'];
		$hitT2 = $holding['holdings_t2_marked'];
		$hitT3 = $holding['holdings_t3_marked'];
		$t1 = floatval($holding['holdings_t1']);
		$t2 = floatval($holding['holdings_t2']);
		$t3 = floatval($holding['holdings_t3']);
		$abandon = $holding['holdings_stop_price'];			
		$start = $holding['holdings_orig_price'];

		$return_percent = holdings::GetReturnPercent($tradeType, 0, $start, $t1, $t2, $t3, $hitT1, $hitT2, $hitT3, $abandon );

		$tradeString = GetTradeTypeConstantNameSingular($tradeType);
		$tradeClass = strtolower($tradeString);
		$ti = GetTradeIndex($tradeType);


		$valuation = (5000 * $return_percent/100) ;

		
		// process months into array for select
		$abandonMonth = date ("F Y", strtotime($holding['holdings_abandon_date'])); 
		if ($abandonMonth != $prevMonth){
			$prevMonth = $abandonMonth;
			$allMonths[] = $abandonMonth;
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
	}
?>					
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
		
		$totalsJS = $sumMonths[$monthKey]["total"]."," . $totalsJS;
	}
	$itemsJS .= "};\n\n";
	$monthsJS = "months=[" . $monthsJS . "];\n\n";
	$reversalsJS = "reversalsByMonth=[" . $reversalsJS. "];\n\n";
	$shortsJS = "shortsByMonth=[" . $shortsJS . "];\n\n";
	$pullbacksJS = "pullbacksByMonth=[" . $pullbacksJS . "];\n\n";
	$breakoutsJS = "breakoutsByMonth=[" . $breakoutsJS . "];\n\n";
	$breakdownsJS = "breakdownsByMonth=[" . $breakdownsJS . "];\n\n";
	
	$totalsJS = "totalsByMonth=[" . $totalsJS . "];\n\n";
	

	echo $itemsJS;
	echo $monthsJS;
	echo $reversalsJS;
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
<script src="http://code.highcharts.com/highcharts.js"></script>