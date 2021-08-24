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
// define( "MOBILPAY_SENDER_NUMBER", "7415" ); // AICI PROBLEMA

$in_db = true; // folosit pentru teste, verificam daca astept de la numarul de telefon cod_confirmare  + da
$data = $_REQUEST;

// $smsOffline = new SmsOffline( $data );
$smsOffline = new SmsOffline();
$smsOffline->uniqueCode 		 = "p4321";
$smsOffline->mobilpayShortNumber = "7415";

$smsOffline->setOperator( $data ['destination'] );
$smsOffline->setPhoneNumber( $data ['sender'] );
		

/** to notify Merchant  */
$notify  = new Notify();
$notify->notifyURL = 'https://navid.ro/smsOff/merchant/';
$notify->verifyURL = 'https://navid.ro/smsOff/merchant/verifyOrder.php';

Log::setArrLog($_GET);
Log::setArrLog($smsOffline);
Log::setStrLog($smsOffline->operator);

if(in_array($smsOffline->operator, SmsOffline::OPERATOR_ARR)) {
	$notifyArr = array(
		'sender' 	=> $data['sender'],
		'message' 	=> $data['message'],
		'dateTime' 	=> $data['timestamp'] //use preg_match to make d-m-Y H:i:s
	); 
	/**
	 * Notify merchant for first time
	 * Merchant can save the information on Database
	 * */
	$notifyFeedback = $notify->sendNotify($notifyArr);

	if ($smsOffline->operator === SmsOffline::OPERATOR_COSMOTE_NAME) {
		$op = new Cosmote();
		$op->reciveMsg 	 		 = $data['message'];
		$op->phoneNumber 		 = $data['sender'];
		$op->operator 	 		 = SmsOffline::OPERATOR_COSMOTE_NAME;
		$op->uniqueCode  		 = $smsOffline->uniqueCode; // ?????? NOT HAPPY WITH THIS VAR
		$op->mobilpayShortNumber = $smsOffline->mobilpayShortNumber; // ?????? NOT HAPPY WITH THIS VAR too
		$op->merchantVerifyURL 	 = $notify->verifyURL;

		$op->makeOperation();
	}

	/** Customize it for Vodafone // CURRENTLY IS COSMOTE  */
	if ($smsOffline->operator === SmsOffline::OPERATOR_VODAFONE_NAME) {
		$op = new Cosmote();
		$op->mobilpayShortNumber = "7415"; // ??????????????????
		$op->reciveMsg 	 		 = $data['message'];
		$op->phoneNumber 		 = $data['sender'];
		$op->operator 	 		 = SmsOffline::OPERATOR_VODAFONE_NAME;
		$op->uniqueCode  		 = $smsOffline->uniqueCode; // ?????? NOT HAPPY WITH THIS VAR
		// $op->mobilpayShortNumber = $smsOffline->mobilpayShortNumber; // ?????? NOT HAPPY WITH THIS VAR too

		$op->makeOperation();
	}

	/** Customize it for Orange // CURRENTLY IS COSMOTE  */
	if ($smsOffline->operator === SmsOffline::OPERATOR_ORANGE_NAME) {
		$op = new Cosmote();
		$op->mobilpayShortNumber = "7415"; // ???????????????????????
		$op->reciveMsg 	 		 = $data['message'];
		$op->phoneNumber 		 = $data['sender'];
		$op->operator 	 		 = SmsOffline::OPERATOR_ORANGE_NAME;
		$op->uniqueCode  		 = $smsOffline->uniqueCode; // ?????? NOT HAPPY WITH THIS VAR
		// $op->mobilpayShortNumber = $smsOffline->mobilpayShortNumber; // ?????? NOT HAPPY WITH THIS VAR too
		
		$op->makeOperation();
	}
} else {
	throw new Exception( "The operation is not found!!!");
}


