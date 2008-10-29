<?php
abstract class Extapi_Channel_Communicator {
	
	protected $config;
	// store signing keys seperate from other config settings for security
	protected $channel_signing_key;
	protected $request;
	protected $logger;
	protected $requesting_channel_name;
	// all possible communication fields for this type of channel
	protected $channel_communication_fields = array();
	// communication fields for this specific channel 
	// [public]: need to access from Service classes
	public $mapped_channel_communication_fields = array();
	// fields that are required to have values in the request
	protected $required_channel_communication_fields = array();
	
	public abstract function collect_request_params();
	public abstract function authenticate_request();
		
	public function __construct($requesting_channel_name, array $request, Logger $logger) {
		$this->requesting_channel_name = $requesting_channel_name;
		$this->request = $request;
		$this->logger = $logger;
		$this->load_config('channels', 'sms', $requesting_channel_name);
	}
	public function config() {
		return $this->config;
	}
	public function communicator() {
		$this->specific_communicator;
	}
	private function load_config($file, $section=null, $channel_name=null) {
		$config_folder =  dirname(dirname(__FILE__)).'/config/';
		$config = parse_ini_file($config_folder.$file.'.ini',true);
		$channel_key = $section.'/'.$channel_name;
		foreach ($config as $key => $value) {
			if (substr($key,0,strlen($channel_key))==$channel_key) {
				$this->channel_signing_key = isset($value['signature_key'])?$value['signature_key']:null;
				unset($value['signature_key']);
				$this->config = $value;
			}
		}
		foreach (array_get_else($config,'sms_channel_fields/'.$channel_name,array()) as $key => $value) {
			if($key=='required_fields') {
				$this->required_channel_communication_fields = array_map('trim',explode(',',$value));
			} else {
				$this->mapped_channel_communication_fields[$key] = array_get_else($this->request, $value);
			}
		}
		$this->channel_communication_fields = array_get_else($config,$section.'_channel_fields',array());
	}
	protected function config_get($key) {
		return array_get_else($this->config,$key);
	}
}

?>