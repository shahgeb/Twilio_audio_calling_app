<?php 
    require_once 'prerequist.php';
    require_once "../vendor/autoload.php";
    use Twilio\TwiML\VoiceResponse;
    $response = new VoiceResponse();
    $data = $_REQUEST;
    $db = database();
    $sql = "INSERT INTO call_logs (from_number, to_number,account_id, call_sid, recording_url, extra_data, created, updated)
    VALUES ('".$data['Caller']."', '".$data['To']."', '".$data['AccountSid']."', '".$data['CallSid']."', '".$data['RecordingUrl']."', '".json_encode($data)."' , '".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."')";
    if ($db->query($sql) === TRUE) {
        $response->say('For quality assurance, This call my be recorded.',['voice' => 'woman', 'language'=>'en']);
       if($data['From'] === 'orbisx' || $data['From'] == 'client:orbisx' || $data['From'] == 16137019690 || $data['From'] == '+16137019690') {
            $response->dial($data['To'],
                array(
                    'callerId' =>$twilio_number, 
                    'record' => 'record-from-answer-dual', 
                    'action'=>'save_record.php',
                    'timeout'=>20, 
                    'method' => 'POST'
                )
            );
        
        }else{
            $dial = $response->dial('',
                array(
                    'record' => 'record-from-answer-dual', 
                    'action'=>'save_record.php',
                    'timeout'=>20, 
                    'method' => 'POST'
                )
            );
            $dial->client('client:orbisx');
            $dial->client('orbisx');
        }
        
        $db -> close();
        header('Content-Type: text/xml');
		echo $response;
        exit;    
        
    } else {
        $response->say('There is problem in calling, Please contact with your administrator.',['voice' => 'woman', 'language'=>'en']);
        $response->hangup();
        header('Content-Type: text/xml');
        echo $response;
        exit;        

    }
    
?>