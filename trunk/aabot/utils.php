<?php
/**
 * @file
 * General Utility Functions for Front Controller use mostly
 *
 * @author "Sam Keen" <sam@pageindigo.com>
 */
/**
 * Look first in the APP root dir then in the FRAMEWORK dir
 *
 * @param string $class The name of the class
 */
function __autoload($class) {
	global $PATH__APP_ROOT, $PATH__FRAMWORK_ROOT;
	if(file_exists($PATH__APP_ROOT.'/' . str_replace('_', '/', $class) . '.php')) {
		require $PATH__APP_ROOT.'/' . str_replace('_', '/', $class) . '.php';
	} else {
		require $PATH__FRAMWORK_ROOT.'/' . str_replace('_', '/', $class) . '.php';
	}
}
/**
 * shortcut version of 
 * - $variable = isset($_POST['places_search_name'] && ! empty $_POST['places_search_name'])?$_POST['places_search_name']:null;
 * allows to to safely do things like 
 * if(array_notempty_else($_POST,'submit') {...
 *
 * @param array $array
 * @param mixed $key
 * @param mixed $val_if_not_found [default is NULL]
 * @return mixed Value at $array[$key] if exists, else $val_if_not_found
 */
function array_notempty_else(array $array, $key, $val_if_not_found=null) {
	return (isset($array[$key]) && !empty($array[$key]))?$array[$key]:$val_if_not_found;
}
/**
 * shortcut version of 
 * - $variable = isset($_POST['places_search_name'])?$_POST['places_search_name']:null;
 * allows to to safely do things like 
 * if(array_get_else($_POST,'submit') {...
 *
 * @param array $array
 * @param mixed $key
 * @param mixed $val_if_not_found [default is NULL]
 * @return mixed Value at $array[$key] if exists, else $val_if_not_found
 */
function array_get_else($array, $key, $val_if_not_found=null) {
	if ($array===NULL) {
		return $val_if_not_found;
	}
	return isset($array[$key])?$array[$key]:$val_if_not_found;
}
function get_context($request_url_context) {
	global $logger;
	$request_url_context = trim($request_url_context,'/ ');
	$context = array();
	$requested_response_type = CONSTS::$DEFAULT_REQUESTED_RESPONSE_TYPE;
	if (! empty($request_url_context) ) {
		if(strstr($request_url_context,'/')) {
			$context = explode('/',$request_url_context);
		} else {
			$context[] = $request_url_context;
		}
		$last_token = $context[count($context)-1];
		$dot_token = strrchr($last_token,'.');
		$logger->debug('['.__FUNCTION__."]found last token in URL to be: ".print_r($last_token,1));
		if ($dot_token) {
			$requested_response_type = substr($dot_token,1);
			$logger->debug('['.__FUNCTION__."]found requested_response_type to be: ".$requested_response_type);
			$context[count($context)-1] = substr($last_token,0,-(strlen($requested_response_type))-1);
		}
	}
	
	return array('request_method' => request_method(), 'request_segments' => $context, 'requested_response_type' => $requested_response_type);
}
/**
 * determine and return the type of REST request method
 * ['put','delete','post','get']
 *
 * @return string The type of REST request method
 */
function request_method() {
	$acceptable_request_methods = array('put','delete','post','get');
	$request_method = 'get';
	if (count($_POST)) { 
		if( ! isset($_POST['_method'])) {
			$request_method = 'post';
		} else {
			$request_method = in_array($_POST['_method'],$acceptable_request_methods) ? $_POST['_method'] : $request_method;
		}
	}
	return $request_method;
}

function http_redirect($path, $code) {
	header('Location: '.$path);
	exit();
}
