<?php
// Required if your environment does not handle autoloading
// echo __DIR__.'/twilio-php-master/Twilio/autoload.php';
require __DIR__.'/twilio-php-master/Twilio/autoload.php';
// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

// Your Account SID and Auth Token from twilio.com/console
$sid = 'ACf487c7fa5fd56fc7fe9a7ff11a1b68de';
$token = 'e4d2ab9e4d7c79e0a17aa6404ea83451';
$client = new Client($sid, $token);

try{
// Use the client to do fun stuff like send text messages!
$client->messages->create(
    // the number you'd like to send the message to
    '+821055834032',
    array(
        // A Twilio phone number you purchased at twilio.com/console
        'from' => '+16099970748',
        // the body of the text message you'd like to send
        'body' => "짱구야 성공했냐?짱구야 성공했냐?짱구야 성공했냐?짱구야 성공했냐?짱구야 성공했냐?짱구야 성공했냐?짱구야 성공했냐?짱구야 성공했냐?짱구야 성공했냐?짱구야 성공했냐?짱구야 성공했냐?"
    )
);
} catch(Exception $e){
	echo $e->getMessage();
}
?>