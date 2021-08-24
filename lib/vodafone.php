<?php
/**
 * pentru Vodafone
 * la vodafone, tranzactia se desfasoara in 2 pasi
 */
require_once('lib/notify.php');

class Vodafone extends SmsOffline {
	public $reciveMsg; 			 // the message what Cellphone owner sent
	public function __construct() {
		parent::__construct();
	}

	/**
	 * initiem plata
	 */
	public function makeOperation() {
		$merchant  = new Notify();
		$merchant->verifyURL = $this->merchantVerifyURL;
		$verifyArr = array(
			'sender' 	=> $this->phoneNumber
		);

		switch($this->reciveMsg) {
			case $this->uniqueCode:
				// initiem plata, la primul sms
				// verificam daca in baza de date exista numarul de telefon $_GET[sender] pentru care se astepata raspunsul MOBILPAY_UNIQUE_CODE+da
				//daca nu, il inseram in baza de date si il rugam sa trimtia codul + da
				$verifyResult 	 = $merchant->getVerify($verifyArr);
				$verifyResultArr = json_decode($verifyResult);
				if($verifyResultArr->status) {
					$this->sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . $this->mobilpayShortNumber . " " . $this->uniqueCode . " da", 0, 0 );
				} else {
					$this->sendResponse( "Va rugam trimiteti inca o data SMS la " . $this->mobilpayShortNumber . " cu  " . $this->uniqueCode , 0, 0 );
				}
			break;
			case $this->uniqueCode.strtolower(self::MOBILPAY_CONFIRM_KEY):
				// verificam in baza de date daca a trimis initial cuvantul  MOBILPAY_UNIQUE_CODE
				$verifyResult 	 = $merchant->getVerify($verifyArr);
				$verifyResultArr = json_decode($verifyResult);

				/*
				* daca are bani in cont, si plata se va face, va primi raspunsul trimis acum
				* daca staus e TRUE intamna ca are bani,... si tot e in regula
				* alfel respuns o sa fii Negativ 
				*/ 
				if ($verifyResultArr->status) { // daca da
					// daca status e TRUE 
					$this->sendResponse( "Comanda Dvs a confirmat cu success, si codul de confirm este : ".$verifyResultArr->code, 1, 0 );
				} else { 
					//daca status e FALSE, inseamna ca nu este in baza de date retrimitem inca odata un sms free
					$this->sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . $this->mobilpayShortNumber . " " . $this->uniqueCode . " da", 0, 0 );
				}
				break;
			default :
				// in cazul in care sms-ul primit este gol
				$this->sendResponse( "Mesajul nu este corect, va rugam trimiteti la " . $this->mobilpayShortNumber . " un SMS cu codul " . $this->uniqueCode, 0, 0 );
				break;
		}
	}

}


	// if ($_GET ['message'] == $smsOffline->uniqueCode) { // initiem plata, la primul sms
	// 	// verificam daca in baza de date exista numarul de telefon $_GET[sender] pentru care se astepata raspunsul MOBILPAY_UNIQUE_CODE+da
	// 	//daca nu, il inseram in baza de date si il rugam sa trimtia codul + da
	// 	SmsOffline::sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . " " . $smsOffline->uniqueCode . " da", 0, 0 );
	
	// } elseif ($_GET ['message'] == $smsOffline->uniqueCode . " da") {
	// 	// verificam in baza de date daca a trimis initial cuvantul  MOBILPAY_UNIQUE_CODE
	// 	if ($in_db) { // daca da
	// 		// daca are bani in cont, si plata se va face, va primi raspunsul trimis acum:
	// 		SmsOffline::sendResponse( "Pentru a avea acces la ce ati comandat introduceti codul UN_COD_UNIC", 1, 0 ); // UN_COD_UNIC este dat de noi, si folosit tot de noi pentru identificare/procesare comanda
	// 		// sau putem verifica in contul din mobilpay dupa nr. de telefon, si raspunsul de mai sus sa fie unul de multumiri
	// 	} else { //daca nu este in baza de date retrimitem inca odata un sms free
	// 		SmsOffline::sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . " " . $smsOffline->uniqueCode . " da", 0, 0 );
	// 	}
	// } else { // in cazul in care sms-ul primit este gol
	// 	SmsOffline::sendResponse( "Mesajul nu este corect, va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . " un SMS cu codul " . $smsOffline->uniqueCode, 0, 0 );
	// }