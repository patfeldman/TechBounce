<?php
	require_once('./php/homePageBase.php');
?>


<!DOCTYPE html>
<html>
<head>
<title>BioBounce.com</title>

<meta name = "keywords" content = "biotech, stock, market, swing trading, stock trading" />
<meta name = "description" content = "" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, minimum-scale=1 user-scalable=no">
<link rel="shortcut icon" href="images/arrow.ico?v2">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/navBar.js" type="text/javascript"></script>

<link href="css/biobounce.css" rel="stylesheet" type="text/css">
<link href="css/biomembers.css" rel="stylesheet" type="text/css">
<link href="css/RESPONSIVE.css" rel="stylesheet" type="text/css">
<link href="css/responsive_reversals.css" rel="stylesheet" type="text/css">
<link href="css/jquery.qtip.min.css" rel="stylesheet" type="text/css">
<link href="css/uniform.aristo.min.css" rel="stylesheet" type="text/css">

<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
<!-- Sliding effect -->
<script src="js/pageInitSimple.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {

<?php
	if ($errorsExist){
?>
	//$('.section').css('height', newHeight);

	$("div#panel").css('top', 0);
	$("div#tabslide").css( 'top', 250);
	$("#toggle a").toggle();

<?php
	}
	if (strlen($messageSentResponse)>0){
?>
		setTimeout(function() {  
			$('.messagebox').fadeOut('slow');  
		}, 4000); // <-- time in milliseconds  
<?php 
	}	
?>

});	


</script>

</head>

<body id="Index">
<!-- Panel -->
<?php include 'navBarIndex.php'; ?>

<?php 
	if (strlen($messageSentResponse)>0){
		echo '<div class="messagebox">' . $messageSentResponse . '</div>';  
	}
?>


<div class = "newwrap">
	<div class="section n0">
		<div id="maintitle">
			BioBOUNCE
		</div >
		<div class="quote" >"The safest place to look for a new trade is at the end of the first correction to a new swing"...W.D.Gann</div>
		<div class="desc">
		<div id="HistoryCharts" class="desc trialMessage"></div>
		<div  class="historyNote bottomBorder">
			(all Profits are based on a $5000.00 per trade) 
		</div >
			BioBounce.com is a trading system that utilizes extensive technical analysis to harness the volatility of the Biotech sector using proven long and short trading techniques: Breakouts and Pullbacks for long trades; Breakdowns and Backdrafts for our short trades. 
			</br></br>
We track the $IBB trend daily and populate our Watchlists according to how the sector is trending at the time. Therefore, each watchlist is weighted more than others depending on that trend. Doing this provides a consistent selection of highly probable trades over time, resulting in increased gains. Here are our four trades:
			</br></br>
		<div class="descTableGroup">
			<table id="SplitRowTable" class="boxborder2" cellspacing="0" cellpadding ="1" border="1">
				<thead>
					<tr class="table_head1">
						<th class="breakoutHeader">Breakout Trades</th>
						<th class="pullbackHeader">Pullback Trades</th>
					</tr>
				</thead>
				<tbody>
					<tr class="table_row">
						<td>
							<div class="table_desc">
								A Breakout trade capitalizes on the momentum of a stock while the ticker is in a bullish trend.
								<ul>
									<dl>
										<dt>One ...</dt>
										<dd>Buy at the entry price.</dd>
									</dl>
									<dl>
										<dt>Two ... </dt>
										<dd>Sell half of your position at the indicated target.</dd>
									</dl>
									<dl>
										<dt>Three ... </dt>
										<dd>When the ABANDON alert is issued, sell your position the next day.</dd>
									</dl>
								</ul>
							</div>
						</td>
						<td>
							<div class="table_desc">	
								A Pullback capitalizes on small decreases in price while a ticker is in a bullish trend.
								<ul>
									<dl>
										<dt>One ...</dt>
										<dd>Buy at the entry price.</dd>
									</dl>
									<dl>
										<dt>Two ... </dt>
										<dd>Sell your full position at the indicated target.</dd>
									</dl>
									<dl>
										<dt>Three ... </dt>
										<dd>When the ABANDON alert is issued, sell your position the next day.</dd>
									</dl>
								</ul>
							</div>
						</td>
					</tr>
				</tbody>
				<thead>
					<tr class="table_head1">
						<th class="breakdownHeader">Breakdown Trades</th> 
						<th class="shortHeader">Backdraft Trades</th>
					</tr>
				</thead>
				<tbody>
					<tr class="table_row">
						<td>
							<div class="table_desc">
								A Breakdown trade capitalizes on the momentum of a stock while the ticker is in a bearish trend.
								<ul>
									<dl>
										<dt>One ...</dt>
										<dd>Short at the Entry Price.</dd>
									</dl>
									<dl>
										<dt>Two ... </dt>
										<dd>Buy to cover half of your position at the indicated target.</dd>
									</dl>
									<dl>
										<dt>Three ... </dt>
										<dd>When the ABANDON alert is issued, buy to cover your position the next day.</dd>
									</dl>
								</ul>
							</div>
						</td>
						<td>
							<div class="table_desc">	
								A Backdraft trade capitalizes on small spikes in price action while the ticker is in a bearish trend.
								<ul>
									<dl>
										<dt>One ...</dt>
										<dd>Short at the Entry Price.</dd>
									</dl>
									<dl>
										<dt>Two ... </dt>
										<dd>Buy to cover your full position at the indicated target.</dd>
									</dl>
									<dl>
										<dt>Three ... </dt>
										<dd>When the ABANDON alert is issued, buy to cover your position the next day.</dd>
									</dl>
								</ul>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
<br/><br/>	
Because we provide our entry prices and profit taking targets BEFORE they happen, it is impossible for us to front run our members.  Providing the information BEFORE it happens also gives you more time to prepare for the trade.  This is especially important for our members that have difficulty trading during the day.  Simply set your orders ahead of time and go about your day.
<br/><br/>	
		<div id="SubscriptionList">
			<div class="signup">Sign up for a free 14 day trial now! </div>
			<div class="signupDesc">
				<div class="noRisk">The free trial is No Risk, No Obligation, and No Credit Card Required.</div>
				Once your trial ends, you can use the link inside our members area to begin your subscription:
				<ul class="deallist">
					<li>$49.99/month.</li>
					<li>You can cancel at anytime, no questions asked. </li>
					<li><a class="underlineLink registerLink" href="javascript:void(0)">REGISTER NOW!</a></li>
				</ul>
			</div>
		</div>
		</br>
		</br>

		</br>
		</br>


Follow along with a chart of our history above and these current pullback trades.
		<div  class="bottomBorder"></div>

		<div class="tablegroup bottomBorder">
			<table id="CurrentHoldings" class="watchtable boxborder2" cellspacing="0" cellpadding ="0" border="0">
				<thead>
					<tr class="table_head1">
						<th colspan="6" class="center"> Currently Held Positions</th>
					</tr>
					<tr class="table_head2">
						<th class="left"> Symbol </th>
						<th class="center" > Type </th>
						<th class="center"> Last </th>
						<th class="center"> Orig Price </th>
						<th class="center"> Orig Date </th>
						<th class="center"> Return </th>
						<th></th>
					</tr>
				</thead>
				<tbody>
<?php
	$holdings = ticker_group_info::retrieveHoldingsArray(LONG_TRADE, true);
	$tablestate = "row_odd";
	$borderTop = "borderTop";
	foreach($holdings as $holding){
		$tt = intval($holding['holdings_tradetype']);
		//if ($tt == BREAKDOWN_TRADE) continue;
		$last = $holding['last'] ;
		$ttStr = GetTradeTypeConstantNameSingular($tt);
		$tablerowtype = strtolower($ttStr);
		$start = $holding['holdings_orig_price'];
		
		$return_percent = holdings::GetReturnPercent($tt, $last, $start, 0, 0, 0, 0, 0, 0);
		
		echo '<tr class="table_row2 '. $tablestate. ' ' . $tablerowtype .'">';
		echo '<td class="left borderLeft '. $borderTop .'">' . strtoupper($holding['ticker_symbol']) . '</td>';
		echo '<td class="center '. $borderTop .'">' . $ttStr . '</td>';
		echo '<td class="center '. $borderTop .'">' . number_format($holding['last'], 2) . '</td>';
		echo '<td class="center '. $borderTop .'">' . number_format($start, 2) . '</td>';
		echo '<td class="center '. $borderTop .'">' . date ("m-d-Y" , strtotime($holding['holdings_orig_date'])) . '</td>';
		echo '<td class="center borderRight '. $borderTop .'">' . number_format($return_percent, 2) . '%</td>';
		echo '</tr>';
		$borderTop = "";
	}
?>					
				</tbody>
			</table>
		</div> <!-- tablegroup -->

	</div> <!-- desc -->
		
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
		
		
	</div> <!-- section -->
</div>
<div class = "newwrap">
	<div class="section n1">
		<div class="title1">
			What Do We Do? 
		</div >
		<div class="desc">
The number of tickers on our watchlists is based on the trend of the $IBB at that point in time. We monitor the $IBB daily to assure we weight our watchlist selections appropriately. 
<br/><br/>
For example, if the $IBB is trending bullish, our selections are weighted toward breakout and pullback trades due to their higher probability of success. 
However, when the $IBB flips into a bearish trend, our selections are weighted toward Breakdown and BackDraft trades due to the market conditions forcing a number of bullish trending stocks into a bearish trend. 
<br/><br/>
It&#39;s simple, if a ticker is on any of our lists, it&#39;s perfectly fine to take an entry if and when it hits.  Be sure to follow the rules and take profits as indicated or exit if you get an ABANDON alert at the end of day.
<br/><br/>
Taking small gains over time adds up, especially in a volatile sector such as Biotechnology.
		</div>
	</div>
</div>
<div class = "newwrap">
	<div class="section n2">
		<div class="title1">
			Who Are We? 
		</div >
		<div class="desc">
<a class="tweeter" href="https://twitter.com/BioBounce">@BioBounce</a> has over 15 years of commercial Biopharmaceutical

experience. His knowledge of the biotech industry provides insight to 

the research side of our selection process.<br/>
<br/>
<a class="tweeter" href="https://twitter.com/AlgoSniffer">@AlgoSniffer</a> has been analyzing the stock market for 20+ years. He has

developed a unique swing trading method that provides the insight into 

the technical analysis side of our selection process. <br/><br/>

This website combines the expertise of @AlgoSniffer and 

@BioBounce. <br/>
<br/>

<table class="twitter-table">

  <tr>
    <td  align="center" valign="top" >
    <div align="left"><P style="text-align: left;"><A class="twitter-timeline" data-widget-id="387138135336828929" href="https://twitter.com/BioBounce">Tweets by @BioBounce</A>&nbsp;
<SCRIPT type="text/javascript">// <![CDATA[
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
// ]]></SCRIPT>
</P></div></td>

    <td  align="center" valign="top" >
    <div align="left"><P><A class="twitter-timeline" data-widget-id="387397309249490944" href="https://twitter.com/AlgoSniffer">Tweets by @AlgoSniffer</A>
<SCRIPT type="text/javascript">// <![CDATA[
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
// ]]></SCRIPT>
</P></div></td>
  </tr>
  
</table>
		</div>
	</div>
</div>
<div class = "newwrap">
	<div class="section n3">
		<div class="title1">
			Why Should You Be Interested? 
		</div >
		<div class="desc">
			<div class="testimonial">
				&#34;My first time buying today. Very happy, net covered cost of subscription on day when my IBD50 stocks tanked!&#34;
			</div>
			<div class="testimonial_username">
				@bpmswatch
			</div>
			<div class="testimonial">
				&#34;I really like how easy your system is to follow. VERY easy! You sure call em&#39; to the penny...look at ALKS...big volume coming in right to the penny.  Really Cool!&#34;
			</div>
			<div class="testimonial_username">
				Gary A.
			</div>
			<div class="testimonial">
				&#34;Hi Gonzo, Just want to thank you for NSPR. Got a small position in the ZONE and now watching it run.
				We appreciate your hard work with the spreadsheet and everything else.&#34;
			</div>
			<div class="testimonial_username">				
				BLY
			</div>
			<div class="testimonial">				
				&#34;The spreadsheet is a thing of beauty!
				Your service is a must have for every trader and investor; priceless technical analysis!
				Thanks for all you do!&#34;
			</div>
			<div class="testimonial_username">
				Paul M.
			</div>

			<div class="testimonial">
				&#34;@AlgoSniffer OMG you&#39;re great!!! DDXS hit the target&#34;
			</div>
			<div class="testimonial_username">
				Andrilu C.
			</div>

			<div class="testimonial">
				&#34;The best way to stay unemotionally involved is don&#39t know anything about the investment other than the symbol... That&#39s being provided along with the price parameters... Then just play by the rules... There can be no emotional involvement... unless you like one set of letter better than another...&#34;
			</div>
			<div class="testimonial_username">
				Andrew J
			</div>
			<div class="testimonial">
				&#34;@AlgoSniffer You are rocking! Even when the bios are doing terribly.&#34;
			</div>
			<div class="testimonial_username">
				Richard U
			</div>
			<div class="testimonial">
				&#34;Your spreadsheet is fantastic.
Thanks for getting me perfectly positioned in KERX. My
last 25% is now running, and that&#39;s exactly how this is supposed
to work. Fantastic.
I was floundering around, trying to do my own setups, stops and
targets by the seat of the pants, but now I have found THE LIGHT.
At $40 a month your service is set too cheap.&#34;
			</div>
			<div class="testimonial_username">
				Scott K
			</div>
			<div class="testimonial">
				&#34;Don&#39;t get distracted and stick with the BioBounce/Gonzo watch list.
				Indeed, the only way to really make money on BIOs is to stick to the BioBounce/Gonzo method. Anything short of that is pure gamble and BIOs are never to be trusted, simply traded!  And this is why BioBounce/Gonzo are successful!
				My 2 cents.&#34;
			</div>
			<div class="testimonial_username">
				Thecatman
			</div>
		</div>
	</div>
</div>
	
<div class = "newwrap last">
	<div class="section n4">
		<div class="title1">
			How Does It Work? 
		</div >
		<div class="desc">
We do the research and technical analysis and populate each watchlist based on current market conditions. Entry prices and profit taking targets are provided BEFORE they happen giving you time to prepare for the trade. Our website is automated to provide you alerts via text and/or email during trading hours as an event happens! 
</br></br>

			<div class="descTableGroup">
				<table id="SplitRowTable2" cellspacing="0" cellpadding ="1" border="1">
					<thead>
						<tr class="table_head1">
							<th class="breakoutHeader">Breakout Trades</th>
							<th class="pullbackHeader">Pullback Trades</th>
							<th class="breakdownHeader">Breakdown Trades</th>
							<th class="shortHeader">Backdraft Trades</th>
						</tr>
					</thead>
					<tbody>
						<tr class="table_row">
							<td>
								<ul class="ruleslistNew">
									<li>Purchase the stock when it hits the indicated entry price.</li>
									<li>Sell 50% of your position at the indicated target.</li>
									<li>If the stock ever closes below its ABANDON price, sell your remaining position.</li>
									<li>If the stock ever hits the HARD STOP price, sell your remaining position immediately.</li>
								</ul>
							</td>
							<td>
								<ul class="ruleslistNew">
									<li>Purchase the stock when it hits the indicated entry price.</li>
									<li>Sell 100% of your position at the indicated target.</li>
									<li>If the stock ever closes below its ABANDON price, sell your remaining position.</li>
								</ul>
							</td>
							<td>
								<ul class="ruleslistNew">
									<li>Short the stock when it hits the indicated entry price.</li>
									<li>Buy to cover 50% of your position at the indicated target.</li>
									<li>If the stock ever closes above its ABANDON price, buy to cover your remaining position.</li>
									<li>If the stock ever hits the HARD STOP price, buy to cover your remaining position immediately.</li>
								</ul>
							</td>
							<td>
								<ul class="ruleslistNew">
									<li>Short the stock when it hits the indicated entry price.</li>
									<li>Buy to cover 100% of your position at the indicated target.</li>
									<li>If the stock ever closes above its ABANDON price, buy to cover your remaining position.</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>
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


<?php
	include('getHistoryChartJs.script.php');
?>
<script type="text/javascript" src="js/historyChartsJustChart.js"></script> 

</body>
</html>