<?php
class Controller_Pdxt extends Controller_Base {
	
	protected function init() {
		
	}
	protected function default_action() {
		$this->payload->message = "Hello, this is the PDXt controller";
		$this->payload->controller = print_r($this,1);
	}
	protected function sms_action() {
		$this->payload->message = "passed through the SMS action";
	}
	
	
//	$this->sms_service = Weave::instance()->sms['service_name'];
	
	/**
	 * handler for inbound SMS requests
	 *
	 * @param array $request
	 */
//	public function receiver(array $request = null) {
//		header('Content-type: text/plain',true);
//		$this->sms_logger->debug(__METHOD__.' $request:> '.print_r($request, 1));	
//		$sms_helper = new Helper_SMS($this->sms_logger);
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
//	}
}