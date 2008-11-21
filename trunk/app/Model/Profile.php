<?php
class Model_Profile extends Model_Base {
	
	protected $attribute_definitions = array(
		'name' => '/a-z0-9_- /i, min=4, max=50',
		'active' => 'boolean'
	);
	public function __construct() {
		parent::__construct(__CLASS__);
	}
}