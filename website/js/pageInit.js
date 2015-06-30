$(document).ready(function() {

	window.scrollTo(0,0);
	var topBarHeight = 100;
	var windowHeight = $(window).height();
	var newHeight = windowHeight - topBarHeight;
	//$('.section').css('height', newHeight);

	$('#tabslide a, #toppanel .navigator2').click(function(){
		var id = $.attr(this, 'id');
			var top = $('div.' + id).offset().top - topBarHeight;
			if (id == "n0") top = 0;
	   		
			$('html, body').animate({scrollTop: top}, 500);
			return false;
	});
	
	
	
	if ( $( "#watchTable" ).length )
		$("#watchTable").tablesorter();
	if ( $( "#holdingsTable" ).length )
		$("#holdingsTable").tablesorter();
	if ( $( ":checkbox:not(.lockbox)" ).length )
		$(":checkbox").uniform();
	
	$('.hastooltip').each(function() { // Notice the .each() loop, discussed below
		var text = $(this).next('div').html();
		$(this).qtip({
			content: {
				title: $(this).attr('title'), 
				text: text// Use the "div" element next to this for the content
			}, 
			position: {
				my: 'top center',
				at: 'bottom center'
			}, 
			style: {
				classes: 'qtip-dark',
				width: 160
			},
			events: {
				render: function(event, api) {
					// Grab the tip element
					var elem = api.elements.tip;
				}
			}

		});
	});
	
	
	$('.personalHolding').change(function(){
		var checked = $(this).attr("checked") == "checked";
		var hid = $(this).attr("name");
		var dataObject = {hid: hid, checked:checked};
		$.ajax({
			type: "GET",
			url: "personal_holdings_update.php",
			data: dataObject
		})
		.done(function( msg ) {
			//alert( "Data Saved: " + msg );
		});
		
	});
	
	$('.showall a').click(function(){
		$(this).parent().addClass("none");
		$('.holdings_row').removeClass("none");
	});

	function changeMonth(monthStr){
		$(".monthTable").addClass("none");
		$("#month_" + monthStr).removeClass("none");
	}	
	
	var $tableBody = $('#ChartTableBody');
	
	// todo parse this out to separate file for other pages	
	if (!$("body#HistoryPage").length){
		$("#monthSelect select").change(function(){
			changeMonth($(this).val());
			var element = $(this).parent().parent().parent();
			var top = element.offset().top - 100;
		    $('html, body').animate({scrollTop: top}, 500);
		});

	    changeMonth($('#monthSelect select').val());
	}
});
