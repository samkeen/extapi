<?php

/**
 *
 */
abstract class Channel_Communicator {
	
	protected $config;
	protected $request;
	protected $logger;
	
	protected abstract function collect_request_params();
	protected abstract function authenticate_request();
	protected abstract function interpret_request_statement();
	protected abstract function act_on_request_statement();
	protected abstract function gather_feedback();
	
	protected function load_config($file, $section=null) {
		$config_folder =  dirname(dirname(__FILE__)).'/config/';
		$config = parse_ini_file($config_folder.$file.'.ini',true);
		foreach ($config as $key => $value) {
			if (substr($key,0,4)=='sms.') {
				$config['sms']['channels'][substr($key,4)] = $value;
				unset($config[$key]);
			}
		}
		if ($section!==null) {
			$this->config = isset($config[$section]) ? $config[$section] : null;
		} else {
			$this->config = $config;
		}
	}
}

?>