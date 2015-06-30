var CONSTS = {};
CONSTS.TOTALS = 0;
CONSTS.BREAKOUTS = 1;
CONSTS.PULLBACKS = 2;
CONSTS.BREAKDOWNS = 3;
CONSTS.SHORTS = 4;
CONSTS.NUM_SERIES_TO_DISPLAY = 5;
var HistoryTable = {};

HistoryTable.Chart = {};
HistoryTable.List = {};

HistoryTable.$chartObject = null;
HistoryTable.visibility = {};
HistoryTable.Initialize = function(){
	HistoryTable.lastSeriesIndex = 0;

	HistoryTable.visibility[0] = true;
	HistoryTable.visibility[1] = false;
	HistoryTable.visibility[2] = false;
	HistoryTable.visibility[3] = false;
	HistoryTable.visibility[4] = false;
	
	    // Set highcharts colors
    Highcharts.theme = {
	colors: ["#F0DE7D", "#78F1F1",  "#87F06D", "#FA5A5A", "#FF2900"], 
        chart: {
	      style: {
	         fontFamily: "'Open Sans', sans-serif"
	      },
	      plotBorderColor: '#606063'
	   }, 
	title: {
	      style: {
	         color: '#222222',
	         fontSize: '2.0em'
	      }
	   },


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
            text: 'Historical Profit'
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
/*
            	if (this.point.hasOwnProperty("numPullbackTrades"))
					retVal += "<br/><p class='note'>(" + this.point.numPullbackTrades + "PB, " ;
            	if (this.point.hasOwnProperty("numShortTrades"))
					retVal += this.point.numShortTrades + "BaD, ";
            	if (this.point.hasOwnProperty("numBreakoutTrades"))
					retVal += this.point.numBreakoutTrades + "BO,"; 
            	if (this.point.hasOwnProperty("numBreakdownTrades"))
					retVal += this.point.numBreakdownTrades + "BrD"; 
		retVal += ")</p>";
*/
	        return retVal;
            }
        },

        plotOptions: {
            column: {
                stacking: 'normal', 
				events: { 
					legendItemClick:function(e) {
						if (this.visible)
							return false;
						var seriesIndex = this.index;
						var series = this.chart.series;

						for (var i = 0; i < series.length; i++) {
							if (series[i].index != seriesIndex) {
								series[i].hide();
							}
						}
						series[seriesIndex].show();
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
           stack: 'total'
        }, {
            name: 'Breakouts',
            data: breakoutsByMonth,
            stack: 'strategies'
       }, {
            name: 'Pullbacks',
            data: pullbacksByMonth,
            stack: 'strategies'
        }, {
            name: 'Breakdowns',
            data: breakdownsByMonth,
            stack: 'strategies'
        }, {
            name: 'Backdrafts',
            data: shortsByMonth,
            stack: 'strategies'
        }]
    });
    

	
    
    //initialize to the most recent month

    HistoryTable.$chartObject = $('#HistoryCharts').highcharts();

    for (var i = 0; i<=CONSTS.NUM_SERIES_TO_DISPLAY-1 ; i++){
    	if (!HistoryTable.visibility[i]){
			HistoryTable.$chartObject.series[i].hide();
    	}
    }

};

$(function () {
	HistoryTable.Initialize();		
});