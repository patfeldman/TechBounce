var Referrals = {};

Referrals.Initialize= function(){
	Referrals.$paidTable = $('#PaidTable');
	Referrals.$pendingTable = $('#PendingTable');
	Referrals.$paidTable.on('click', function(e){
		var $target = $(e.target);
		if (!$target.hasClass("buttonLink")) return;
		$target.text("Mark Paid");
		var $table = $target.closest(".watchtable");
		var $tableRow = $target.closest("tr");
		var $rowMove = $tableRow.clone();
		
		$tableRow.remove();
		Referrals.$pendingTable.append($rowMove);

		var saveData = {};
		saveData.rid = $target.attr("data-referralid");
		saveData.isPaid = false;
		
		$.ajax({
			url: "changeReferralPaidStatus.php",
			type: "POST",
			data: saveData
		});

	});

	Referrals.$pendingTable.on('click', function(e){
		var $target = $(e.target);
		if (!$target.hasClass("buttonLink")) return;
		$target.text("Mark Pending");
		var $table = $target.closest(".watchtable");
		var $tableRow = $target.closest("tr");
		var $rowMove = $tableRow.clone();
		$tableRow.remove();
		Referrals.$paidTable.append($rowMove);			

		var saveData = {};
		saveData.rid = $target.attr("data-referralid");
		saveData.isPaid = true;
		
		$.ajax({
			url: "changeReferralPaidStatus.php",
			type: "POST",
			data: saveData
		});

	});
};


$(function() {
	Referrals.Initialize();
});
