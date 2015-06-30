$(document).ready(function() {

	window.scrollTo(0,0);
	var topBarHeight = 100;
	var windowHeight = $(window).height();
	var newHeight = windowHeight - topBarHeight;
	//$('.section').css('height', newHeight);

	$('#toppanel .navigator').click(function(){
		var id = $.attr(this, 'id');
		var top = $('div.' + id).offset().top - topBarHeight;
		if (id == "n0") top = 0;
   		
	    $('html, body').animate({scrollTop: top}, 500);
	    return false;
	});
	

});
