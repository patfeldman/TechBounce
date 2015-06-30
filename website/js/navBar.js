//$('#cssmenu').prepend('<div id="menu-button">Menu</div>');
  $('#cssmenu #menu-button').on('click', function(){
    var menu = $(this).next('ul');
    if (menu.hasClass('open')) {
      menu.removeClass('open');
    } else {
      menu.addClass('open');
    }
});


$(document).ready(function() {

	window.scrollTo(0,0);
	var topBarHeight = 60;
	var windowHeight = $(window).height();
	var newHeight = windowHeight - topBarHeight;
	//$('.section').css('height', newHeight);
	var $sidemenu = $("#sideMenu");
	$sidemenu.addClass ("none");
	$('#submenu .navigator2, .indexMenu .navigator2').click(function(){
		var id = $.attr(this, 'id');
		var top = $('div.' + id).offset().top - topBarHeight;
		if (id == "n0") top = 0;
   		
	    $('html, body').animate({scrollTop: top}, 500);
		if (!$sidemenu.hasClass("none"))
			$sidemenu.addClass("none");
	    return false;
	});

	$('#cssmenu .openMenu').click(function(){
		$sidemenu.removeClass("none");
	});
	$('#cssmenu .closeMenu').click(function(target){
		if (!$sidemenu.hasClass("none"))
			$sidemenu.addClass("none");
	});
	
		// Expand Panel
	$("#open, .registerLink").click(function(){
	//	$("div#panel").slideDown("slow");
		$("div#panel").animate ( {top:0}, 500);
		$("#bigMenu .wrapper").animate ( {marginTop:250}, 500);
	});	
	
	// Collapse Panel
	$("#close").click(function(){
		//$("div#panel").slideUp("slow");	
		$("div#panel").animate ( {top:-250}, 500);
		$("#bigMenu .wrapper").animate ( {marginTop:0}, 500);
	});		
	
	// Switch buttons from "Log In | Register" to "Close Panel" on click
	$("#toggle a").click(function () {
		$("#toggle a").toggle();
	});		
		
	$("a.registerLink").click(function () {
		$toggle = $("#toggle a");
		if ($("#toggle a:visible")[0].id== "open")
				$toggle.toggle();
	});		

	
});