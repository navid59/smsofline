<?php
/*
 * pentru Cosmote 
 * la fel ca la vodafone, si la cosmote tranzactia se desfasoara in doua etape, cu specificatia ca SMS-ul 2 se trimite la MOBILPAY_SENDER_NUMBER + 0 
 */
class Cosmote extends SmsOffline {
	public $reciveMsg; 			 // the message what Cellphone owner sent
	// public $mobilpayShortNumber; // the mobilpay short number
	public function __construct() {
		// parent::__construct();
	}

	/**
	 * initiem plata
	 */
	public function makeOperation() {
		switch($this->reciveMsg) {
			case $this->uniqueCode:
				// verificam daca in baza de date exista numarul de telefon $_GET[sender] si care astepta raspunsul
				//daca nu, il inseram in baza de date si il rugam sa trimtia codul + da
				$this->sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . $this->mobilpayShortNumber . "0 " . $this->uniqueCode . " da", 0, 0 );
			break;
			case $this->uniqueCode.strtolower(self::MOBILPAY_CONFIRM_KEY):
				// verificam in baza de date daca a trimis initial cuvantul  de test
				// daca da
				// $in_db = true / false
				// NAVID , Manage the "in_db" 
				if ($in_db) {
					// daca are bani in cont, si plata se va face, va primi raspunsul trimis acum:
					$this->sendResponse( "Pentru a avea acces la ce ati comandat introduceti codul UN_COD_UNIC", 1, 0 );
				} else {
					$this->sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . $this->mobilpayShortNumber . "0 " . $this->uniqueCode . " da", 0, 0 );
				}
			break;
			default:
				$this->sendResponse( "Mesajul nu este corect, va rugam trimiteti la " . $this->mobilpayShortNumber . " un SMS cu codul " . $this->uniqueCode, 0, 0 );
			break;
		}
	}
}



	// if ($_GET ['message'] == $smsOffline->uniqueCode) { // initiem plata
		// // verificam daca in baza de date exista numarul de telefon $_GET[sender] si care astepta raspunsul
		// //daca nu, il inseram in baza de date si il rugam sa trimtia codul + da
		// SmsOffline::sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . "0 " . $smsOffline->uniqueCode . " da", 0, 0 );
	
	// } elseif ($_GET ['message'] == $smsOffline->uniqueCode . " da") {
	// 	// verificam in baza de date daca a trimis initial cuvantul  de test
	// 	// daca da
		

	// 	if ($in_db) {
	// 		// daca are bani in cont, si plata se va face, va primi raspunsul trimis acum:
	// 		SmsOffline::sendResponse( "Pentru a avea acces la ce ati comandat introduceti codul UN_COD_UNIC", 1, 0 );
	// 	} else {
	// 		SmsOffline::sendResponse( "Pentru a finaliza plata va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . "0 " . $smsOffline->uniqueCode . " da", 0, 0 );
	// 	}
	// } else {
	// 	SmsOffline::sendResponse( "Mesajul nu este corect, va rugam trimiteti la " . MOBILPAY_SENDER_NUMBER . " un SMS cu codul " . $smsOffline->uniqueCode, 0, 0 );
	// }