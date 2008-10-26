<?php
require 'Communication.php';
class Channel_Zeep implements Channel_Communication  {
	
	/**
	 * @see Channel_Communicator::act_on_request_statement()
	 *
	 */
	public function act_on_request_statement() {
	}
	
	/**
	 * @see Channel_Communicator::authenticate_request()
	 *
	 */
	public function authenticate_request() {
	}
	
	/**
	 * @see Channel_Communicator::collect_request_params()
	 *
	 */
	public function collect_request_params() {
		>>>>> need to refactor / create __construct for this class so it has access to this info.
		 // verify that we have the expected number of required params
	 	foreach ($this->mapped_channel_communication_fields['required_fields'] as $required_field) {
	 		if (empty($this->mapped_channel_communication_fields[$required_field])) {
	 			$this->logger->warn(__METHOD__.'Value for required field['.$required_field.'] found found to be empty');
	 		}
	 	}
	}
	
	/**
	 * @see Channel_Communicator::gather_feedback()
	 *
	 */
	public function gather_feedback() {
	}
	
	/**
	 * @see Channel_Communicator::interpret_request_statement()
	 *
	 */
	public function interpret_request_statement() {
	}

}

?>