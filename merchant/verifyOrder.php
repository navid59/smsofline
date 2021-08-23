<?php
// Simulate confirm code
$status = rand(0,1);
if($status) {
    $result['status']   = $status;
    $result['code']     = generateRandomString(); 
    $result['message']  = 'Your order is confirmed';
}else {
    $result['status']   = $status;
    $result['code']     = null; 
    $result['message']  = 'Your order is NOT confirmed!!!';
}


echo json_encode($result, true);


function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}