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
	protected function index() {
		$user = new Model_User();
		$this->payload->users = $user->find();
	}

	protected function view() {
		$user = new Model_User();
		$user->set('user_id',$this->arguments__first);
		$this->payload->user = new SimpleDTO($user->findOne());
	}
	
	protected function add() {
		if ($this->recieved_form_data) {
			$user = new Model_User();
			if ($user->save($this->form_data)) {
				$this->feedback = "The User has been created";
				$this->redirect('/users');
			} else {
				$this->feedback = "There was a problem creating the user";
			}
		}	
	}
	protected function edit() {
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
	protected function delete() {
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
	protected function over20() {
		$user = new Model_User();
		// could also be: $user->set('active','=',true);
		$user->set('active',true);
		$user->set('age','>','20');
		$this->set_template('users/default');
		$this->payload->users = $user->find();
	}
}