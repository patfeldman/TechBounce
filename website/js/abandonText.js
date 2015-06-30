$(document).ready(function() {
	dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 140,
      width: 350,
      modal: true,
      buttons: {
        "Abandon Now": function(){
			var hid = $("#HoldingId").val();
			var str = $("#AbandonMessage").val();
			if (str.length > 0){
				window.location = "?abandon_id=" + hid + "&message=" + encodeURIComponent(str);				
			}else{
				window.location = "?abandon_id=" + hid;
			}
        },
        Cancel: function() {
          dialog.dialog( "close" );
        }
      }
    });


	$("#AbandonNowButton").click(function(event){
      dialog.dialog( "open" );
	});
});