var Settings = {};
Settings.MarkUpdateNeeded = function(){
	$("#updateBtn").removeClass("updateOff");
	$("#updateBtn").addClass("updateOn");
};

Settings.SaveSettings = function(){
	var saveData = {};
	saveData.esub = $("#emailCheckBox").prop('checked');
	saveData.essub = $("#shortEmailCheckBox").prop('checked');
	saveData.ersub = $("#reversalEmailCheckBox").prop('checked');
	saveData.tsub = $("#textCheckBox").prop('checked');
	saveData.tssub = $("#shortTextCheckBox").prop('checked');
	saveData.trsub = $("#reversalTextCheckBox").prop('checked');
	saveData.tnum = $('#telephoneNumber').val();
	saveData.tprovider = $("#textNetwork").val();

	$.ajax({
		url: "saveSettings.php",
		type: "POST",
		data: saveData	
	})
	.done(function( data ) {
		$("#savedInformation").parent().removeClass("hidden");
		if (data == "SUCCESS"){
			$("#savedInformation").text("UPDATES SAVED!");
			//$("#savedInformation").text(data);			
		} else {
			$("#savedInformation").text("UPDATES FAILED! TRY AGAIN LATER.");			
			//$("#savedInformation").text(data);			
		}
		$("#updateBtn").removeClass("updateOn");
		$("#updateBtn").addClass("updateOff");
		setTimeout(function(){
			$("#savedInformation").parent().addClass("hidden");
		}, 3000);
	});

};

Settings.Initialize = function(){
	$('.telephone').mask('(000) 000-0000');
	if ($('#telephoneNumber').val().length == 0) {
		$('#telephoneNumber').val("Enter Phone Number");
		$('#telephoneNumber').addClass("hintText");
	}
	
	if ($("#textCheckBox").prop("checked") || $("#shortTextCheckBox").prop("checked") || $("#reversalTextCheckBox").prop("checked") ){
		$("#phoneInfo").removeClass("none");
	} else {
		$("#phoneInfo").addClass("none");
	}
	$("#textCheckBox, #shortTextCheckBox, #reversalTextCheckBox").change(function(){
		if ($("#textCheckBox").prop("checked") || $("#shortTextCheckBox").prop("checked") || $("#reversalTextCheckBox").prop("checked") ){
				$("#phoneInfo").removeClass("none");
			} else {
				$("#phoneInfo").addClass("none");
			}
		}
	);
	$('#telephoneNumber').click(function(){
		Settings.MarkUpdateNeeded();
		$(this).val("");
		$(this).removeClass("hintText");
	});
	$('#telephoneNumber').blur(function(){
		if ($(this).val().length == 0) {
			$(this).val("Enter Phone Number");
			$(this).addClass("hintText");
		} 
	});	


	$("#textNetwork").change(function(){
		Settings.MarkUpdateNeeded();
	});
	
	$(".personalHolding").change(function(){
		Settings.MarkUpdateNeeded();
	});
	
	$("#updateBtn").click(Settings.SaveSettings);
};

$(function() {
	Settings.Initialize();
});
