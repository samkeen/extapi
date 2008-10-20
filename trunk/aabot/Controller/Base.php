<?php
abstract class Controller_Base {
	protected $request_context;
	protected $request_segments = null;
	protected $requested_response_type = null;
	protected $request_method = null;
	protected $logger = null;
	// the name used for related files in the View directory (matches name in URL).
	// ex: Class name CustomRoutes will have view_dir_name of custom_routes
	protected $view_dir_name;
	protected $router;
	protected $requested_action = null;
	
	protected $template_file = null;
	protected $layout_file = null;
	
	protected $payload;
	protected $rendered_template;
	
	/**
	 * Enter description here...
	 *
	 * @param Util_Router $router
	 */
	public function __construct(Util_Router $router) {
		global $logger;
		$this->logger = $logger;
		$this->router = $router;
		
		$this->request_context = $this->router->request_context();
		$this->request_segments = $this->request_context['request_segments'];
		$this->requested_response_type = $this->request_context['requested_response_type'];
		$this->request_method = $this->request_context['request_method'];
		$this->view_dir_name = $this->router->requested_controller_name();
		$this->payload = new SimpleDTO();
		
//		$this->logger->debug(print_r($this,1));
		
	}
	/**
	 * file not found internal action
	 */
	protected function file_not_found_action() {
		$this->logger->debug(__METHOD__.' Calling base controller internal File Not Found Action');
		$this->payload->message = "You've requested an unknown resource";
	}
	
	
	/**
	 * main driver method for a controller
	 */
	public function process($override_template = null, $override_action = null) {
		$this->logger->debug(__METHOD__.' Calling process');
		if ($override_action===null) {
			$this->determine_requested_action();
		}
		if ($override_template===null) {
			$this->set_template_for_action();
		} else {
			$this->template_file = $override_template;
		}
		if (! file_exists($this->template_file)) {
			$this->logger->notice(__METHOD__.' requested template file not found ['.$this->template_file.'], sending to file not found');
			// override the $layout=null, $action=null, $view=null
			$override_template = ENV::FILE_NOT_FOUND_TEMPLATE();
			$this->template_file = $override_template;
			$override_action = CONSTS::$FILE_NOT_FOUND_ACTION;
		}
		if ($this->logger->debugEnabled() && $override_template!==null) {
			$this->logger->debug(__METHOD__.' Template has been set to OVERRIDE VALUE: ['.$override_template.']');
		}
		if ($this->logger->debugEnabled() && $override_action!==null) {
			$this->logger->debug(__METHOD__.' Action has been set to OVERRIDE VALUE: ['.$override_action.']');
		}
		$this->action_and_view($override_action);
		
	}
	/**
	 * If an action is supplied it overrides the calculated action
	 * (used for 404 at this time)
	 */
	private function action_and_view($action=null) {
		$this->set_layout();
		$this->call_action($action);
		$this->render_view();
	}
	/**
	 * each controller must define its own default action
	 */
	protected abstract function default_action();
	/**
	 * Sets the template file path.  First looks in App, then Lib
	 *
	 * @param string $relative_path The relative path (from the View dir) to the template file
	 */	
	protected function set_template($relative_path) {
		$relative_path = ltrim($relative_path,'/');
		$file_path = null;
		if (file_exists(ENV::PATH('TEMPLATE_DIR','/'.$relative_path))) {
			$this->logger->debug(__METHOD__.' Explicitly setting template file to :'.ENV::PATH('TEMPLATE_DIR','/'.$relative_path));
			$this->template_file = ENV::PATH('TEMPLATE_DIR','/'.$relative_path);
		} else if (file_exists(ENV::PATH('LIB_TEMPLATE_DIR','/'.$relative_path))) {
			$this->logger->debug(__METHOD__.' Explicitly setting template file to :'.ENV::PATH('LIB_TEMPLATE_DIR','/'.$relative_path));
			$this->template_file = ENV::PATH('LIB_TEMPLATE_DIR','/'.$relative_path);
		} else {
			$this->logger->debug(__METHOD__.'  Attempted to Explicitly set template file to : ['
				.ENV::PATH('TEMPLATE_DIR','/'.$relative_path). '] But file did not exist.');
			$this->template_file = ENV::PATH('TEMPLATE_DIR','/'.$relative_path);
		}
	}
	
	/**
	 * render the template first and bring any variables defined in it into then
	 * namespace of the layout when it is rendered (if thier is a layout to render.
	 */
	protected function render_view() {
		$this->digest_template();
		if ($this->using_layout()) {
			// set a short name ref to $this->payload for ease of use in the view.
			$payload = $this->payload;
			include($this->layout_file);
		}
	}
	/**
	 * Stores the rendered contents of the template in 
	 * $rendered_template to to be included in the layout
	 * (or rendeded on its own if no template) 
	 *
	 */
	private function digest_template() {
		// set a short name ref to $this->payload for ease of use in the view.
		$payload = $this->payload;
		ob_start();
		include($this->template_file);
		if ($this->using_layout()) {
			$this->logger->debug(__METHOD__.' Using Layout [' . $this->layout_file . ']');
			/**
			 * pull back any mutations of $payload into $this->payload
			 * This allows templates to inject values into the 
			 * surrounding layout. (ex. define a head title, stylesheet, or js import) 
			 */
			$this->payload = $payload;
			$this->rendered_template = ob_get_contents();
			ob_end_clean();
		} else {
			$this->logger->debug(__METHOD__.' Not using Layout (layout_path was found to be [null])');
		}
	}
	
	private function call_action($action=null) {
		$the_action = $action!==null?$action:$this->requested_action;
		$this->logger->debug(__METHOD__.' Invoking Action [' . $the_action .'] ');
		$this->$the_action();
	}
	private function determine_requested_action() {
		// if we have a leftmost segemnt and it is a action method for this controller
		$possible_action = isset($this->request_segments[0]) ? str_replace('-','_',$this->request_segments[0]['value']).'_action' : null;
		if($possible_action!==null && method_exists($this,$possible_action)) {
			$this->requested_action = $possible_action;
			array_shift($this->request_segments);
			$this->logger->debug(__METHOD__.'  Action was found to be: '.$this->requested_action);	
		} else { // use the default action
			if ($this->logger->debugEnabled()) {
				if (isset($this->request_segments[0])) {
					$this->requested_action = CONSTS::$DEFAULT_ACTION;
					$this->logger->debug(__METHOD__.'  Did not find requested Action['.$possible_action.'] Sending to File not found');	
				} else {
					$this->requested_action = CONSTS::$DEFAULT_ACTION;
					$this->logger->debug(__METHOD__.' No action supplied, using default action ['.CONSTS::$DEFAULT_ACTION.']');
				}
			}
		}
	}
	private function set_template_for_action() {
		// if the template has not been set, set it here.  this allows the controller to set it.
		if($this->template_file === null) {
			$this->template_file = $this->detemine_deepest_template_match();
		}
		
	}
	/**
	 * For example if the request URL is "http://example.com/courses/math/algebra/algebra2"
	 * we will look for the following templates in this order and use the first one found
	 * /{template_dir}/courses/math/algebra/algebra2.php
	 * /{template_dir}/courses/math/algebra.php
	 * /{template_dir}/courses/math.php
	 *
	 */
	private function detemine_deepest_template_match() {
		$deepest_template_file_path = null;
		$template_path = $this->view_dir_name.'/'.str_replace('_action','',$this->requested_action).'/';
		// look for template starting with all request segments and then working down
		for($index=count($this->request_segments);$index>=1;$index--) {
			$segment_names = array_slice($this->request_segments,0,$index);
			$segments = array();
			foreach ($segment_names as $name) {
				$segments[] = $name['value'];
			}
			$possible_template_file = $template_path.implode('/',$segments).".php";
			$this->logger->debug(__METHOD__.' trying template match for: '.$possible_template_file);
			if ($deepest_template_file_path = ENV::PATH_TO_TEMPLATE_FILE($possible_template_file)) {
				$this->logger->debug(__METHOD__.' Found deepest template file match: '.$possible_template_file);
				break;
			}
		}
		// look for a template for the action
		if ( ! $deepest_template_file_path) {
			$this->logger->debug(__METHOD__.' trying template match for: '.ENV::PATH('TEMPLATE_DIR','/').$this->view_dir_name.'/'.str_replace('_action','',$this->requested_action).'.php');
			if ($deepest_template_file_path = ENV::PATH_TO_TEMPLATE_FILE($this->view_dir_name.'/'.str_replace('_action','',$this->requested_action).'.php') ) {
				$this->logger->debug(__METHOD__.' Found deepest template file match[action]: '.ENV::PATH('TEMPLATE_DIR','/').$this->view_dir_name.'/'.str_replace('_action','',$this->requested_action).'.php');
			}
		}
		// finally look for a template for the contoller
		if ( ! $deepest_template_file_path) {
			$this->logger->debug(__METHOD__.' trying template match for: '.ENV::PATH('TEMPLATE_DIR','/').$this->view_dir_name.'.php');
			if ($deepest_template_file_path = ENV::PATH_TO_TEMPLATE_FILE($this->view_dir_name.'.php')) {
				$this->logger->debug(__METHOD__.' Found deepest template file match[controller]: '.ENV::PATH('TEMPLATE_DIR','/').$this->view_dir_name.'.php');
			}
		}
		return $deepest_template_file_path;
	}
	/**
	 * Stores the path to the layout file.
	 */
	private function set_layout() {
		$layout_dir = ENV::PATH('LAYOUT_DIR','/');
		$lib_layout_dir = ENV::PATH('LIB_LAYOUT_DIR','/');
		if (file_exists($layout_dir . $this->view_dir_name . '.php')) {
			$this->layout_file = $layout_dir . $this->view_dir_name . '.php';
		} else {
			$this->layout_file = file_exists($layout_dir . CONSTS::$DEFAULT_LAYOUT . '.php')
				? $layout_dir . CONSTS::$DEFAULT_LAYOUT . '.php'
				: $lib_layout_dir . CONSTS::$DEFAULT_LAYOUT.'.php';
		}
	}
	private function using_layout() {
		return $this->layout_file !== null;
	}
}
?>