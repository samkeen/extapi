<?php
class Controller_CustomRoutes extends Controller_Base {
	
	protected function default_action() {
		$this->payload->message = "Hello, this is the default controller";
		$this->payload->controller = print_r($this,1);
	}
	
	protected function home_action() {
		$this->payload->message = "Adding SMS to Trimet TransitTracker";
	}
	protected function about_action() {
		$this->payload->message = "How SMS was added to Trimet TransitTracker";
	}
	
	
}

?>
