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
	return is_array($array) && isset($array[$key]) && !empty($array[$key]) ? $array[$key] : $val_if_not_found;
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
	return is_array($array) && isset($array[$key]) ? $array[$key] : $val_if_not_found;
}
/**
 * Echo the HTML escaped version of the string
 *
 * @param unknown_type $text
 */
function h($text) {
	echo htmlentities($text, ENT_QUOTES, 'UTF-8');
}
/**
 * Stub for translation function
 *
 * @param string $text
 * @param boolean $echo_output
 * @return unknown
 */
function t($text, $echo_output = false) {
	if($echo_output) {
		h($text);
	} else {
		return $text;
	}
}
///**
// * determine and return the type of REST request method
// * ['put','delete','post','get']
// *
// * @return string The type of REST request method
// */
//function request_method() {
//	$acceptable_request_methods = array('put','delete','post','get');
//	$request_method = 'get';
//	if (count($_POST)) { 
//		if( ! isset($_POST['_method'])) {
//			$request_method = 'post';
//		} else {
//			$request_method = in_array($_POST['_method'],$acceptable_request_methods) ? $_POST['_method'] : $request_method;
//		}
//	}
//	return $request_method;
//}

function http_redirect($path, $code) {
	header('Location: '.$path);
	exit();
}
