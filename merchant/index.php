<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function setStrLog($log) {
    if(is_null($log))
        return false;
        
    ob_start();                     // start buffer capture
    echo ($log)." \n";                    // print the values
    $contents = ob_get_contents();  // put the buffer into a variable
    ob_end_clean();
       file_put_contents('merchant.log.txt', $contents , FILE_APPEND | LOCK_EX);

    return true;
}

// Takes raw data from the request
$json = file_get_contents('php://input');

// Log incoming data 
setStrLog($json);

// Simulate result
$result['status']   = true; 
$result['code']     = 1; 
$result['message']  = 'Merchant Notified';

echo json_encode($result, true);