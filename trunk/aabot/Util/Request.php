<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Ex: instanciated Request for URI: http://local.example.com/admin/users/edit/42
 * object(Util_Request)
 * 		private 'protocol' => string 'HTTP/1.1'
 * 		private 'subdomain' => string 'local'
 * 		private 'domain' => string 'extapi.com'
 * 		private 'context' => string 'admin'
 * 		private 'controller' => string 'users'
 * 		private 'action' => string 'edit'
 * 		private 'arguments' =>
 *   	array
 *     		0 => string '42'
 * 		private 'request_method' => string 'get'
 *
 *
 * @author sam
 */
class Util_Request {

    const CONTEXT = '__context';
    const CONTROLLER = '__controller';
    const ACTION = '__action';
    const ARGUMENT = '__argument';
    
    //put your code here
    private $protocol;
    private $subdomain;
    private $domain;
    private $context;
    private $controller;
    private $action;
    private $arguments;

    private $request_segment_suffixes = array(
        self::CONTEXT => null,
        self::CONTROLLER => null,
        self::ACTION => null,
        self::ARGUMENT => array()
    );

    private $request_method; //POST, PUT, DELETE, GET, ...
    
    const REDIRECT_VAR = ';c;';
    const PATH_SEPARATOR = '/';
    const REQUEST_METHOD_TOKEN = '_method';
    const REQUEST_SEGMENT_SUFFIX_DELIMITER = '.';

    public function  __construct() {
        $this->protocol = array_get_else($_SERVER, 'SERVER_PROTOCOL');
        $domain_parts = array_reverse(explode('.', array_get_else($_SERVER, 'HTTP_HOST')));
        $this->subdomain = array_get_else($domain_parts, 2);
        $this->domain = $domain_parts[1].'.'.$domain_parts[0];
        $this->process_request(array_get_else($_GET, self::REDIRECT_VAR, '/'));
        $this->request_method = $this->determine_request_method();
        var_dump($this);die;
        $x=1;


    }
    private function process_request($app_portion_of_uri) {
		global $PATH__APP_ROOT; // ie: "/Library/WebServer/Documents/extapi/app"
        $request_path_segments = explode(self::PATH_SEPARATOR, $app_portion_of_uri);
        foreach ($request_path_segments as $segemnt_index => $request_path_segment) {
            // if context is not already set, check for it
 			if (! $this->context && is_dir($PATH__APP_ROOT.'/'.CONSTS::CONTROLLER_DIR.'/'.$this->get_segment_with_suffix_parts($request_path_segment))) {
                $this->context = $this->record_request_segment(self::CONTEXT, $request_path_segment);
            // set the $requested_url_controller_index to the first non dir in the request path
            } else if(! $this->controller) {
                $this->controller = $this->record_request_segment(self::CONTROLLER, $request_path_segment);
            } else if(! $this->action) { // controller has been set so look to set action
                $this->action = $this->record_request_segment(self::ACTION, $request_path_segment);
            } else { // context,controller,action set so put rest in arguments
                // set the remainder as segments
                $this->arguments = $this->record_request_segment(
                        self::ARGUMENT, array_slice($request_path_segments, $segemnt_index)
                );
            }
        }
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
    /**
     *
     * this is used to build the suffix array for request segments
     * ex: given "http://local.extapi.com/admin.super/users/edit.x/42"
     * will generate suffix array:
     *  '__context' => 'null'super'
     *  '__controller' => null
     *  '__action' => 'x'
     *  '__argument' =>
     *    array
     *         0 => null
     *
     *
     * @param const $segemnt_type [CONTEXT|CONTROLLER|ACTION|ARGUMENT]
     * @param string $request_path_segment The string value for this path segment
     * <note> if $request_path_segment is an array, then we are wholesale setting
     * arguments.
     *
     * @return string The value portion of this request segment.
     * ex: given 'scale.10' returns 'scale'
     * ex: given array('42', '73.rss') returns array('42','73')
     */
    private function record_request_segment($segemnt_type, $request_path_segment) {
        $segemnt_value = null;
        if(is_array($request_path_segment)) {
            foreach ($request_path_segment as $index => $argument_segment_value) {
                if ($segment_parts = $this->get_segment_with_suffix_parts($argument_segment_value)) { // if  delimiter was found
                    $segemnt_value[] = $segment_parts['value'];
                    $this->request_segment_suffixes[self::ARGUMENT][] = $segment_parts['suffix'];
                } else { // no delimiter, but still want to set null for each names argument
                    $segemnt_value[] = $argument_segment_value;
                    $this->request_segment_suffixes[self::ARGUMENT][] = null;
                }
            }
        } else {
            if ($segment_parts = $this->get_segment_with_suffix_parts($request_path_segment)) { // if  delimiter was found
                if($segemnt_type==self::ARGUMENT) {
                    $segemnt_value = $segment_parts['value'];
                    $this->request_segment_suffixes[self::ARGUMENT][] = $segment_parts['suffix'];
                } else {
                    $segemnt_value = $segment_parts['value'];
                    $this->request_segment_suffixes[$segemnt_type] = $segment_parts['suffix'];
                }
            } else { // no delimiter, but still want to set null for each names argument
                $segemnt_value = $request_path_segment;
                if($segemnt_type==self::ARGUMENT) {
                    $this->request_segment_suffixes[self::ARGUMENT][$segemnt_value] = null;
                }
            }
        }
        return $segemnt_value;
    }
    private function get_segment_with_suffix_parts($request_segment_string) {
        $segment_parts = array('value'=>null, 'suffix'=>null);
        if ($suffix_delim_index = $this->get_segment_suffix($request_segment_string)) { // if  delimiter was found
            $segment_parts['value'] = $suffix_delim_index['index']>0 ? substr($request_segment_string,0,$suffix_delim_index['index']) : '';
            $segment_parts['suffix'] = substr($request_segment_string,$suffix_delim_index['index']+1);
        } else {
           $segment_parts = false;
        }
        return $segment_parts;
    }
    private function get_segment_suffix($request_segment_string) {
        $suffix_delim_index = stripos($request_segment_string,self::REQUEST_SEGMENT_SUFFIX_DELIMITER);
        return $suffix_delim_index!==false&&$suffix_delim_index!==null?array('index'=>$suffix_delim_index):false;
    }
}
?>
