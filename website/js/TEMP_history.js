var CONSTS = {};
CONSTS.TOTALS = 0;
CONSTS.PULLBACKS = 1;
CONSTS.REVERSALS = 2;
CONSTS.SHORTS = 3;
CONSTS.NUM_SERIES_TO_DISPLAY = 4;
var HistoryTable = {};

HistoryTable.Chart = {};
HistoryTable.List = {};

HistoryTable.$chartObject = null;
HistoryTable.visibility = {};
HistoryTable.Initialize = function(){
	HistoryTable.lastSeriesIndex = -1;

	HistoryTable.visibility[0] = true;
	HistoryTable.visibility[1] = true;
	HistoryTable.visibility[2] = true;
	HistoryTable.visibility[3] = true;
	HistoryTable.visibility[4] = false;
	
	    // Set highcharts colors
    Highcharts.theme = {
	colors: ["#F0DE7D","#87F06D", "#00CFFF", "#FA5A5A"],
	   chart: {
	      backgroundColor: {
	         linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
	         stops: [
	            [0, '#333333'],
	            [1, '#333333']
	         ]
	      },
	      style: {
	         fontFamily: "'Unica One', sans-serif"
	      },
	      plotBorderColor: '#606063'
	   },
	   title: {
	      style: {
	         color: '#E0E0E3',
	         textTransform: 'uppercase',
	         fontSize: '20px'
	      }
	   },
	   subtitle: {
	      style: {
	         color: '#E0E0E3',
	         textTransform: 'uppercase'
	      }
	   },
	   xAxis: {
	      gridLineColor: '#707073',
	      labels: {
	         style: {
	            color: '#E0E0E3'
	         }
	      },
	      lineColor: '#707073',
	      minorGridLineColor: '#505053',
	      tickColor: '#707073',
	      title: {
	         style: {
	            color: '#A0A0A3'
	
	         }
	      }
	   },
	   yAxis: {
	      gridLineColor: '#707073',
	      labels: {
	         style: {
	            color: '#E0E0E3'
	         }
	      },
	      lineColor: '#707073',
	      minorGridLineColor: '#505053',
	      tickColor: '#707073',
	      tickWidth: 1,
	      title: {
	         style: {
	            color: '#A0A0A3'
	         }
	      }
	   },
	   tooltip: {
	      backgroundColor: 'rgba(0, 0, 0, 0.85)',
	      style: {
	         color: '#F0F0F0'
	      }
	   },
	   plotOptions: {
	      series: {
	         dataLabels: {
	            color: '#B0B0B3'
	         },
	         marker: {
	            lineColor: '#333'
	         }
	      },
	      boxplot: {
	         fillColor: '#505053'
	      },
	      candlestick: {
	         lineColor: 'white'
	      },
	      errorbar: {
	         color: 'white'
	      }
	   },
	   legend: {
	      itemStyle: {
	         color: '#E0E0E3'
	      },
	      itemHoverStyle: {
	         color: '#FFF'
	      },
	      itemHiddenStyle: {
	         color: '#606063'
	      }
	   },
	   credits: {
	      style: {
	         color: '#666'
	      }
	   },
	   labels: {
	      style: {
	         color: '#707073'
	      }
	   },
	
	   drilldown: {
	      activeAxisLabelStyle: {
	         color: '#F0F0F3'
	      },
	      activeDataLabelStyle: {
	         color: '#F0F0F3'
	      }
	   },
	
	   navigation: {
	      buttonOptions: {
	         symbolStroke: '#DDDDDD',
	         theme: {
	            fill: '#505053'
	         }
	      }
	   },
	
	   // scroll charts
	   rangeSelector: {
	      buttonTheme: {
	         fill: '#505053',
	         stroke: '#000000',
	         style: {
	            color: '#CCC'
	         },
	         states: {
	            hover: {
	               fill: '#707073',
	               stroke: '#000000',
	               style: {
	                  color: 'white'
	               }
	            },
	            select: {
	               fill: '#000003',
	               stroke: '#000000',
	               style: {
	                  color: 'white'
	               }
	            }
	         }
	      },
	      inputBoxBorderColor: '#505053',
	      inputStyle: {
	         backgroundColor: '#333',
	         color: 'silver'
	      },
	      labelStyle: {
	         color: 'silver'
	      }
	   },
	
	   navigator: {
	      handles: {
	         backgroundColor: '#666',
	         borderColor: '#AAA'
	      },
	      outlineColor: '#CCC',
	      maskFill: 'rgba(255,255,255,0.1)',
	      series: {
	         color: '#7798BF',
	         lineColor: '#A6C7ED'
	      },
	      xAxis: {
	         gridLineColor: '#505053'
	      }
	   },
	
	   scrollbar: {
	      barBackgroundColor: '#808083',
	      barBorderColor: '#808083',
	      buttonArrowColor: '#CCC',
	      buttonBackgroundColor: '#606063',
	      buttonBorderColor: '#606063',
	      rifleColor: '#FFF',
	      trackBackgroundColor: '#404043',
	      trackBorderColor: '#404043'
	   },
	
	   // special colors for some of the
	   legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
	   background2: '#505053',
	   dataLabelsColor: '#B0B0B3',
	   textColor: '#C0C0C0',
	   contrastTextColor: '#F0F0F3',
	   maskColor: 'rgba(255,255,255,0.3)'   
	};
	
	// Apply the theme
	Highcharts.setOptions(Highcharts.theme);
	
	// highcharts
    $('#HistoryCharts').highcharts({

        chart: {
            type: 'column', 
                // Edit chart spacing
	        spacingBottom: 25,
	        spacingTop: 5,
	        spacingLeft:5,
	        spacingRight: 5,
        },

        title: {
            text: 'Profit By Month And Strategy'
        },

        xAxis: {
            categories: months
        },

        yAxis: {
            allowDecimals: true,
            title: {
                text: 'Monthly Profit'
            }
        },

        tooltip: {
            formatter: function () {
            
            	var negSymbol = (this.y < 0) ? "-" : "";
            	var retVal ='<b>' + this.x + '</b><br/>' + this.series.name + ': '+ negSymbol+'$' + Math.abs(this.y).toFixed(2)+ '<br/>';
            	if (this.point.hasOwnProperty("numTrades"))
			retVal += "<br/>Total Trades:" + this.point.numTrades ;
            	if (this.point.hasOwnProperty("numPullbackTrades"))
			retVal += "<br/><p class='note'>(" + this.point.numPullbackTrades + "p, " ;
            	if (this.point.hasOwnProperty("numShortTrades"))
			retVal += this.point.numShortTrades + "s, ";
            	if (this.point.hasOwnProperty("numReversalTrades"))
			retVal += this.point.numReversalTrades + "r)</p>";
            	return retVal;
            }
            
        },

        plotOptions: {
            column: {
                stacking: 'normal', 
                point: {
                	events: {
			            click:function(e){
			            	HistoryTable.List.ChangeMonth(this.category, this.series.index);
			            }
                	}
                }, 

				events: { 
					legendItemClick:function(e) {
						if (this.visible)
							return false;
						var seriesIndex = this.index;
						var series = this.chart.series;

						for (var i = 0; i < series.length; i++) {
							if (series[i].index != seriesIndex) {
								series[i].hide();
								HistoryTable.Chart.toggleStrategy(series[i].index, false);
							}
						}
						series[seriesIndex].show();
						if (seriesIndex != CONSTS.TOTALS) {
							HistoryTable.Chart.toggleStrategy(e.target.index, true);
						} else {
							HistoryTable.Chart.toggleStrategy(CONSTS.PULLBACKS, true);
							HistoryTable.Chart.toggleStrategy(CONSTS.REVERSALS, true);
							HistoryTable.Chart.toggleStrategy(CONSTS.SHORTS, true);
							HistoryTable.Chart.toggleStrategy(CONSTS.TOTALS, true);
						}
						HistoryTable.List.ChangeTotals($('#monthSelect select').val(), seriesIndex);
						HistoryTable.$chartObject.xAxis[0].dirty = true;
						HistoryTable.$chartObject.redraw();
						return false;
					}
				}
            }
        },
        legend: {
            align: 'right',
            verticalAlign: 'top',
            x: 0,
            y: 125, 
            layout:'vertical'
        },

        series: [ {
            name: 'Total',
            data: totalsByMonth,
            stack: 'strategies'
        },{
            name: 'Pullbacks',
            data: pullbacksByMonth,
            stack: 'strategies'
        }, {
            name: 'Reversals',
            data: reversalsByMonth,
            stack: 'strategies'
        }, {
            name: 'Shorts',
            data: shortsByMonth,
            stack: 'strategies'
        }]
    });
    

	
    
    //initialize to the most recent month

    HistoryTable.$chartObject = $('#HistoryCharts').highcharts();
    HistoryTable.List.ChangeMonth($('#monthSelect select').val(), HistoryTable.lastSeriesIndex);

    for (var i = 0; i<=CONSTS.NUM_SERIES_TO_DISPLAY-1 ; i++){
	HistoryTable.$chartObject.series[i].hide();
	//HistoryTable.Chart.toggleStrategy(i, false);
   }
   HistoryTable.$chartObject.series[0].show();

	$('.highcharts-xaxis-labels').click(function(e){ 
		var $target = $(e.target);
		var month;
		if ($target.prev().length > 0){
			month = $target.prev().text() + $target.text();
		} else if ($target.next().length > 0){
			month = $target.text() + $target.next().text() ;
		} else {
			month = $target.text();
		}
		HistoryTable.List.ChangeMonth(month,HistoryTable.lastSeriesIndex);  
	}); 

	// listeners
	$("#monthSelect select").change(function(){
		HistoryTable.List.ChangeMonth($(this).val(),HistoryTable.lastSeriesIndex);
	});
	


	

};
HistoryTable.$ChartTableBody = $('#ChartTableBody');
HistoryTable.Chart.toggleStrategy = function(strategyIndex, makeVisible, saveVisibility){
	saveVisibility = typeof saveVisibility !== 'undefined' ? saveVisibility : true;

	var rowClass = "GetNothing";
	switch (strategyIndex){
		case CONSTS.PULLBACKS:
			rowClass = "pullback";
    		break;
		case CONSTS.REVERSALS:
			rowClass = "reversal";
    		break;
		case CONSTS.SHORTS:
			rowClass = "short";
    		break;
		case CONSTS.TOTALS:
			rowClass = "total";
    		break;
	}
	if (saveVisibility)
		HistoryTable.visibility[strategyIndex] = makeVisible;
	
	if (makeVisible){
    	HistoryTable.$ChartTableBody.find("." + rowClass).removeClass("none"); 		
	}else {
    	HistoryTable.$ChartTableBody.find("." + rowClass).addClass("none");
	}
};
HistoryTable.List.ChangeTotals = function(monthStr, seriesIndex){
	seriesIndex = typeof seriesIndex !== 'undefined' && seriesIndex !== CONSTS.TOTALS ? seriesIndex : -1;
	HistoryTable.lastSeriesIndex = seriesIndex

	var isSpecificCase = seriesIndex >= 0 ? true : false;
	var $summaryTitle = $("#SummaryTitle");
	
	if (isSpecificCase){
		var idText="Nothing";
		switch(seriesIndex){
			case CONSTS.PULLBACKS:
				idText = "Pullbacks";
				break;
			case CONSTS.REVERSALS:
				idText = "Reversals";
				break;
			case CONSTS.SHORTS:
				idText = "Shorts";
				break;
		}
		$summaryTitle.text(idText + " for " );
	}else{
		$summaryTitle.text("Summary for " );
	}
	
	//var $summaryMonth = $("span#SummaryMonth");
	var $pullbacksTotal = $("div#PullbackSummary");
	var $reversalsTotal = $("div#ReversalSummary");
	var $shortsTotal = $("div#ShortSummary");
	var $total = $("div#TotalSummary");

	var monthTotals = totals[monthStr];

	var total = 0;	
	var showCount = 0;
		//$summaryMonth.text(monthTotals["monthString"]);
	if (HistoryTable.visibility[CONSTS.PULLBACKS] && (seriesIndex == -1 || seriesIndex == CONSTS.PULLBACKS)){
		$pullbacksTotal.removeClass("none");
		$pullbacksTotal.find(".summaryData").text(monthTotals["0"]).formatCurrency({colorize:true});
		total += monthTotals["0"];
		showCount ++;
	} else {
		$pullbacksTotal.addClass("none");
	} 	

	if (HistoryTable.visibility[CONSTS.REVERSALS] && (seriesIndex == -1 || seriesIndex == CONSTS.REVERSALS)){
		$reversalsTotal.removeClass("none");
		$reversalsTotal.find(".summaryData").text(monthTotals["2"]).formatCurrency({colorize:true});
		total += monthTotals["2"];
		showCount ++;
	} else {
		$reversalsTotal.addClass("none");
	} 	
	
	if (HistoryTable.visibility[CONSTS.SHORTS] && (seriesIndex == -1 || seriesIndex == CONSTS.SHORTS)){
		$shortsTotal.removeClass("none");
		$shortsTotal.find(".summaryData").text(monthTotals["1"]).formatCurrency({colorize:true});
		total += monthTotals["1"];
		showCount ++;
	} else {
		$shortsTotal.addClass("none");
	} 	
	
	if (showCount > 1){
		$total.removeClass("none");		
		$total.find(".summaryData").text(total).formatCurrency({colorize:true});
	} else {
		$total.addClass("none");		
	}
	
};

HistoryTable.List.ChangeMonth = function(monthStr, seriesIndex){
	seriesIndex = typeof seriesIndex !== 'undefined' && seriesIndex !== CONSTS.TOTALS ? seriesIndex : -1;
	var monthKey = monthStr.replace(" ", "");
	$(".monthSelect select").val(monthKey);
	HistoryTable.List.ChangeTotals(monthKey, seriesIndex);
	HistoryTable.$ChartTableBody.html(tablesByMonth[monthKey]);		
    for (var i = 0; i<=CONSTS.NUM_SERIES_TO_DISPLAY-1 ; i++){
    	if (seriesIndex <0){
	    	if (!HistoryTable.visibility[i] && !HistoryTable.visibility[CONSTS.TOTALS]){
			HistoryTable.$chartObject.series[i].hide();
			HistoryTable.Chart.toggleStrategy(i, false);
	    	}
    	} else {
	    	if (seriesIndex == i){
				HistoryTable.Chart.toggleStrategy(i, true, false);
	    	}else {
				HistoryTable.Chart.toggleStrategy(i, false, false);
	    	}
    	}
    }
	//$("#holdingsTable").tablesorter();

};

$(function () {
	HistoryTable.Initialize();		
});