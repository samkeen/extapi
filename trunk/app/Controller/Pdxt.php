<?php
class Controller_Pdxt extends Controller_Base {
	
	protected function init() {
		
	}
	protected function default_action() {
		$this->payload->message = "Hello, this is the PDXt controller";
		$this->payload->controller = print_r($this,1);
	}
	protected function sms_action() {
		$x = $this->next_request_segment_value();
		switch ($x) {
			case 'receiver':
				$this->receiver();
			break;
			
			default:
				;
			break;
		}
		
		$this->payload->message = "passed through the SMS action";
		$this->payload->this = print_r($this,1);
	}
	protected function register_action() {
		ENV::load_vendor_file('Extapi/Channel/Sms');
		$sms_channel = new Extapi_Channel_Sms(Util_Router::request_params(), $this->logger);
		$this->payload->zeep_channel = $sms_channel->config_for_provider('zeep');
		$this->payload->user_id = 'samkeen';
	}
/*
 * Subscription ping
 * $_REQUESTArray
(
    [;c;] => pdxt/sms/receiver
    [sms_prefix] => pdxtt
    [short_code] => 88147
    [uid] => samkeen
    [min] => +15034733242
    [event] => SUBSCRIPTION_UPDATE
)
 * 
 * ?sms_prefix=pdxtt&short_code=88147&uid=samkeen&min=+15034733242&event=SUBSCRIPTION_UPDATE
 */

/*
 * user message
 * $_REQUESTArray
(
    [;c;] => pdxt/sms/receiver
    [sms_prefix] => pdxtt
    [short_code] => 88147
    [uid] => samkeen
    [body] => hello
    [min] => +15034733242
    [event] => MO
)
 * 
 * ?sms_prefix=pdxtt&short_code=88147&uid=samkeen&body=hello&min=+15034733242&event=MO
 */
	private function receiver() {
		
		ENV::load_vendor_file('Extapi/Channel/Communicator');
		header('Content-type: text/plain',true);
//print_r($this);
		$this->logger->debug('$_REQUEST'.print_r($_REQUEST,1));
		$sms_channel = new Extapi_Channel_Communicator($this->requested_response_type, Util_Router::request_params(), $this->logger);
print_r($sms_channel);
		if ($sms_channel->communicator->collect_request_params() && $sms_channel->authenticate_request()) {
			$sms_channel->interpret_request_statement();
			if ($sms_channel->has_feedback()) {
				$this->payload->feedback = $sms_channel->gather_feedback();
			} else {
				$this->viewless();
			}
			
		} else {
			$this->logger->notice(__METHOD__.' Required components were not found and/or authentcation failed for this request');
			// don't respond to these requests.
			$this->viewless();
		}
		
		// authenticate request
		
		// interprate request statement
		
		// act on request statement
		
		// gather feedback
		
		
		
		
//		if ($sms_helper->gather_conversation_components_for($this->sms_service,array_map('urldecode',$_REQUEST)) 
//			&& $user = $sms_helper->authenticate_request(Weave::instance()->sms['service_name'])) {
//
//		$sms_helper->parse_statement($user);
//			if($sms_helper->has_feedback()) {
//				echo $sms_helper->gather_feedback();
//			}
//		} else {
//			$this->sms_logger->info(__METHOD__.' Required components were not found and/or authentcation failed for this request');
//		}
//		die;
	}


}