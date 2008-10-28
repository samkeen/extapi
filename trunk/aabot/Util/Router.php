<?php
class Util_Router {
	
	private $original_requested_url;
	private $reconciled_requested_url;
	
	private $requested_url_segments= array();
	
	private $requested_controller = array('name'=>null,'sub_designation'=>null);
	
	private $requested_response_type;

	private $custom_routes;

	
	const PATH_SEPARATOR = '/';
	const REDIRECT_VAR = ';c;';
	CONST REQUEST_METHOD_TOKEN = '_method';
	
	public function __construct(array $custom_routes = null) {
		$this->original_requested_url = isset($_GET[self::REDIRECT_VAR])?$_GET[self::REDIRECT_VAR]:'';
		$this->custom_routes = $custom_routes;
		$this->reconciled_requested_url = $this->reconcile_requested_url();
		$this->process_request();
	}
	public function requested_controller_name() {
		return $this->requested_controller['name']!==null ? strtolower($this->requested_controller['name']) : CONSTS::$DEFAULT_CONTROLLER;
	}
	public function requested_contoller_sub_designation() {
		return $this->requested_controller['sub_designation'];
	}

	public function request_context() {
		return array('request_method' => $this->request_method(),
			'request_segments' => $this->requested_url_segments, 
			'requested_response_type' => $this->requested_response_type
		);
	}
	
	public static function request_params() {
		$strip_from_request = array(self::REDIRECT_VAR=>null);
		return array_diff_key($_REQUEST,$strip_from_request);
//		return array_map('urldecode',array_diff_key($_REQUEST,$strip_from_request));
	}
	private function shift_segment() {
		return isset($this->requested_url_segments[0])?array_shift($this->requested_url_segments):false;
	}
	private function process_request() {
		$request_segments = explode(self::PATH_SEPARATOR, $this->reconciled_requested_url);
		foreach ($request_segments as $index => $request_segment) {
			$dot_index = stripos($request_segment,'.');
			if ($dot_index!==false) {
				$this->requested_url_segments[$index]['sub_designation'] = substr($request_segment,$dot_index+1);
				$this->requested_url_segments[$index]['value'] = $dot_index>0 ? substr($request_segment,0,$dot_index) : '';
			} else {
				$this->requested_url_segments[$index]['sub_designation'] = null;
				$this->requested_url_segments[$index]['value'] = $request_segment;
			}
		}
		// the the requested response type
		$this->requested_response_type = array_notempty_else($this->requested_url_segments[$index],'sub_designation');
		// set the requested controller
		if($requested_controller = $this->shift_segment()) {
			$this->requested_controller['name'] = $requested_controller['value'];
			$this->requested_controller['sub_designation'] = $requested_controller['sub_designation'];
		}
	}
	private function reconcile_requested_url() {
		return isset($this->custom_routes['/'.$this->original_requested_url]) 
			? ltrim($this->custom_routes['/'.$this->original_requested_url],'/')
			: $this->original_requested_url;
	}
	/**
	 * determine and return the type of REST request method
	 * ['put','delete','post','get']
	 *
	 * @return string The type of REST request method
	 */
	private function request_method() {
		$acceptable_request_methods = array('put','delete','post','get');
		$request_method = 'get';
		if (count($_POST)) { 
			if( ! isset($_POST[self::REQUEST_METHOD_TOKEN])) {
				$request_method = 'post';
			} else {
				$request_method = in_array($_POST[self::REQUEST_METHOD_TOKEN],$acceptable_request_methods) 
					? $_POST[self::REQUEST_METHOD_TOKEN] 
					: $request_method;
			}
		}
		return $request_method;
	}
}
?>