<?php
/**
 * Handles URL Routing
 * 
 * example request URI
 * 
 * http://example.com/admin/users/edit/42
 * 
 * first goal of Router is to determine the Controller in the above example.  First the 
 * request portion (/admin/users/edit/42) is tokenized: array('admin','users','edit','42')
 * Then the immediate gaol is to determine which token refers to the Controller.  This is done 
 * by reconsiling the tokens with the Controller directory filesystem
 * 
 * i.e. for the above request, with the belore file structure, 'users' is determined
 * to be the controller. admin is excluded as it is a folder and not a file.  Users is chooses as
 * it is the first Filename to URL Token match (we stop looking after first match) 
 * Controller
 *		  	\admin
 * 				\Users.php
 * 
 * We end up with
 * $pre_controller_tokens = array(
 * 		'0' => array(
 * 			'name' => 'admin',
 * 			'sub_designation' => ''
 * 		)
 * )
 * $controller_token = array(
 * 		'name' => 'users',
 * 		'sub_designation' => ''
 * )
 * $post_controller_tokens = array(
 * 		'0' => array(
 * 			'name' => 'edit',
 * 			'sub_designation' => ''
 * 		),
 * 		'1' => array(
 * 			'name' => '42',
 * 			'sub_designation' => ''
 * 		)
 * )
 *
 */
class Util_Router {
	
	private $original_requested_url;
	private $reconciled_requested_url;
	
	
	/*
	 * i.e. for admin/users/add if the controller folder structure is:
	 * Controller
	 *  	\admin
	 * 			\Users.php
	 * the $pre_controller_path is /admin & the $requested_url_controller_index is 1
	 * & $post_controller_path = /add
	 */ 
	private $pre_controller_path = '/';
	private $post_controller_path = '/';
	private $requested_url_controller_index = null;
	
	private $requested_url_segments= array();
	
	private $requested_controller = array('name'=>null,'sub_designation'=>null);
	
	private $requested_response_type;
	
	private $request_method;

	private $custom_routes;
	
	private static $request_parameters = array();
	
	private $strip_from_request = array(self::REDIRECT_VAR=>null);
	
	private static $debug_requested = false;

	
	const PATH_SEPARATOR = '/';
	const REDIRECT_VAR = ';c;';
	CONST REQUEST_METHOD_TOKEN = '_method';
	
	public function __construct(array $custom_routes = null) {
		$this->original_requested_url = isset($_GET[self::REDIRECT_VAR])?$_GET[self::REDIRECT_VAR]:'';
		$this->custom_routes = $custom_routes;
		$this->request_method = $this->determine_request_method();
		$this->reconciled_requested_url = $this->reconcile_requested_url();
		$this->process_request();
	}
	public function requested_controller_name() {
		return $this->requested_controller['name']!==null ? strtolower($this->requested_controller['name']) : CONSTS::$DEFAULT_CONTROLLER;
	}
	public function requested_contoller_sub_designation() {
		return $this->requested_controller['sub_designation'];
	}
	
	public function pre_controller_path() {
		return $this->pre_controller_path;
	}
	public function request_segments() {
		return $this->requested_url_segments;
	}
	public function requested_response_type() {
		return $this->requested_response_type;
	}
	public function request_method() {
		return $this->request_method;
	}
	
	public static function request_params() {
		return self::$request_parameters;
	}
	public static function debug_requested() {
		return self::$debug_requested;
	}
	
	//==============================================================================
	
	private function process_request() {
		global $PATH__APP_ROOT;
		self::$request_parameters = array_diff_key($_REQUEST,$this->strip_from_request);
		self::$debug_requested = isset(self::$request_parameters['debug']);
		$request_path_segments = explode(self::PATH_SEPARATOR, $this->reconciled_requested_url);
		foreach ($request_path_segments as $index => $request_path_segment) {
			if (is_dir($PATH__APP_ROOT.'/'.CONSTS::CONTROLLER_DIR.$this->pre_controller_path.$request_path_segment)) {
#### just set $pre_controller_tokens = array(
## let pre_controller_path() build the path
				$this->pre_controller_path .= $request_path_segment.'/';
			} else { // set the $requested_url_controller_index to the first non dir in the request path
				// set it if it is not already set
#### set $controller_token = array(
				if (! $this->requested_url_controller_index) {
					$this->requested_url_controller_index = $index;
				} else { // controller has been set so the rest of path is added to post_controller_path
#### just set $post_controller_tokens = array(
### not sure we'll need `post_controller_path` ???
					$this->post_controller_path .= $request_path_segment.'/';
				}
			}
			$dot_index = stripos($request_path_segment,'.');
			if ($dot_index!==false) {
				$this->requested_url_segments[$index]['sub_designation'] = substr($request_path_segment,$dot_index+1);
				$this->requested_url_segments[$index]['value'] = $dot_index>0 ? substr($request_path_segment,0,$dot_index) : '';
			} else {
				$this->requested_url_segments[$index]['sub_designation'] = null;
				$this->requested_url_segments[$index]['value'] = $request_path_segment;
			}
		}
		$this->requested_response_type = array_notempty_else($this->requested_url_segments[$index],'sub_designation'); 
		if($this->requested_url_controller_index !== null) {
			$this->requested_controller['name'] = $this->requested_url_segments[$this->requested_url_controller_index]['value'];
			$this->requested_controller['sub_designation'] = $this->requested_url_segments[$this->requested_url_controller_index]['sub_designation'];
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
	private function determine_request_method() {
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