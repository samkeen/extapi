<?php
/**
 * 
 * 
 *
 */
class Controller_Channels extends Controller_Base {
	
	protected function init() {
		$this->default_response_type = CONSTS::$RESPONSE_HTML;
	}
	/**
	 * show list of channels
	 */
	protected function default_action() {
		$channel = new Model_Channel();
		//$channel->set('active',true);
		$this->payload->channels = $channel->find();
	}
	
	protected function add_action() {
		if ($this->recieved_form_data) {
			$channel = new Model_Channel();
			if ($channel->save($this->form_data)) {
				$this->feedback = "The Channel has been created";
				$this->redirect('/channels');
			} else {
				$this->feedback = "There was a problem creating the channel";
			}
		}
		// just display form	
	}
	protected function edit_action() {
		if ($this->recieved_form_data) {
			$channel = new Model_Channel();
			if ($channel->save($this->form_data)) {
				$this->feedback = "The Channel has been updated";
				$this->redirect('/channels');
			} else {
				$this->feedback = "There was a problem creating the channel";
			}
		}
		$channel = new Model_Channel();
		$channel->set('channel_id',$this->next_request_segment_value());
		$this->payload->channel = $channel->findOne();
	}
	protected function delete_action() {
		$channel = new Model_Channel();
		$channel->set('channel_id',$this->next_request_segment_value());
		if ($channel->delete()) {
			$this->feedback = "The channel has been deleted";
			$this->redirect('/channels');
		} else {
			$this->feedback = "There was a problem deleting this channel";
		}
		$this->payload->channels = $channel->find();
	}
	protected function over20_action() {
		$channel = new Model_Channel();
		// could also be: $channel->set('active','=',true);
		$channel->set('active',true);
		$channel->set('age','>','20');
		$this->set_template('channels/default');
		$this->payload->channels = $channel->find();
	}
}