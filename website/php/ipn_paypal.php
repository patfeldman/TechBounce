<?php

  require_once('db_interface/autoload.php');

	  // Send an empty HTTP 200 OK response to acknowledge receipt of the notification 
  
  /*
  // Build the required acknowledgement message out of the notification just received
  $req = 'cmd=_notify-validate';               // Add 'cmd=_notify-validate' to beginning of the acknowledgement

  foreach ($_POST as $key => $value) {         // Loop through the notification NV pairs
    $value = urlencode(stripslashes($value));  // Encode these values
    $req  .= "&$key=$value";                   // Add the NV pairs to the acknowledgement
  }

  */
		
	header('HTTP/1.1 200 OK'); 
	
	// Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
	// Instead, read raw POST data from the input stream. 
	$raw_post_data = file_get_contents('php://input');
	$raw_post_array = explode('&', $raw_post_data);
	$myPost = array();
	foreach ($raw_post_array as $keyval) {
	  $keyval = explode ('=', $keyval);
	  if (count($keyval) == 2)
	     $myPost[$keyval[0]] = urldecode($keyval[1]);
	}
	// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
	$req = 'cmd=_notify-validate';
	if(function_exists('get_magic_quotes_gpc')) {
	   $get_magic_quotes_exists = true;
	} 
	foreach ($myPost as $key => $value) {        
	   if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
	        $value = urlencode(stripslashes($value)); 
	   } else {
	        $value = urlencode($value);
	   }
	   $req .= "&$key=$value";
	}
		
	// Open a socket for the acknowledgement request
	$ch = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
	
	if( !($res = curl_exec($ch)) ) {
		curl_close($ch);
		exit;
	}
	curl_close($ch);
	
	$debugString = "\nRECEIVED: " . print_r($raw_post_array , true);
	
//	if (strcmp ($res, "VERIFIED") == 0) {
	// Assign payment notification values to local variables
	    
		$paypalTxn = new paypal_transaction();

		$recordTxn = false;
		// check to make sure we didn't already record this transaction	
		if (!empty($_POST['txn_id'])){
			$paypalTxn->set_variable('bp_paypal_txn_id', $_POST['txn_id']); 
			if ($paypalTxn->load()){
				$recordTxn = false;
				$debugString .= "\nTransaction already recorded\n";
			} else {
				$recordTxn = true;
			}						
		}
		
		if ($recordTxn){
			$paypalTxn->set_variable('bp_biobounce_uid', $_POST['custom']);
			$paypalTxn->set_variable('bp_paypal_payer_id', $_POST['payer_id']); 
			$paypalTxn->set_variable('bp_paypal_txn_id', $_POST['txn_id']); 
			$paypalTxn->set_variable('bp_paypal_txn_type', $_POST['txn_type']); 
			$paypalTxn->set_variable('bp_paypal_username', $_POST['username']); 
			$paypalTxn->set_variable('bp_paypal_mc_gross ', $_POST['mc_gross']); 
			$paypalTxn->set_variable('bp_paypal_option_name', $_POST['option_name1']); 
			$paypalTxn->set_variable('bp_paypal_option_selection', $_POST['option_selection1']); 
			$paypalTxn->set_variable('bp_paypal_email', $_POST['payer_email']); 
			$paypalTxn->set_variable('bp_paypal_mc_fee ', $_POST['mc_fee']); 
			$paypalTxn->set_variable('bp_paypal_zip', $_POST['address_zip']); 
			$paypalTxn->set_variable('bp_payment_status', $_POST['payment_status']); 
			$paydate = $_POST['payment_date'];
			$timestamp = date('Y-m-d', strtotime($paydate));  
			$paypalTxn->set_variable('bp_payment_date', $timestamp);
			$paypalTxn->createNew();
		}

		$debugString.= "\nProcessed\n";
//	} 
	
	if (strcmp ($res, "INVALID") == 0) {
		$debugString.= "\nINVALID - DON'T KNOW WHY!!!\n";
		$debugString .= "\nRECEIVED: " . print_r($raw_post_array , true);
		$raw_post_array = explode('&', $req);
		$debugString .= "\n SEND BACK: " . print_r($raw_post_array, true);
		$debugString .= "\nREPLY FROM PAYPAL::". $res;
		
	}
	
	
	// try to resolve this or any other outstanding payment ids
	payment_info::reconcileAllPaymentUids();
	
	$my_file = 'debug.txt';
	$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly creates file
	fwrite($handle, $debugString); 
	fclose($handle);
			