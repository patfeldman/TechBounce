$(document).ready(function() {
	
	// Expand Panel
	$("#open").click(function(){
	//	$("div#panel").slideDown("slow");
		$("div#panel").animate ( {top:0}, 500);
		$("div#tabslide").animate ( {top:250}, 500);
	});	
	
	// Collapse Panel
	$("#close").click(function(){
		//$("div#panel").slideUp("slow");	
		$("div#panel").animate ( {top:-250}, 500);
		$("div#tabslide").animate ( {top:0}, 500);
	});		
	
	// Switch buttons from "Log In | Register" to "Close Panel" on click
	$("#toggle a").click(function () {
		$("#toggle a").toggle();
	});		
		
});