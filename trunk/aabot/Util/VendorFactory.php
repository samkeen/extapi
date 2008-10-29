<?php

class Util_VendorFactory {

	public static function get_instance($vendor_resource_path) {
		$class_dir = dirname(__FILE__).'/';
		$vendor_resource_parts = explode('/',$vendor_resource_path);
		$path_to_resource_file = array_map('ucfirst',$vendor_resource_parts);
		$path_to_resource_file = implode('/',$path_to_resource_file);
		$class_name = implode('_',array_map('ucfirst',$vendor_resource_parts));
		global $logger;
		if (ENV::load_vendor_file($path_to_resource_file)) {
			$request_params = Util_Router::request_params();
			return new $class_name(array_pop($vendor_resource_parts),$request_params,$logger);
		} else {
			$logger->error('The file ['.$class_dir.$class_name.'.php] for the requested channel ['.$vendor_resource_path.'] could not be found');
			return null;
		}
	}
	
}

?>