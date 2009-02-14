<?php
class View_Form {
	
	
	private $controller;
	private $model_name;
	private $model_id_name;
	private $form_action; //add|edit|delete
	
	public function __construct(Controller_Base $controller) {
		$this->controller = $controller;
	}
	
	public function create($model_name, $action = null) {
		$this->form_action = $action != null ? strtolower($action) : $this->controller->action;
		$this->model_name = strtolower($model_name);
		$this->model_id_name = $this->model_name.'_id';
        $relative_action_url = ltrim(implode('/',array($this->controller->context,$this->controller->name,$this->controller->action)),'/');
		// <form action="/channels/add" method="post" accept-charset="utf8">
		echo '<form action="/'.$relative_action_url.'" method="post" accept-charset="utf8">'."\n";
	}
	public function close($submit_button_label=null) {
		// <p><input type="submit" name="submit" value="submit" /><input type="hidden" name="__method" value="post" /></p>
		// <input type="hidden" name="profile[profile_id]" value="$profile['profile_id']" />
		$edit_id = $this->form_action=='edit' 
			? "\n".'<input type="hidden" name="'.$this->model_name.'['.$this->model_id_name.']" value="'.$this->form_get($this->model_id_name).'" />'."\n" 
			: '';
		$form_close = $edit_id.'<input type="hidden" name="__method" value="post" />'."\n</form>\n";
		if ($submit_button_label!==null) {
			$form_close = '<p><input type="submit" name="submit" value="'.$submit_button_label.'" />'.$form_close."</p>\n";
		}
		echo $form_close;
	}
	
	public function text($input_name) {
		//  value="$this->form_get('name')"
		$names = $this->model_field_names($input_name);
		$value = $this->form_action=='edit' ? ' value="'.$this->form_get($names['field']).'" ' : '';
		// <p><label for="channel-name">Name:</label><input id="channel-name" name="channel[name]" type="text" ></p>
		echo '<p><label for="'.$names['model'].'-'.$names['field'].'">'.$this->labelize_name($names['field']).'</label>'
			.'<input id="'.$names['model'].'-'.$names['field'].'" name="'.$names['model'].'['.$names['field'].']" type="text"'
			.$value
			." ></p>\n";
	}
    /**
     *
     * @param <type> $select_name ex: 'User.group' OR 'group' (User implied in that case)
     * @param array $options
     *  - selected
     *  - items
     *  - top
     *  - attributes
     */
	public function select($select_name, $options = array()) {
        $selected = array_get_else($options, 'selected');
        $items = array_get_else($options, 'items');
        $top_option_label = array_get_else($options, 'top');
        $attibutes_string = array_get_else($options, 'attributes');
        
		$names = $this->model_field_names($select_name);
		$name = "{$names['model']}[{$names['field']}]";
		$id = "{$names['model']}-{$names['field']}";
		// sniff out the id for an edit form if user does not explicitly supply it
		if ($selected===null && $this->form_action=='edit') {
			$selected = $this->form_get($names['field']);
		}
		// look for the default named array in the payload
		if ( ! $items) {
            // default to looking for a has many or habtm between $this->model_name
            // and $names['field']
            $model = Util_Naming::modelize($this->model_name);
            $model = new $model;
            $items = $model->lookup_list($names['field']);
            


//			$items = $this->controller->payload->{controller_for_model($select_name)}
//				? $this->controller->payload->{controller_for_model($select_name)}
//				: array();
		}
		$output = '<p><label for="'.$names['model'].'-'.$names['field'].'">'.$this->labelize_name($names['field'])."</label>\n";
		$output .= "\n<select name=\"".$name.'" id="'.$id.'" ';
		$output .= ($attibutes_string) ? $attibutes_string." >\n" : " >\n";
		$output .= $top_option_label ? "\t<option value=\"\">$top_option_label</option>\n" : '';
		$selected = ($selected===null && isset($_REQUEST[$name])) ? $_REQUEST[$name] : $selected;
		foreach ($items as $value => $label) {
			$output.= "\t<option value=\"$value\" ".($selected==$value?' selected="true" ':'').'>'.h($label,false)."</opion>\n";
		}
		$output.="</select>\n";
		echo $output;
	}
	private function model_field_names($input_name) {
		$model_field_names = array('model'=>$this->model_name, 'field' => $input_name);
		if (strstr($input_name,'.')) {
			$parts = explode('.',$input_name);
			$model_field_names['model'] = strtolower(array_get_else($parts,0));
			$model_field_names['field'] = strtolower(array_get_else($parts,1));
		}
		return $model_field_names;
	}
	private function labelize_name($name) {
		if (strstr($name,'_')) {
			$name = implode(' ',array_map('ucfirst',explode('_',$name)));
		} else {
			$name = ucfirst($name);
		}
		return $name;
	}
	/**
	 * get field value for use in a html form.
	 */
	private  function form_get($field_name, $echo=false) {
		$value = '';
		if(isset($this->controller->form_data[$this->model_name][$field_name])) {
			$value = $this->controller->form_data[$this->model_name][$field_name];
		} else if ($this->controller->payload->{$this->model_name}!==null) {
			$value = array_get_else($this->controller->payload->{$this->model_name},$field_name);
		}
		if ($echo) {
			echo $value;
		} else {
			return $value;
		}
	}
}
?>