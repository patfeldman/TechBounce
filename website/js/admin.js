$(document).ready(function() {
	var $dropdowns = $('#InputBodyGroup select.enteredValues');
	var $lowBox = $('#LowBox');
	var $highBox = $('#HighBox');
	
	$dropdowns.change(function(event){
		var targetVal = $(event.target).val();
		var isLocked = $('#InputHeaderGroup .lockbox').is(':checked');
		if (isLocked) {
			$dropdowns.val(targetVal);
		}
		if (!isLocked){
			$lowBox.text("Low/Entry");
			$highBox.text("High/Range");			
		}else if (targetVal == "3" || targetVal == "4"){ // BREAKOUT OR BREAKDOWN
			$lowBox.text("Entry");
			$highBox.text("Range");
		} else {
			$lowBox.text("Low");
			$highBox.text("High");			
		}
		
	});
	
	$("div.sectionTitle").click(function(event){
		var $target = $(event.target);
		var text = $target.html();
		var headerName = $target.attr('id');
		var sectionName = headerName.replace('Header', 'Table');
		var $table = $("div#" + sectionName);
		
		if ($table.css('display')=='none'){
			$table.show();
			text = text.replace('+', '-');			
		}else {
			$table.hide();
			text = text.replace('-', '+');			
		}
		$target.html(text);
	});

});