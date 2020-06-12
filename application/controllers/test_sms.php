
<?php
function test_sms() {
		$sid = getenv("Twilio_SID");
		$token = getenv("Twilio_Token");
		$twilio_no = getenv("Twilio_PhoneNo");

		$client = new Twilio\Rest\Client($sid, $token);
		try {
			$client->messages->create(
				"+14158676326", array(
					'from' => $twilio_no,
					'body' => "Hello there - I am working!!"
				)
			);
		} catch (Services_Twilio_RestException $e) {
			log_message('Error', "Could send sms with this error: $e->getMessage()");
		}
		log_message('Info', "just sent a message");
	}
test_sms();
