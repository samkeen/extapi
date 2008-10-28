<?php
class Controller_Example extends Controller_Base {
	
	protected function init() {
		// set a resp type to fall back to if none is explicitly requested.
		// Global response type is set in CONSTS
//		$this->default_response_type = CONSTS::$RESPONSE_TEXT;
		// override the resp type (regardless of what is requested)
//		$this->requested_response_type = CONSTS::$RESPONSE_HTML
		// set the layout for all the actions
//		$this->set_layout();
		// negate the use of a layout for this controller
//		$this->use_layout = false;
		// set the template for all the actions
//		$this->set_template();
		// negate the use of templates for this controller
	}
	
	protected function default_action() {
		// set the layout for this action
//		$this->set_layout();
		// negate the use of a layout for this action
//		$this->use_layout = false;
		// set the template for this action
//		$this->set_template();
		// negate the use of templates for this action
//		$this->use_template = false;
		$this->payload->message = "This is the action you get (".__FUNCTION__.") if no action is called";
		$this->payload->response_type = $this->get_response_type();
	}
	protected function viewless_action() {
		// set the layout for this action
//		$this->set_layout();
		// negate the use of a layout for this action
		$this->use_layout = false;
		// set the template for this action
//		$this->set_template();
		// negate the use of templates for this action
		$this->use_template = false;
		$this->logger->debug("Made it to the viewless action of the Example contoller");
	}
	/**
	 * /example/no_layout OR /example/no-layout
	 */
	protected function no_layout_action() {
		// set the layout for this action
//		$this->set_layout();
		// negate the use of a layout for this action
		$this->use_layout = false;
		// set the template for this action
//		$this->set_template();
		// negate the use of templates for this action
//		$this->use_template = false;
		$this->payload->message = "<p>This action (".__FUNCTION__.") does not use a layout<p><p>It can be called by .../example/no_layout OR .../example/no-layout</p>";
	}
	
}