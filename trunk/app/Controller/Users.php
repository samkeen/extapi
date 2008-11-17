<?php
/**
 * 
 * 
 *
 */
class Controller_Users extends Controller_Base {
	
	protected function init() {
		$this->default_response_type = CONSTS::$RESPONSE_HTML;
	}
	/**
	 * show list of users
	 */
	protected function default_action() {
		$user = new Model_User();
		//$user->set('active',true);
		$this->payload->users = $user->find();
	}
	
	protected function add_action() {
		if ($this->recieved_form_data) {
			$user = new Model_User();
			if ($user->save($this->form_data)) {
				$this->feedback = "The User has been created";
				$this->redirect('/users');
			} else {
				$this->feedback = "There was a problem creating the user";
			}
		}
		// just display form	
	}
	protected function edit_action() {
		if ($this->recieved_form_data) {
			$user = new Model_User();
			if ($user->save($this->form_data)) {
				$this->feedback = "The User has been updated";
				$this->redirect('/users');
			} else {
				$this->feedback = "There was a problem creating your account";
			}
		}
		$user = new Model_User();
		$user->set('user_id',$this->next_request_segment_value());
		$this->payload->user = $user->findOne();
	}
	protected function delete_action() {
		$user = new Model_User();
		$user->set('user_id',$this->next_request_segment_value());
		if ($user->delete()) {
			$this->feedback = "The User has been deleted";
			$this->redirect('/users');
		} else {
			$this->feedback = "There was a problem deleting this user";
		}
		$this->payload->users = $user->find();
	}
	protected function over20_action() {
		$user = new Model_User();
		$user->set('active',true);
		$user->set('age','>','20');
		$this->set_template('users/default');
		$this->payload->users = $user->find();
	}
}