<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('lib/smsOffline.php');
require_once('lib/cosmote.php');
require_once('lib/notify.php');
require_once('lib/log.php');

if(isset($_GET['rmLog'])) {
    /** Temporary to DELETE Log file */
    log::rmLog($_GET['rmLog']);
    exit;
}

/**
 * Just to log in Developing
 */
// $httpInput = file_get_contents('php://input'); // Takes raw data from the request
// Log::setArrLog(getallheaders());
// Log::setArrLog($httpInput);

// exit; // Temporary STOP

// define( "MOBILPAY_UNIQUE_CODE", "tst" );
define( "MOBILPAY_SENDER_NUMBER", "7415" ); // AICI PROBLEMA
$in_db = true; // folosit pentru teste, verificam daca astept de la numarul de telefon cod_confirmare  + da

$smsOffline = new SmsOffline( $_REQUEST );
// $smsOffline->uniqueCode 		 = "tst";
$smsOffline->uniqueCode 		 = "p4321";
$smsOffline->mobilpayShortNumber = "7415";


/** to notify Merchant  */
$notify  = new Notify();
$notify->notifyURL = 'https://navid.ro/smsOff/merchant/';

Log::setArrLog($_GET);
Log::setStrLog($smsOffline->operator);

if(in_array($smsOffline->operator, SmsOffline::OPERATOR_ARR)) {
	$notifyArr = array(
		'sender' 	=> $_REQUEST['sender'],
		'message' 	=> $_REQUEST['message'],
		'dateTime' 	=> $_REQUEST['timestamp'] //use preg_match to make d-m-Y H:i:s
	); 
	$notifyFeedback = $notify->sendNotify($notifyArr); // Notify for first time

	if ($smsOffline->operator === SmsOffline::OPERATOR_COSMOTE_NAME) {
		$op = new Cosmote();
		$op->mobilpayShortNumber = "7415";
		$op->reciveMsg 	 		 = $_REQUEST['message'];
		$op->phoneNumber 		 = $_REQUEST['sender'];
		$op->operator 	 		 = SmsOffline::OPERATOR_COSMOTE_NAME;
		$op->uniqueCode  		 = $smsOffline->uniqueCode; // ?????? NOT HAPPY WITH THIS VAR
		// $op->mobilpayShortNumber = $smsOffline->mobilpayShortNumber; // ?????? NOT HAPPY WITH THIS VAR too

		$op->makeOperation();
	}

	if ($smsOffline->operator === SmsOffline::OPERATOR_VODAFONE_NAME) {
		$op = new Cosmote();
		$op->mobilpayShortNumber = "7415";
		$op->reciveMsg 	 		 = $_REQUEST['message'];
		$op->phoneNumber 		 = $_REQUEST['sender'];
		$op->operator 	 		 = SmsOffline::OPERATOR_VODAFONE_NAME;
		$op->uniqueCode  		 = $smsOffline->uniqueCode; // ?????? NOT HAPPY WITH THIS VAR
		// $op->mobilpayShortNumber = $smsOffline->mobilpayShortNumber; // ?????? NOT HAPPY WITH THIS VAR too

		$op->makeOperation();
	}

	if ($smsOffline->operator === SmsOffline::OPERATOR_ORANGE_NAME) {
		$op = new Cosmote();
		$op->mobilpayShortNumber = "7415";
		$op->reciveMsg 	 		 = $_REQUEST['message'];
		$op->phoneNumber 		 = $_REQUEST['sender'];
		$op->operator 	 		 = SmsOffline::OPERATOR_ORANGE_NAME;
		$op->uniqueCode  		 = $smsOffline->uniqueCode; // ?????? NOT HAPPY WITH THIS VAR
		// $op->mobilpayShortNumber = $smsOffline->mobilpayShortNumber; // ?????? NOT HAPPY WITH THIS VAR too
		
		$op->makeOperation();
	}
} else {
	die('SNED REPLY SMS WITH MSG, THE OPERATION IS NOT FOUND!!!');
}


