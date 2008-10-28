<?php

class Util_VendorFactory {
	
	private $vendor_name = null;
	
	public function __construct($vendor_name) {
		$this->vendor_name = ucfirst($vendor_name);
	}
	
	public function get_instance($channel_name) {
		$class_dir = dirname(__FILE__).'/';
		$channel_parts = explode('/',$channel_name);
		$channel_path = array_map('ucfirst',$channel_parts);
		$channel_path = implode('/',$channel_path);
		$class_name = $this->vendor_name.'_'.implode('_',array_map('ucfirst',$channel_parts));
		global $logger;
		if (ENV::load_vendor_file($this->vendor_name.'/'.$channel_path)) {
			$request_params = Util_Router::request_params();
			return new $class_name(array_pop($channel_parts),$request_params,$logger);
		} else {
			$logger->error('The file ['.$class_dir.$class_name.'.php] for the requested channel ['.$channel_name.'] could not be found');
			return null;
		}
	}
	
}

?>