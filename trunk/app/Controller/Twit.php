<?php
class Controller_Twit extends Controller_Base {
	
	protected function init() {
		
	}
	protected function default_action() {
		$this->payload->message = "Hello, this is the Twitter controller";
		$this->payload->controller = print_r($this,1);
	}
}