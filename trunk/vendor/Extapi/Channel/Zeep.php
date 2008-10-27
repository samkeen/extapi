<?php
require 'Communicator.php';
class Extapi_Channel_Zeep extends Extapi_Channel_Communicator {
	
	public function __construct($requesting_channel_name, array $request, Logger $logger) {
		parent::__construct($requesting_channel_name, $request, $logger);
	}

	
	/**
	 * @see Channel_Communicator::authenticate_request()
	 *
	 */
	public function authenticate_request() {
		$authenticated = false;
		// authenticate the request is from Zeep
		$authenticated = 
			$this->config['channel_short_code'] == $this->mapped_channel_communication_fields['channel_short_code'];
		// authenticate the user making the request
		$authenticated = 
			$this->mapped_channel_communication_fields['sms_user_id'] == 'samkeen'
			&&
			$this->mapped_channel_communication_fields['sms_user_number'] == '15034733242';
		return $authenticated;
	}
	
	/**
	 * @see Channel_Communicator::collect_request_params()
	 *
	 */
	public function collect_request_params() {
		$collected_all_required_params = null;
		// verify that we have the expected number of required params
	 	foreach ($this->required_channel_communication_fields as $required_field) {
	 		if (empty($this->mapped_channel_communication_fields[$required_field])) {
	 			$this->logger->warn(__METHOD__.'Value for required field['.$required_field.'] found found to be empty');
	 			$collected_all_required_params = false;
	 		}
	 	}
	 	return $collected_all_required_params===null?true:false;
	}

	
	/**
	 * build the security http header for a given sms service
	 */
	public static function generate_authorization_headers($service_name,$api_key, $signing_key, $message_parameters_string ) {
		$authorization_header = null;
		switch($service_name) {
			case 'zeep':
				$httpDate = gmdate("D, d M Y H:i:s T");
				$canonical_string = $api_key . $httpDate . $message_parameters_string;				
				$b64Mac = base64_encode(hash_hmac('sha1', $canonical_string, $signing_key,true));
				$authorization_header = "Zeep " . $api_key . ":" . $b64Mac;
			break;
		}
		return array('Authorization' => $authorization_header, 'Date' => $httpDate);
	}


}

?>