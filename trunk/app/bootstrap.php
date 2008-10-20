<?php
$PATH__WEB_ROOT = dirname(dirname(__FILE__));
$PATH__APP_ROOT = $PATH__WEB_ROOT.'/app';
$PATH__FRAMWORK_ROOT = $PATH__WEB_ROOT.'/aabot';

require $PATH__FRAMWORK_ROOT.'/utils.php';
// include custom routes file which should put $custom_routes in scope;
include 'config/custom_routes.php';

final class CONSTS {
	CONST CONTROLLER_DIR = 'Controller';
	CONST MODEL_DIR = 'Model';
	CONST VIEW_DIR = 'View';
	
	// default app settings
	public static $DEFAULT_REQUESTED_RESPONSE_TYPE = 'html';
	public static $DEFAULT_CONTROLLER = 'Default';
	public static $DEFAULT_TEMPLATE = 'default';
	public static $DEFAULT_LAYOUT = 'default';
	
	public static $DEFAULT_ACTION = 'default_action';
	public static $FILE_NOT_FOUND_ACTION = 'file_not_found_action';
	
	public static $APP_DIR = '/';
	public static $LAYOUT_DIR = '/View/layout';
	public static $TEMPLATE_DIR = '/View/templates';
	
	public static $LIB_LAYOUT_DIR = '/View/layout';
	public static $LIB_TEMPLATE_DIR = '/View/templates';
	
	public static $FILE_NOT_FOUND_TEMPLATE = '/default/file_not_found.php';
}

final class ENV {
	
	static final function PATH($path, $append=null) {
		global $PATH__APP_ROOT,$PATH__FRAMWORK_ROOT;
		$append = ($append!=null) ? '/'.ltrim($append,'/') : '';
		if(substr($path,0,4)=='LIB_') {
			return $PATH__FRAMWORK_ROOT.CONSTS::$$path.$append;
		} else {
			return $PATH__APP_ROOT.CONSTS::$$path.$append;
		}	
	}
	public static final function get_controller_classname($requested_controller_name) {
		global $PATH__APP_ROOT, $logger;
		$controller_classname = null;
		$requested_controller_name = self::classifyName($requested_controller_name);
		if(file_exists($PATH__APP_ROOT.'/'.CONSTS::CONTROLLER_DIR.'/'.$requested_controller_name.'.php')) {
			$controller_classname = "Controller_".$requested_controller_name;
		} else {
			$logger->warn(__METHOD__.'  controller file requested ['.$PATH__APP_ROOT
				.'/'.CONSTS::CONTROLLER_DIR.'/'.$requested_controller_name.'.php'.'] does not exist');
		}
		return $controller_classname;
	}
	/**
	 * This takes a relative path and first looks for it in app, then
	 * in lib.
	 *
	 * @param string $relative_path_to_file
	 * @param string $append
	 * @return unknown
	 */
	public static final function PATH_TO_TEMPLATE_FILE($relative_path_to_file) {
		return self::determine_app_or_framework_for_file(CONSTS::$TEMPLATE_DIR.'/'.ltrim($relative_path_to_file,'/ '));
	}
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public static final function FILE_NOT_FOUND_TEMPLATE() {
		return self::PATH_TO_TEMPLATE_FILE(CONSTS::$FILE_NOT_FOUND_TEMPLATE);
	}
	/**
	 * given a relative path, this looks first for the file
	 * in APP then in FRAMEWORK
	 * 
	 * @param string $relative_path_to_file
	 * @return string The full path to the file if it exists on filesystem, else NULL
	 */
	private static final function determine_app_or_framework_for_file($relative_path_to_file) {
		global $PATH__APP_ROOT,$PATH__FRAMWORK_ROOT;
		$full_path_to_file = null;
		$relative_path_to_file = '/'.ltrim($relative_path_to_file,'/');
		if(file_exists($PATH__APP_ROOT.$relative_path_to_file)) {
			$full_path_to_file = $PATH__APP_ROOT.$relative_path_to_file;
		} else if(file_exists($PATH__FRAMWORK_ROOT.$relative_path_to_file)) {
			$full_path_to_file = $PATH__FRAMWORK_ROOT.$relative_path_to_file;
		}
		return $full_path_to_file;
	}
	private static final function classifyName($name) {
		$name = strtolower($name);
		if (strstr($name,'_')) {
			$name = explode('_',$name);
			$name = array_map('ucfirst',$name);
			$name = implode($name);
		} else {
			$name = ucfirst((strtolower($name)));
		}
		return $name;
		
	}
}
?>