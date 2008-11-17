<?php
class Model_User extends Model_Base {
	
	protected $attributes = array(
		'username' => null,
		'password' => null,
		'xmpp_jid' => null,
		'sms_number' => null,
		'age' => null,
		'active' => null
	);
	public function __construct() {
		parent::__construct(__CLASS__);
	}	
}