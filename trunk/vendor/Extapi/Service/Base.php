<?php

abstract class Extapi_Service_Base {
	
	protected $channel;
	protected $config;
	protected $http_util;
	
	public abstract function interpret_request_statement();
	public abstract function act_on_request_statement();
	public abstract function has_feedback();
	public abstract function gather_feedback();
	
	public function __construct(Extapi_Channel_Communicator $channel) {
		$this->channel = $channel;
	}
	/**
	 * @todo pull this out to a util class (Channel_Communicator has the same method); 
	 */
	protected function load_config($file, $section=null) {
		$config_folder =  dirname(dirname(__FILE__)).'/config/';
		$config = parse_ini_file($config_folder.$file.'.ini',true);
		$this->config = array_get_else($config,$section);
	}
	protected function config_get($key) {
		return array_get_else($this->config,$key);
	}
}

?>