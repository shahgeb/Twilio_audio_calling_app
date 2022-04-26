<?php 
    //// mine.
    $twilio_account = '';
    $twilio_auth_token = '';
    $twilio_number = '';
    $twilio_app_sid = '';
    
    function debug($data){
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    function database(){
        $servername = "";
        $username = "";
        $password = "";
        $db = "";
        $conn = new mysqli($servername, $username, $password, $db);
        if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
?>