<?php
class Extapi_Channel_Communicator {
	
	private $config;
	// store signing keys seperate from other config settings for security
	private $channel_signing_keys;
	private $request;
	private $logger;
	private $requesting_channel_name;
	public $communicator;
	
	protected $mapped_channel_communication_fields = array();
	
	public function __construct($requesting_channel_name, array $request, Logger $logger) {
		$this->requesting_channel_name = $requesting_channel_name;
		$this->request = $request;
		$this->logger = $logger;
		$this->load_config('channels', 'sms');
		$this->load_specific_communicator();
	}
	public function config_for_provider($provider_name) {
		return array_get_else($this->config['channels'],$provider_name);
	}
	public function communicator() {
		$this->specific_communicator;
	}
	private function load_config($file, $section=null) {
		$config_folder =  dirname(dirname(__FILE__)).'/config/';
		$config = parse_ini_file($config_folder.$file.'.ini',true);
		foreach ($config as $key => $value) {
			if (substr($key,0,4)=='sms/') {
				$this->channel_signing_keys[substr($key,4)] = isset($value['signature_key'])?$value['signature_key']:null;
				unset($value['signature_key']);
				$config['sms']['channels'][substr($key,4)] = $value;
				unset($config[$key]);
			}
			else if (substr($key,0,19)=='sms_channel_fields/') {
				$config['sms']['channels'][substr($key,19)]['sms_channel_fields_map'] = $value;
				unset($config[$key]);
			}
			
		}
		if ($section!==null) {
			$this->config = isset($config[$section]) ? $config[$section] : null;
		} else {
			$this->config = $config;
		}
		foreach ($this->config['channels'][$this->config['default_channel']]['sms_channel_fields_map'] as $key => $value) {
			if($key=='required_fields') {
				$this->mapped_channel_communication_fields[$key] = array_map('trim',explode(',',$value));
			} else {
				$this->mapped_channel_communication_fields[$key] = array_get_else($this->request, $value);
			}
		}
	}
	private function load_specific_communicator() {
		$specific_communicator = 'Channel_'.ucfirst($this->requesting_channel_name);
		require $specific_communicator.'.php';
		$this->communicator = new $specific_communicator;
	}
}

?>