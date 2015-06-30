<?php
	require_once('php/member.pagebase.php');

	$watchlist = ticker_group_info::retrieveWatchlistArray($tradeTypes);
	$holdings = ticker_group_info::retrieveHoldingsArray($tradeTypes);
	$abandons = ticker_group_info::retrieveAbandonArray($tradeTypes);
	$hasHoldingFirstElement = true;
	// in cases where there are multiple tradetypes, check to see if 
	// we are using the first trade type, which will be deprecated 
	// once all of the current holdings are the updated tradeType
	if (is_array($tradeTypes)){
		$hasHoldingFirstElement = ticker_group_info::HoldingThisTradeType($tradeTypes[0]);
	}
?>


<div class = "newwrap small_bottom_margin">
	<div class="section n1 <?php echo $login->showClass; ?>">
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
						<th class="bottom"><span class="hastooltip" title="Entry Price">Entry Price</span><div class="none"><?php echo $typeStrings->entryWatchDesc;?></div></th>
						<th class="bottom"><span class="hastooltip" title="Percent From Entry">% from Entry</span><div class="none">The percentage difference from the last sell price and the entry price.</div></th>
						<th class="bottom"><span class="hastooltip" title="First Target">Target</span><div class="none"><?php echo $typeStrings->targetDesc; ?></div></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
<?php
if ($login->showData){
	$tablestate = "row_odd";
	foreach($watchlist as $watch){
		$id = $watch['watchlist_id'];
		$highlights = highlights::getWatchlistHighlights($id);
		if ($watch['last']==0){
			$fromZone = 0;
		}else{
			if (in_array(SHORT_TRADE, $tradeTypes) || in_array(BACKDRAFT_TRADE, $tradeTypes) ){
				$fromZone = -100 * (($watch['last'] - $watch['watchlist_bottom'])/$watch['last']);				
			} else {
				$fromZone = 100 * (($watch['last'] - $watch['watchlist_top'])/$watch['last']);
			}
		}
		$isZoned = $watch['watchlist_is_zoned'];
		$t1 = $watch['watchlist_target1'];
		$zonedString = '';
		if (!empty($isZoned)){
			$tablestate .= " zoned ";
			$zonedString = " <span class='zonedText'>(BOUGHT)</span>";
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
		$entryPrice = (in_array(SHORT_TRADE, $tradeTypes) || in_array(BACKDRAFT_TRADE, $tradeTypes) ) ? number_format($watch['watchlist_bottom'], 2) : number_format($watch['watchlist_top'], 2);
		echo '
			<td class="left"><a class="stocklink '. $hasToolTipString.'" target="_blank" href="https://www.google.com/finance?q=' . $symbol . '" >' . $symbol . '</a><div class="none">'. $watch["watchlist_tooltip"] .'</div>' . $zonedString. '</td>
			<td>' . number_format($watch['last'], 2) . '</td>
			<td class="'.$changeClass.'">' . $watch['change_today_percent'] . '%</td>
			<td class="zonecolumn"><div class="zonePriceDiv '. $highlights[W_ENTRY_ZONE] .'"><span class="topzone">' . $entryPrice . '</span></div></td>
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
	<div class="section n2 <?php echo $login->showClass; ?>">
		<div class="title1">
			Holdings
		</div >
		<div class="tablegroup">
			<table id="holdingsTable" class="holdingstable" cellspacing="0" cellpadding ="0" border="0">
				<thead>
					<tr class="table_head">
						<th class="bottom lowpriority"> <span class="hastooltip" title="Is Owned">Own</span><div class="none">Check the boxes of the stocks you have a position in.</div></th>
						<th class="bottom lowpriority"> <span class="hastooltip" title="Entry Date">Entry Date</span><div class="none"><?php echo $typeStrings->entryDateDesc; ?></div></th>
						<th class="bottom"> <span class="hastooltip" title="Ticker Symbol">Symbol </span><div class="none">The ticker symbol of this BioBounce holding.</div></th>
						<th class="bottom lowpriority"> <span class="hastooltip" title="Percent Change">Today </span><div class="none">The percent change of this ticker today.</div></th>
						<th class="bottom"> <span class="hastooltip" title="Last Price">Last </span><div class="none">The most recent sell price.</div></th>
						<?php if ($hasHoldingFirstElement) { ?>
							<th class="bottom"> <span class="hastooltip" title="First Target">Target 1 </span><div class="none"><?php echo $typeStrings->t1Desc; ?></div></th>
							<th class="bottom"> <span class="hastooltip" title="Second Target">Target 2 </span><div class="none"><?php echo $typeStrings->t2Desc; ?></div></th>
							<th class="bottom"> <span class="hastooltip" title="Third Target">Target 3 </span><div class="none"><?php echo $typeStrings->t3Desc; ?></div></th>
						<?php } else { ?>
							<th class="bottom"> <span class="hastooltip" title="First Target">Target</span><div class="none"><?php echo $typeStrings2->t1Desc; ?></div></th>
						<?php } ?>
						<th class="bottom"> <span class="hastooltip" title="Abandon Price">Abandon Price</span><div class="none"><?php echo $typeStrings->abandonDesc; ?></div><div class="note">(sell if closes below)</div></th>
						<th class="bottom lowpriority"> <span class="hastooltip" title="Entry Price">Entry Price</span><div class="none"><?php echo $typeStrings->entryHoldingDesc; ?></div> </th>
						<th class="bottom "> <span class="hastooltip" title="BioBounce Return">Return</span><div class="none">The current return percent of this stock based on the original price.</div> </th>
						<th class="bottom"> <span class="hastooltip" title="Last Action Taken">Last Action</span><div class="none">The last BioBounce action that was taken.</div> </th>
					</tr>
				</thead>
				<tbody>
<?php
if ($login->showData){
	
	$tablestate = "row_odd";
	$someHoldingHidden = false;
	foreach($holdings as $holding){
		$hid = $holding['holdings_id'];
		$highlights = highlights::getHoldingsHighlights($hid);		
		$last = $holding['last'] ;
		$action = $holding['holdings_last_action'];
		$tradeType = $holding['holdings_tradetype'];
		$lastAction = GetLastActionString($tradeType, $action);
		$abandonClass = (IsAbandoned($action)) ? " abandonRow " : "";
		
		$hasToolTipString = "";	
		if (strlen($holding['holdings_tooltip'])>0){
			$hasToolTipString = "hastooltip";
		} 
		
		
		$iAmHolding = personal_holdings::iAmHolding($login->userId, $hid);
		if (!$iAmHolding) $someHoldingHidden = true;
		
		$checkedString = $iAmHolding ? "checked" : "";
		$noneString = $iAmHolding ? "" : " none ";
		$last = $holding['last'];
		$hitT1 = $holding['holdings_t1_marked'];
		$hitT2 = $holding['holdings_t2_marked'];
		$hitT3 = $holding['holdings_t3_marked'];
		$t1 = floatval($holding['holdings_t1']);
		$t2 = floatval($holding['holdings_t2']);
		$t3 = floatval($holding['holdings_t3']);
		$start = $holding['holdings_top_price'];				
		$orig = $holding['holdings_orig_price'];
	
		$return_percent = holdings::GetReturnPercent($tradeType, $last, $orig, $t1, $t2, $t3, $hitT1, $hitT2, $hitT3);
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
		// Targets for depecrated method
		if ($hasHoldingFirstElement) {
			if (UsesAllTargets($tradeType)){
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
			
			} else {
				echo '<td>N/A</td>';
				echo '<td>N/A</td>';				
			}
		}
				
		if ($lastAction == "ABANDON" || $lastAction == "ABANDON BY CLOSE" ){
			echo '<td class="abandon">';
			echo number_format($holding['holdings_stop_price'], 2);
			echo '<img class=\'checkmark\' src=\'images/error.png\'/>';
			echo '</td>';
		} else {			
 			echo '<td class="'.$highlights[H_ABANDON].'">' . number_format($holding['holdings_stop_price'], 2) . '</td>';
		}
		echo '<td class="lowpriority">' . number_format($holding['holdings_orig_price'], 2) . '</td>';
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
	<div class="section n3 <?php echo $login->showClass; ?>">
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
						<th class="bottom lowpriority"> <span class="hastooltip" title="Entry Date">Entry Date</span><div class="none"><?php echo $typeStrings->entryDateDesc; ?></div></th>
						<th class="bottom lowpriority">Abandon Date</th>
						<th class="bottom"> <span class="hastooltip" title="Ticker Symbol">Symbol </span><div class="none">The ticker symbol of this BioBounce holding.</div></th>
						<th class="bottom"> <span class="hastooltip" title="First Target">Target 1</span><div class="none"><?php echo $typeStrings->t1Desc; ?></div></th>
						<th class="bottom"> <span class="hastooltip" title="Second Target">Target 2</span><div class="none"><?php echo $typeStrings->t2Desc; ?></div></th>
						<th class="bottom"> <span class="hastooltip" title="Third Target">Target 3</span><div class="none"><?php echo $typeStrings->t3Desc; ?></div></th>
						<th class="bottom"> <span class="hastooltip" title="Abandon Price">Abandon Price</span><div class="none"><?php echo $typeStrings->abandonDesc; ?></div></th>
						<th class="bottom"> <span class="hastooltip" title="Entry Price">Entry Price</span><div class="none"><?php echo $typeStrings->entryHoldingDesc; ?></div> </th>
						<th class="bottom"> <span class="hastooltip" title="BioBounce Return">Return</span><div class="none">The current return percent of this stock based on the original price.</div> </th>
					</tr>
				</thead>
<?php
	$tablestate = "row_odd";
	$prevMonth = "";
	$allMonths = array();
	$first=true;
	foreach($abandons as $holding){
		$hid = $holding['holdings_id'];
		$hitT1 = $holding['holdings_t1_marked'];
		$hitT2 = $holding['holdings_t2_marked'];
		$hitT3 = $holding['holdings_t3_marked'];
		$t1 = floatval($holding['holdings_t1']);
		$t2 = floatval($holding['holdings_t2']);
		$t3 = floatval($holding['holdings_t3']);
		$start = $holding['holdings_top_price'];			
		$orig = $holding['holdings_orig_price'];			
		$abandonPrice = $holding['holdings_stop_price'];	
		$tradeType = $holding['holdings_tradetype'];
				

		$return_percent = holdings::GetReturnPercent($tradeType, 0, $orig, $t1, $t2, $t3, $hitT1, $hitT2, $hitT3, $abandonPrice );
		
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
			echo '<td class="checked">';
			echo number_format($holding['holdings_t1'], 2) ;
			echo '<img class=\'checkmark\' src=\'images/checkmark.png\'/>';
			echo '</td>';
		}
		else
			echo '<td>' . number_format($holding['holdings_t1'], 2) . '</td>';


		if (UsesAllTargets($tradeType)){
			if (!empty($holding['holdings_t2_marked'])){
				echo '<td class="checked">';
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
			}
			else
				echo '<td>' . number_format($holding['holdings_t3'], 2) . '</td>';
				
		} else {
			echo '<td>N/A</td>';
			echo '<td>N/A</td>';				
		}
	
	 		echo '<td>' . number_format($holding['holdings_stop_price'], 2) . '</td>';
		echo '<td>' . number_format($orig, 2) . '</td>';
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
			<?php echo GetTradeTypeConstantNameSingular($tradeTypes[0]); ?> Actions
		</div >
		<div class="desc">
			<ul class="ruleslist">
				<?php echo $typeStrings->actionList; ?>
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
<script type="text/javascript" src="js/jquery.mask.min.js"></script> 


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