<?php 
    require_once 'prerequist.php';
    require_once "../vendor/autoload.php";
    use Twilio\TwiML\VoiceResponse;
    $response = new VoiceResponse();
    $db = database();
    $data = $_REQUEST;
    if(!empty($data)) {
        $extra_data = json_encode($data);
        $recording_url =  !empty($data['RecordingUrl']) ? $data['RecordingUrl'] : '';
        $sql = " UPDATE call_logs SET recording_url='".$recording_url."', extra_data = '".$extra_data."' WHERE call_sid= '".$data['CallSid']."'";
        $db->query($sql);
        $db -> close();
        $response->hangup();
        header('Content-Type: text/xml');
        echo $response;
        exit;       
    }else {
        $response->hangup();
        header('Content-Type: text/xml');
        echo $response;
        exit;        

    }    

?>