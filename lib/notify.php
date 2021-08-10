<?php
class Notify {
    public $notifyURL = null;   // URL to notify merchant
    public function __construct() {
        //
        // $xxx = new \StdClass();
    }

    // Send notification json
    public function sendNotify($jsonStr) {
        if(!isset($this->notifyURL) || is_null($this->notifyURL)) {
            throw new \Exception('INVALID_NOTIFY_URL');
            exit;
        }

        //////////////////
        // $url = $this->notifyURL;
        // // $url = 'url_to_post';
        // $data = array("first_name" => "First name","last_name" => "last name","email"=>"email@gmail.com","addresses" => array ("address1" => "some address" ,"city" => "city","country" => "CA", "first_name" =>  "Mother","last_name" =>  "Lastnameson","phone" => "555-1212", "province" => "ON", "zip" => "123 ABC" ) );

        // $postdata = json_encode($data);

        // $ch = curl_init($url); 
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        // $result = curl_exec($ch);
        // curl_close($ch);
        // print_r ($result);
        //////////////////

        $url = $this->notifyURL;
        $ch = curl_init($url);
        
        $payload = json_encode($jsonStr); // json DATA
        // die($payload);
  
        // To do a regular HTTP POST like 'application/x-www-form-urlencoded'
        curl_setopt($ch, CURLOPT_POST, true);
        
        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  
        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  
        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
        // Execute the POST request
        $result = curl_exec($ch);
        
        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                case 200:  # OK
                    $arr = array(
                        'status'  => 1,
                        'code'    => $http_code,
                        'message' => "You send your request, successfully",
                        'data'    => json_decode($result)
                    );
                break;
                case 404:  # Not Found 
                    $arr = array(
                        'status'  => 0,
                        'code'    => $http_code,
                        'message' => "You send request to wrong URL",
                        'data'    => json_decode($result)
                    );
                break;
                case 400:  # Bad Request
                    $arr = array(
                        'status'  => 0,
                        'code'    => $http_code,
                        'message' => "You send Bad Request",
                        'data'    => json_decode($result)
                    );
                break;
                case 405:  # Method Not Allowed
                    $arr = array(
                        'status'  => 0,
                        'code'    => $http_code,
                        'message' => "Your method of sending data are not Allowed",
                        'data'    => json_decode($result)
                    );
                break;
                default:
                    $arr = array(
                        'status'  => 0,
                        'code'    => $http_code,
                        'message' => "Opps! Something is wrong, verify how you send data & try again!!!",
                        'data'    => json_decode($result)
                    );
                break;
            }
        } else {
            $arr = array(
                'status'  => 0,
                'code'    => 0,
                'message' => "Opps! There is some problem, you are not able to send data!!!"
            );
        }
        
        // Close cURL resource
        curl_close($ch);
        
        $finalResult = json_encode($arr, JSON_FORCE_OBJECT);
        return $finalResult;
    }
}