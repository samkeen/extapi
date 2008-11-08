<?php
class Controller_Users extends Controller_Base {
	
	protected function init() {
		$this->default_response_type = CONSTS::$RESPONSE_HTML;
	}
	/**
	 * show list of users
	 */
	protected function default_action() {
		$user = new Model_User();
		$user->set('active',true);
		$this->payload->users = $user->find();
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