<?php
	//Include the PS_Pagination class
	require_once('php/db_interface/autoload.php');
	$login = new login(true);
	
	if (!empty($_POST)){
		foreach ($_POST as $key=>$date){
			if (!empty($date) && strpos($key,'date') !== false){
				$uid = intval(str_replace("date", "", $key));
				$user = new user();
				$user->set_variable('users_id', $uid);
				if ($user->load()){
					$user->set_variable('users_manualexpdate', date('Y-m-d', strtotime($date)));
					$user->update();
				}
			}
		}
	}


	$users = user::getAllUserInfo();
	$expiredUsers = array();
	if (isset($users['expired'][user::EXP_TYPE_TRIAL]))
		$expiredUsers = array_merge($expiredUsers, $users['expired'][user::EXP_TYPE_TRIAL]);
	if (isset($users['expired'][user::EXP_TYPE_PAID]))
		$expiredUsers = array_merge($expiredUsers, $users['expired'][user::EXP_TYPE_PAID]);
	if (isset($users['expired'][user::EXP_TYPE_MANUAL]))
		$expiredUsers = array_merge($expiredUsers, $users['expired'][user::EXP_TYPE_MANUAL]);
	
	
	$trialUsers = $users['valid'][user::EXP_TYPE_TRIAL];
	$paidUsers = $users['valid'][user::EXP_TYPE_MANUAL];
	if (isset($users['valid'][user::EXP_TYPE_PAID]))
		$paidUsers = array_merge($paidUsers, $users['valid'][user::EXP_TYPE_PAID]);


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


<!-- Sliding effect -->
<script type="text/javascript">
$(document).ready(function() {
	$( ".datepicker" ).datepicker();
	$("#memberTable").tablesorter();
	$("#trialTable").tablesorter();
	$("#unregistedTable").tablesorter();
	$("#expiredTable").tablesorter();
	
	

});	


</script>

</head>

<body>
<?php include 'navBarMain012715.php'; ?>
<?php include 'navSecBarAdminNew.php'; ?>

<form class="clearfix"  action="useradmin.php" method="post">

<div class = "newwrap userwrap">
	<div class="section n0 n1">
		<div id="maintitle">
			Trial Users<br/>
		</div >
		<div class="warning">
		</div>
		<div class="tablegroup">
				<table id="trialTable" class="watchtable" cellspacing="0" cellpadding ="0" border="0">
					<thead>
						<tr class="table_head">
							<th class="bottom left">#</td>
							<th class="bottom">user name</td>
							<th class="bottom">email </td>
							<th class="bottom">pe</td>
							<th class="bottom">pt</td>
							<th class="bottom">re</td>
							<th class="bottom">rt</td>
							<th class="bottom">se</td>
							<th class="bottom">st</td>
							<th class="bottom">last</td>
							<th class="bottom">exp<br/>(days)</td>
							<td class="bottom">type</td>
							<th class="bottom">ip</td>
							<td class="bottom">new exp</td>
						</tr>
					</thead>
					<tbody>
<?php
	$counter = 0;
	$tablestate = "row_odd";
	$emailList = array();
	
	foreach ($trialUsers as $user){
		$counter ++;
		$id = $user['users_id'];
		$creationdate=date("m/d/Y", strtotime($user['users_creationdate']));
		$lastlogin = date("m/d/Y", strtotime($user['users_lastlogindate']));
		$manualdate = date("m/d/Y", strtotime($user['users_manualexpdate']));

		if (intval(date("Y", strtotime($user['users_creationdate'])) < 2013)){
			$creationdate = "-";
		}
		if (intval(date("Y", strtotime($user['users_lastlogindate'])) < 2013)){
			$lastlogin = "-";
		}
		if (intval(date("Y", strtotime($user['users_manualexpdate'])) < 2013)){
			$manualdate = "-";
		}
		
		
		$ipAddr = $user['users_ipaddress'];
		$otherUserId = intval($user['users_dupid']); 
		$otherUserHtml = "";
		$tableDup ="";
		if ($otherUserId  > 0){
			$tableDup = "dupId";
			$otherUserInfo = new user();
			$otherUserInfo->set_variable('users_id', $otherUserId);
			$otherUserInfo->load();			
			$otherUserHtml = '(<a href="indiv.php?uid=' .$otherUserId. '">' . $otherUserInfo->get_variable('users_username'). '</a>)';			
		}

		$updateString = array();
		$updateString[]= $user['users_send_email_updates'] ;
		$updateString[]= $user['users_send_text_updates'] ;
		$updateString[]= $user['users_send_reversal_email_updates'] ;
		$updateString[]= $user['users_send_reversal_text_updates'] ;
		$updateString[]= $user['users_send_short_email_updates'] ;
		$updateString[]= $user['users_send_short_text_updates'] ;
				
		$expirationdate = $user['expdate'];
		$now = new DateTime(date("Y-m-d"));
		$expiresin = intval($now->diff($expirationdate)->format("%r%a"));
		$emailAddress = $user['users_email'];
		$emailList[] = $emailAddress;
		
//		echo '<input type="hidden" name="'.$counter.'" value="'.$id.'"/>' . "\n";
		echo '<tr class="table_row '. $tablestate. ' ' . $tableDup .'">'. "\n";
		echo '<td class="left">' . $counter . '</td>'. "\n";
		echo '<td><a href="indiv.php?uid=' .$id. '">' . $user['users_username']. '</a>'.$otherUserHtml.'</td>'. "\n";
		echo '<td>' . $user['users_email']. '</td>'. "\n";
		echo '<td>' . $updateString[0] . '</td>'. "\n";
		echo '<td>' . $updateString[1] . '</td>'. "\n";
		echo '<td>' . $updateString[2] . '</td>'. "\n";
		echo '<td>' . $updateString[3] . '</td>'. "\n";
		echo '<td>' . $updateString[4] . '</td>'. "\n";
		echo '<td>' . $updateString[5] . '</td>'. "\n";
		echo '<td>' . $lastlogin . '</td>'. "\n";
		echo '<td>' . $expiresin . '</td>'. "\n";
		echo '<td>' . $user['exptype']. '</td>'. "\n";
		echo '<td class="reallySmall">' . $ipAddr. '</td>'. "\n";
		echo '<td><input type="text" class="datepicker" name="date'.$id.'"></td>'. "\n";
		echo '</tr>'. "\n";
		if ($tablestate=='row_odd') $tablestate = 'row_even';
		else $tablestate = 'row_odd';
	}
		
?>					
						
					</tbody>
				</table>
				<div class="bt_wrap"><input type="submit" name="submit" value="Save Changes" class="bt_login" /></div>
				<div class="addressList"><?php echo implode(", ", $emailList)  ?></div>
		</div>

	</div>
</div>


<div class = "newwrap userwrap">
	<div class="section n2">
		<div id="maintitle">
			Paid Users<br/>
		</div >
		<div class="warning">
		</div>
		<div class="tablegroup">
				<table id="memberTable" class="watchtable" cellspacing="0" cellpadding ="0" border="0">
					<thead>
						<tr class="table_head">
							<th class="bottom left">#</td>
							<th class="bottom">user name</td>
							<th class="bottom">email </td>
							<th class="bottom">pe</td>
							<th class="bottom">pt</td>
							<th class="bottom">re</td>
							<th class="bottom">rt</td>
							<th class="bottom">se</td>
							<th class="bottom">st</td>
							<th class="bottom">last</td>
							<th class="bottom">expDate</td>
							<th class="bottom">exp<br/>(days)</td>
							<th class="bottom">type</td>
							<th class="bottom">new exp date</td>
						</tr>
					</thead>
					<tbody>
<?php
	$counter = 0;
	$tablestate = "row_odd";
	$emailList = array();
	foreach ($paidUsers as $user){
		$counter ++;
		$id = $user['users_id'];
		$creationdate=date("m/d/Y", strtotime($user['users_creationdate']));
		$lastlogin = date("m/d/Y", strtotime($user['users_lastlogindate']));
		$manualdate = date("m/d/Y", strtotime($user['users_manualexpdate']));

		if (intval(date("Y", strtotime($user['users_creationdate'])) < 2013)){
			$creationdate = "-";
		}
		if (intval(date("Y", strtotime($user['users_lastlogindate'])) < 2013)){
			$lastlogin = "-";
		}
		
		if ($user['exptype']){}
		if (intval(date("Y", strtotime($user['users_manualexpdate'])) < 2013)){
			$manualdate = "-";
		}
		
		$updateString = array();
		$updateString[]= $user['users_send_email_updates'] ;
		$updateString[]= $user['users_send_text_updates'] ;
		$updateString[]= $user['users_send_reversal_email_updates'] ;
		$updateString[]= $user['users_send_reversal_text_updates'] ;
		$updateString[]= $user['users_send_short_email_updates'] ;
		$updateString[]= $user['users_send_short_text_updates'] ;
				
		
		
		$expirationdate = $user['expdate'];
		$now = new DateTime(date("Y-m-d"));
		$expiresin = intval($now->diff($expirationdate)->format("%r%a"));
		$emailAddress = $user['users_email'];
		$emailList[] = $emailAddress;
		
		echo '<tr class="table_row '. $tablestate. ' ">'. "\n";
		echo '<td class="left">' . $counter . '</td>'. "\n";
		//echo '<td class="left">' . $id . '</td>'. "\n";
		echo '<td><a href="indiv.php?uid=' .$id. '">' . $user['users_username']. '</a></td>'. "\n";
		echo '<td>' . $emailAddress. '</td>'. "\n";
		echo '<td>' . $updateString[0] . '</td>'. "\n";
		echo '<td>' . $updateString[1] . '</td>'. "\n";
		echo '<td>' . $updateString[2] . '</td>'. "\n";
		echo '<td>' . $updateString[3] . '</td>'. "\n";
		echo '<td>' . $updateString[4] . '</td>'. "\n";
		echo '<td>' . $updateString[5] . '</td>'. "\n";
		echo '<td>' . $lastlogin . '</td>'. "\n";
		echo '<td>' . $manualdate. '</td>'. "\n";
		echo '<td>' . $expiresin . ' </td>'. "\n";
		echo '<td>' . $user['exptype']. '</td>'. "\n";
		echo '<td><input type="text" class="datepicker" name="date'.$id.'"></td>'. "\n";
		echo '</tr>'. "\n";
		if ($tablestate=='row_odd') $tablestate = 'row_even';
		else $tablestate = 'row_odd';
	}
		
?>					
						
					</tbody>
				</table>
				<div class="bt_wrap"><input type="submit" name="submit" value="Save Changes" class="bt_login" /></div>
				<div class="addressList"><?php echo implode(", ", $emailList)  ?></div>
		</div>

	</div>
</div>

<div class = "newwrap userwrap">
	<div class="section n3">
		<div id="maintitle">
			Expired Users<br/>
		</div >
		<div class="warning">
		</div>
		<div class="tablegroup">
				<table id="expiredTable" class="watchtable" cellspacing="0" cellpadding ="0" border="0">
					<thead>
						<tr class="table_head">
							<th class="bottom left">#</td>
							<th class="bottom">user name</td>
							<th class="bottom">email </td>
							<th class="bottom">created </td>
							<th class="bottom">last</td>
							<th class="bottom">exp<br/>(days)</td>
							<td class="bottom">type</td>
							<th class="bottom">ip</td>
							<td class="bottom">new exp</td>
						</tr>
					</thead>
					<tbody>
<?php
	$counter = 0;
	$tablestate = "row_odd";
	$emailList = array();
	
	foreach ($expiredUsers as $user){
		if (intval(date("Y", strtotime($user['users_creationdate'])) < 2013)) continue;
		
		$counter ++;
		$id = $user['users_id'];
		$lastlogin = date("m/d/Y", strtotime($user['users_lastlogindate']));
		$manualdate = date("m/d/Y", strtotime($user['users_manualexpdate']));
		$creationdate=date("m/d/Y", strtotime($user['users_creationdate']));
		
		if (intval(date("Y", strtotime($user['users_creationdate'])) < 2013)){
			$creationdate = "-";
		}
		if (intval(date("Y", strtotime($user['users_lastlogindate'])) < 2013)){
			$lastlogin = "-";
		}
		if (intval(date("Y", strtotime($user['users_manualexpdate'])) < 2013)){
			$manualdate = "-";
		}
		
		
		$ipAddr = $user['users_ipaddress'];
		$tableDup = "";
		$otherUserId = intval($user['users_dupid']); 
		$otherUserHtml = "";
		if ($otherUserId  > 0){
			$tableDup = "dupId";
			$otherUserInfo = new user();
			$otherUserInfo->set_variable('users_id', $otherUserId);
			$otherUserInfo->load();			
			$otherUserHtml = '(<a href="indiv.php?uid=' .$otherUserId. '">' . $otherUserInfo->get_variable('users_username'). '</a>)';			
		}
		
		
		$expirationdate = $user['expdate'];
		$now = new DateTime(date("Y-m-d"));
		$expiresin = intval($now->diff($expirationdate)->format("%r%a"));
		$emailAddress = $user['users_email'];
		$emailList[] = $emailAddress;
		
		
//		echo '<input type="hidden" name="'.$counter.'" value="'.$id.'"/>' . "\n";
		echo '<tr class="table_row '. $tablestate. ' ">'. "\n";
		echo '<td class="left">' . $counter . '</td>'. "\n";
		echo '<td><a href="indiv.php?uid=' .$id. '">' . $user['users_username']. '</a>'.$otherUserHtml.'</td>'. "\n";
		echo '<td>' . $user['users_email']. '</td>'. "\n";
		echo '<td>' . $creationdate . '</td>'. "\n";
		echo '<td>' . $lastlogin . '</td>'. "\n";
		echo '<td>' . $expiresin . '</td>'. "\n";
		echo '<td>' . $user['exptype']. '</td>'. "\n";
		echo '<td class="reallySmall">' . $ipAddr. '</td>'. "\n";
		echo '<td><input type="text" class="datepicker" name="date'.$id.'"></td>'. "\n";
		echo '</tr>'. "\n";
		if ($tablestate=='row_odd') $tablestate = 'row_even';
		else $tablestate = 'row_odd';
	}
		
?>					
						
					</tbody>
				</table>
				<div class="bt_wrap"><input type="submit" name="submit" value="Save Changes" class="bt_login" /></div>
				<div class="addressList"><?php echo implode(", ", $emailList)  ?></div>
		</div>

	</div>
</div>

<div class = "newwrap userwrap">
	<div class="section n4">
		<div id="maintitle">
			Unregistered Users<br/>
		</div >
		<div class="warning">
		</div>
		<div class="tablegroup">
				<table id="unregistedTable" class="watchtable" cellspacing="0" cellpadding ="0" border="0">
					<thead>
						<tr class="table_head">
							<th class="bottom left">#</td>
							<th class="bottom">user name</td>
							<th class="bottom">email </td>
							<td class="bottom">type</td>
							<td class="bottom">new exp</td>
						</tr>
					</thead>
					<tbody>
<?php
	$counter = 0;
	$tablestate = "row_odd";
	$emailList = array();
	foreach ($expiredUsers as $user){
		$creationdate=date("m/d/Y", strtotime($user['users_creationdate']));
		if (intval(date("Y", strtotime($user['users_creationdate'])) >= 2013)) continue;
		
		$emailAddress = $user['users_email'];
		$emailList[] = $emailAddress;
		
		$counter ++;
		$id = $user['users_id'];		
//		echo '<input type="hidden" name="'.$counter.'" value="'.$id.'"/>' . "\n";
		echo '<tr class="table_row '. $tablestate. ' ">'. "\n";
		echo '<td class="left">' . $counter . '</td>'. "\n";
		echo '<td><a href="indiv.php?uid=' .$id. '">' . $user['users_username']. '</a></td>'. "\n";
		echo '<td>' . $user['users_email']. '</td>'. "\n";
		echo '<td>' . $user['exptype']. '</td>'. "\n";
		echo '<td><input type="text" class="datepicker" name="date'.$id.'"></td>'. "\n";
		echo '</tr>'. "\n";
		if ($tablestate=='row_odd') $tablestate = 'row_even';
		else $tablestate = 'row_odd';
	}
		
?>					
						
					</tbody>
				</table>
				<div class="bt_wrap"><input type="submit" name="submit" value="Save Changes" class="bt_login" /></div>
				<div class="addressList"><?php echo implode(", ", $emailList)  ?></div>
		</div>

	</div>
</div>


</form>
</body>
</html>