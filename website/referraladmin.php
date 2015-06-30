<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	$login = new login(true);

	function getStatusString($status){
		switch ($status){
			case user::EXP_TYPE_MANUAL:
			case user::EXP_TYPE_PAID:
				return "Paid User";
			case user::EXP_TYPE_TRIAL:
			default:
				return "Trial User";
		}
	}

	function getListItemOutput($key, $value, $buttonText){
		return "<tr class='tableReferrals'>	
			<td>" . $key . "</td>
			<td>" . $value['rb_username'] . "</td>
			<td>" . $value['rb_useremail'] . "</td>
			<td>" . $value['rb_userpaypal'] . "</td>
			<td class='borderRight'>" . $value['rb_userstatus'] . "</td>
			
			<td>" . $value['r_username'] . "</td>
			<td>" . $value['r_useremail'] . "</td>
			<td>" . $value['r_userpaypal'] . "</td>
			<td class='borderRight'>" . $value['r_userstatus'] . "</td>
			<td><div class='buttonLink' data-referralid='".$value['rid']."'>".$buttonText."</td>
		</tr>";
	}



	$isPaidList = array();
	$isNotPaidList = array();
	$referrals = new referral();
	while($referrals->loadNext()){
		$listInfo = array();  
		$rid = $referrals->get_variable("referral_id");
		$uid = $referrals->get_variable("referral_referred_by_userid");
		$newUid = $referrals->get_variable("referral_referred_userid");
		$date = $referrals->get_variable("referral_date");
		$isPaid = $referrals->get_variable("referral_paid");
		$expireInfo = user::getUserExpirationDate($uid);
		$newExpireInfo = user::getUserExpirationDate($newUid);
		
		$user = new user();
		$user->set_variable("users_id", $uid);
		if (!$user->load()) continue;

		$newUser = new user();
		$newUser->set_variable("users_id", $newUid);
		if (!$newUser->load()) continue;

		$userPaypalId = paypal_transaction::getPaypalEmail($uid);
		$newUserPaypalId = paypal_transaction::getPaypalEmail($newUid);

		$listInfo['rid'] = $rid;
				
		$listInfo['rb_uid'] = $uid;
		$listInfo['rb_username'] = $user->get_variable("users_username");
		$listInfo['rb_useremail'] = $user->get_variable("users_email");
		$listInfo['rb_userpaypal'] = $userPaypalId;
		$listInfo['rb_userstatus'] = getStatusString($expireInfo['type']);
		
		$listInfo['r_uid'] = $newUid;
		$listInfo['r_username'] = $newUser->get_variable("users_username");
		$listInfo['r_useremail'] = $newUser->get_variable("users_email");
		$listInfo['r_userpaypal'] = $newUserPaypalId;
		$listInfo['r_userstatus'] = getStatusString($newExpireInfo['type']);
		
		if ($isPaid){
			array_push($isPaidList, $listInfo);
		} else {
			array_push($isNotPaidList, $listInfo);
		}
	}
		
	
?>


<!DOCTYPE html>
<html>
<head>
<title>BioBounce.com</title>

<meta name = "keywords" content = "biotech, stock, market, swing trading, stock trading" />
<meta name = "description" content = "" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, minimum-scale=1 user-scalable=no">

<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
<link href="css/biobounce.css" rel="stylesheet" type="text/css">
<link href="css/biomembers.css" rel="stylesheet" type="text/css">
<link href="css/bioadmin.css" rel="stylesheet" type="text/css">
<link href="css/biomembersnew.css" rel="stylesheet" type="text/css">
<link href="css/RESPONSIVE.css" rel="stylesheet" type="text/css">
<link href="css/responsive_admin.css" rel="stylesheet" type="text/css">

<link href='http://fonts.googleapis.com/css?family=Della+Respira' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Josefin+Slab' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Radley' rel='stylesheet' type='text/css'>



 <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script> 
<script type="text/javascript" src="js/jquery.uniform.min.js"></script> 
<script type="text/javascript" src="js/pageInit.js"></script> 
<script type="text/javascript" src="js/referrals.js"></script> 


</head>
<body>
<!-- Panel -->
<?php include 'navBarMain012715.php'; ?>
<?php include 'navSecBarAdminNew.php'; ?>

<div class = "newwrap userwrap">
	<div class="section n0 n1">
		<div id="maintitle">
			Pending Referrals<br/>
		</div >
		<div class="tablegroup">
				<table id="PendingTable" class="watchtable" cellspacing="0" cellpadding ="0" border="0">
					<thead>
						<tr class="table_head">
							<th class="bottom left"></th>
							<th class="bottom borderRight" colspan="4">REFERRED BY</th>
							<th class="bottom borderRight" colspan="4">NEW USER REFERRED</th>
							<th class="bottom"></th>
						</tr>

						<tr class="table_head">
							<th class="bottom left">#</th>
							<th class="bottom">User Name</th>
							<th class="bottom">Email</th>
							<th class="bottom">PaypalID</th>
							<th class="bottom borderRight">Status</th>
							<th class="bottom">User Name</th>
							<th class="bottom">Email</th>
							<th class="bottom">PaypalID</th>
							<th class="bottom borderRight">Status</th>
							<th class="bottom"></th>
						</tr>
					</thead>
					<tbody>
<?php

	$outStr = "";
	foreach($isNotPaidList as $key=>$value){
		$outStr .= getListItemOutput($key, $value, "Mark Paid");
	}
	echo $outStr;

?>					
						
					</tbody>
				</table>
		</div>
	</div>
</div>

<div class = "newwrap userwrap">
	<div class="section n2">
		<div id="maintitle">
			Paid Referrals<br/>
		</div >
		<div class="tablegroup">
				<table id="PaidTable"  class="watchtable" cellspacing="0" cellpadding ="0" border="0">
					<thead>
						<tr class="table_head">
							<th class="bottom left"></th>
							<th class="bottom borderRight" colspan="4">REFERRED BY</th>
							<th class="bottom borderRight" colspan="4">NEW USER REFERRED</th>
							<th class="bottom"></th>
						</tr>

						<tr class="table_head">
							<th class="bottom left">#</th>
							<th class="bottom">User Name</th>
							<th class="bottom">Email</th>
							<th class="bottom">PaypalID</th>
							<th class="bottom borderRight">Status</th>
							<th class="bottom">User Name</th>
							<th class="bottom">Email</th>
							<th class="bottom">PaypalID</th>
							<th class="bottom borderRight">Status</th>
							<th class="bottom"></th>
						</tr>
					</thead>
					<tbody>
<?php
	$outStr = "";
	foreach($isPaidList as $key=>$value){
		$outStr .= getListItemOutput($key, $value, "Mark Pending");
	}
	echo $outStr;
?>					
						
					</tbody>
				</table>
		</div>
	</div>
</div>
</body>
</html>