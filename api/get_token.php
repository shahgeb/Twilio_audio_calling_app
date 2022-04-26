<?php 
    include "../vendor/autoload.php";

    use Twilio\Jwt\ClientToken;
    include 'prerequist.php';
    // put your Twilio API credentials here
    $accountSid = $twilio_account;
    $authToken  = $twilio_auth_token;
    $appSid = $twilio_app_sid;
    
    $capability = new ClientToken($accountSid, $authToken);
    $capability->allowClientOutgoing($appSid);
    $capability->allowClientIncoming('orbisx');
    $token = $capability->generateToken(3600*12);
    $data['identity'] = 'orbisx';
    $data['token'] = $token;
    echo json_encode($data);
    exit;

?>