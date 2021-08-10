<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('lib/smsOffline.php');
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
// Log::setArrLog(getallheaders());

// define( "MOBILPAY_UNIQUE_CODE", "tst" );
define( "MOBILPAY_SENDER_NUMBER", "7415" );
$in_db = true; // folosit pentru teste, verificam daca astept de la numarul de telefon cod_confirmare  + da

$smsOffline = new SmsOffline( $_REQUEST );
$smsOffline->uniqueCode = "tst";

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
}

/** 
 * Now, to decide what to do next
 */

//pentru Orange
if ($smsOffline->operator === SmsOffline::OPERATOR_ORANGE_NAME) {
	if ($_GET ['message'] == MOBILPAY_UNIQUE_CODE) {
		SmsOffline::sendResponse( "Va multumim pentru plata efectuata!", 1, 0 ); // la orange se taxeaza din prima
	} else { // in cazul in care codul nu este cel pe care il asteptam
		SmsOffline::sendResponse( "Mesajul nu este corect, va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . " un SMS cu codul " . MOBILPAY_UNIQUE_CODE, 0, 0 );
	}
}

//pentru Vodafone
if ($smsOffline->operator === SmsOffline::OPERATOR_VODAFONE_NAME) { // la vodafone, tranzactia se desfasoara in 2 pasi
	if ($_GET ['message'] == MOBILPAY_UNIQUE_CODE) { // initiem plata, la primul sms
		// verificam daca in baza de date exista numarul de telefon $_GET[sender] pentru care se astepata raspunsul MOBILPAY_UNIQUE_CODE+da
		//daca nu, il inseram in baza de date si il rugam sa trimtia codul + da
		SmsOffline::sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . " " . MOBILPAY_UNIQUE_CODE . " da", 0, 0 );
	
	} elseif ($_GET ['message'] == MOBILPAY_UNIQUE_CODE . " da") {
		// verificam in baza de date daca a trimis initial cuvantul  MOBILPAY_UNIQUE_CODE
		if ($in_db) { // daca da
			// daca are bani in cont, si plata se va face, va primi raspunsul trimis acum:
			SmsOffline::sendResponse( "Pentru a avea acces la ce ati comandat introduceti codul UN_COD_UNIC", 1, 0 ); // UN_COD_UNIC este dat de noi, si folosit tot de noi pentru identificare/procesare comanda
			// sau putem verifica in contul din mobilpay dupa nr. de telefon, si raspunsul de mai sus sa fie unul de multumiri
		} else { //daca nu este in baza de date retrimitem inca odata un sms free
			SmsOffline::sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . " " . MOBILPAY_UNIQUE_CODE . " da", 0, 0 );
		}
	} else { // in cazul in care sms-ul primit este gol
		SmsOffline::sendResponse( "Mesajul nu este corect, va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . " un SMS cu codul " . MOBILPAY_UNIQUE_CODE, 0, 0 );
	}
}

//pentru Cosmote
/* 
 *	la fel ca la vodafone, si la cosmote tranzactia se desfasoara in doua etape, cu specificatia ca SMS-ul 2 se trimite la MOBILPAY_SENDER_NUMBER + 0
 */
if ($smsOffline->operator === SmsOffline::OPERATOR_COSMOTE_NAME) {
	if ($_GET ['message'] == MOBILPAY_UNIQUE_CODE) { // initiem plata
		// verificam daca in baza de date exista numarul de telefon $_GET[sender] si care astepta raspunsul
		//daca nu, il inseram in baza de date si il rugam sa trimtia codul + da
		SmsOffline::sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . "0 " . MOBILPAY_UNIQUE_CODE . " da", 0, 0 );
	
	} elseif ($_GET ['message'] == MOBILPAY_UNIQUE_CODE . " da") {
		// verificam in baza de date daca a trimis initial cuvantul  de test
		// daca da
		

		if ($in_db) {
			// daca are bani in cont, si plata se va face, va primi raspunsul trimis acum:
			SmsOffline::sendResponse( "Pentru a avea acces la ce ati comandat introduceti codul UN_COD_UNIC", 1, 0 );
		} else {
			SmsOffline::sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . "0 " . MOBILPAY_UNIQUE_CODE . " da", 0, 0 );
		}
	} else {
		SmsOffline::sendResponse( "Mesajul nu este corect, va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . " un SMS cu codul " . MOBILPAY_UNIQUE_CODE, 0, 0 );
	}
}