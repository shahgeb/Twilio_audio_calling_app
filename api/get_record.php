<?php 
    require_once 'prerequist.php';
    require_once "../vendor/autoload.php";
    use Twilio\Rest\Client;
    $twilio = new Client($twilio_account, $twilio_auth_token);
    $call_sid = !empty($_GET['call_sid'])? $_GET['call_sid'] : '';
    if(!empty($call_sid)) {
        $call = $twilio->calls($call_sid)
                   ->fetch();
        echo (!empty($call->status) && !in_array($call->status, ['ringing'])) ? 1 : 0;
    }else{
        echo 0;
    }
    
    exit;
?>