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

// function setArrLog($arrLog) {
//     if(is_null($arrLog))
//         return false;

//     $logPoint = date(" - H:i:s - ")." \n";
//     ob_start();                     // start buffer capture
    
//     foreach($arrLog as $key => $val) {
//         $logPoint .= $key ." : ". $val . "\n";
//     }
//     $logPoint .= "--------------------\n";
//     echo $logPoint;
//     $contents = ob_get_contents();  // put the buffer into a variable
//     ob_end_clean();
//        file_put_contents('merchant.log.txt', $contents , FILE_APPEND | LOCK_EX);

//        return true;
// }


// Takes raw data from the request
$json = file_get_contents('php://input');
$msg['data']   = setStrLog($json) ? 'Notification is loged.' : 'Could not log the Notification'; 
echo json_encode($msg, true);