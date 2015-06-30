$(document).ready(function() {
	$buttonRow = $("#ButtonRow");

	$('button#RegisterOn').on("click", function(){
		$buttonRow.addClass("none");
		$("#RegisterSection").removeClass("none");
	});
	$('button#LoginOn').on("click", function(){
		$buttonRow.addClass("none");
		$("#LoginSection").removeClass("none");
	});

	$('button#Close').on("click", function(){
		$buttonRow.addClass("none");
		$("#LoginSection").removeClass("none");
	});
	
	
});