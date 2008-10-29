<?php
class Controller_Pdxt extends Controller_Base {
	
	protected function init() {
		$this->default_response_type = CONSTS::$RESPONSE_TEXT;
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
		$sms_channel = Util_VendorFactory::get_instance('extapi/channel/zeep');
//		ENV::load_vendor_file('Extapi/Channel/Factory');
//		$sms_channel = new Extapi_Channel_Zeep('zeep', Util_Router::request_params(), $this->logger);
		$this->payload->zeep_channel = $sms_channel->config();
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
		$this->use_layout = false;
		$requesting_channel = $this->next_request_segment_value();
		//ENV::load_vendor_file('Extapi/Channel/Zeep');
		header('Content-type: text/plain',true);
//print_r($this);die;
		ENV::$log->debug('$_REQUEST'.print_r($_REQUEST,1));
		$sms_channel = Util_VendorFactory::get_instance('extapi/channel/'.$requesting_channel);//new Extapi_Channel_Zeep($requesting_channel, Util_Router::request_params(), $this->logger);
//print_r($sms_channel);
		if ($sms_channel->collect_request_params() && $sms_channel->authenticate_request()) {
			ENV::load_vendor_file('Extapi/Service/Tmet');
			$tmet_service = new Extapi_Service_Tmet($sms_channel);
			$tmet_service->interpret_request_statement();
			$tmet_service->act_on_request_statement();			
//>>> do we need gather feedback? OR just display template?? <<<
			if ($tmet_service->has_feedback()) {
				$arrivals = $tmet_service->gather_feedback();
				$this->payload->arrivals = array_get_else($arrivals,'arrivals');
				$this->payload->transit_stop = array_get_else($arrivals,'transit_stop');
				$this->payload->query_time = array_get_else($arrivals,'query_time');
//				$rendered_view = $this->get_rendered_view();
//				print_r($rendered_view);die;
//				$sms_channel->send_channel_message(array(0=>array('user_name'=>'samkeen','message'=>$rendered_view)),new Util_Http());
			} else {
				$this->viewless();
			}
			
		} else {
			$this->logger->notice(__METHOD__.' Required components were not found and/or authentcation failed for this request');
			// don't respond to these requests.
			$this->viewless();
		}
	}


}