<?php
class Controller_Register extends Controller_Base {
	
	protected function init() {
		$this->default_response_type = CONSTS::$RESPONSE_HTML;
	}
	protected function default_action() {

	}
	protected function add_action() {
		if ($this->received_data) {
			$user = new Model_User($this->received_data);
			if ($user->save()) {
				$this->feedback = "Your Account has been created";
				$this->redirect('/register');
			} else {
				$this->feedback = "There was a problem creating your account";
			}
		}
		// just display form	
	}
	protected function edit_action() {

	}
}