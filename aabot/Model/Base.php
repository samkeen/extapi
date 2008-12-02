<?php
/**
 * This is the base DataObject Class.
 *
 * @package default
 * @author Sam
 **/
abstract class Model_Base {
	
	private $db_handle;
	protected $model_name;
	protected $model_id_name;
	protected $id = null;
	private $base_attribute_definitions = array(
		'created' => null,
		'modified' => null,
		'active' => null
	);
	protected $relations = array ();
	/**
	 * defined in the implementing class thusly
	 * 	protected $attribute_definitions = array(
	 *	  'username' => null,
	 *	  'password' => null,
	 *	  'xmpp_jid' => null,
	 *	  'sms_number' => null,
	 *	  'active' => null
	 *  );
	 */
	protected $attribute_definitions = array();
	private $field_values = array();
	private $field_value_comparitors = array();
	
	/**
	 * Allow an injected db_handle, else create on the fly
	 */
	public function __construct($db_handle=null) {
		if ($db_handle===null && $config = ENV::load_config_file('db_conf')) {
			$db_handle = new Model_DBHandle($config);
		} else {
			ENV::$log->error(__METHOD__.' Unable to load db config file');
		}
		$this->db_handle = $db_handle;
		$this->attribute_definitions = array_merge($this->attribute_definitions, $this->base_attribute_definitions);
		if (isset($this->relations['belongs_to'])) {
			$belongs_to = explode(',',$this->relations['belongs_to']);
			foreach ($belongs_to as $relation) {
				$this->attribute_definitions[strtolower($relation).'_id'] = 'int';
			}
		}
		$this->model_name = strtolower(str_replace('Model_','',get_class($this)));
		$this->model_id_name = $this->model_name.'_id';
	}
	/**
	 * 
	 * ex usage: $this->payload->users = $profile->User(array('user_id'=>'username'));
	 */
	public function __call($name, $arguments) {
		if (isset($this->relations['belongs_to'][$name])) {
			$return_structure = array_get_else($arguments,0);
			$where_conditions = array_get_else($arguments,1);
			$result = null;
			// SELECT b, d FROM foo WHERE `b` = :b AND `d` = :d
			$find_statement = 
				$this->build_select_clause($return_structure).' FROM '.$name;
			ENV::$log->debug(__METHOD__.' built find QUERY: '.$find_statement);
			try {
				$statement = $this->db_handle->prepare($find_statement);
//				foreach ($this->field_values as $field_name => $field_value) {
//					$statement->bindValue(':'.$field_name, $field_value);
//				}
//				if ($this->id !== null) {
//					$statement->bindValue(':'.$this->model_id_name, $this->id);
//				}
				$statement->execute();
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			} catch (Exception $e) {
				ENV::$log->error(__METHOD__.'-'.$e->getMessage());
			}
		}
		return $this->apply_return_structure($return_structure,$result);
		
	}
	/**
	 * set reacts to 2 parameter signatures:
	 * set($field_name, $comparison_operator, $field_value)
	 * OR
	 * set($field_name, $field_value)
	 */
	public function set() {
		// determine the param signature we are in and set vars accordingly
		$args = func_get_args();
		$comparison_operator = '=';
		$field_name = $args[0];
		$field_value = func_num_args()==3?func_get_arg(2):func_get_arg(1);
		if (func_num_args()==3) { // ($field_name, $comparison_operator, $field_value)
			$field_value = func_get_arg(2);
			$comparison_operator = func_get_arg(1);
		} else { // ($field_name, $field_value)
			$field_value = func_get_arg(1);
		}
		// look to see if setting id
		if ($field_name==$this->model_id_name) {
			$this->id = $field_value;
		} else if (key_exists($field_name,$this->attribute_definitions)) {
			$this->field_values[$field_name] = $field_value;
			$this->field_value_comparitors[$field_name] = $comparison_operator;
		}
	}

	/**
	 * 
	 * @param array $submitted_data {optional, we could have set the 
	 * various field values on the model prior to calling this method
	 */
	public function save(array $submitted_data = null) {
		$rows_affected = null;
		$this->set_field_values($submitted_data);
		if ($this->have_data_to_save()) {
			$save_statement = $this->is_new_model() 
				? $this->build_update_statement() 
				: $this->build_insert_statement();
			ENV::$log->debug(__METHOD__.' built save QUERY: '.$save_statement);
			$statement = null;
			try {
				if( ! $statement = $this->db_handle->prepare($save_statement)) {
					ENV::$log->error(__METHOD__.' - $statement::prepare failed for query: '
						.$save_statement."\n".print_r($this->db_handle->errorInfo(),1));
				}
				foreach ($this->field_values as $field_name => $field_value) {
					 $statement->bindValue(':'.$field_name, $field_value);
				}
				if ($this->is_new_model()) {
					$statement->bindValue(':'.$this->model_id_name, $this->id);
				}
				$rows_affected = $statement->execute();
				if ($rows_affected===false) {
					ENV::$log->error(__METHOD__.' - $statement->execute() failed for query: '
						.$save_statement."\n".print_r($statement->errorInfo(),1));
				}
			} catch (Exception $e) {
				ENV::$log->error(__METHOD__.'-'.$e->getMessage());
			}
		} else {
			ENV::$log->error(__METHOD__. ' Valid model id not supplied as param and not currently set on $this');
		}
		return $rows_affected;
	}
	public function delete() {
		if ($this->id !== null) {
			$result = null;
			$delete_sql = 'DELETE FROM `'.$this->model_name.'` WHERE `'.$this->model_id_name.'` = :'.$this->model_id_name;
			try {
				$statement = $this->db_handle->prepare($delete_sql);
				$statement->bindValue(':'.$this->model_id_name, $this->id);
				$result = $statement->execute();
			} catch (Exception $e) {
				ENV::$log->error(__METHOD__.'-'.$e->getMessage());
			}
		}
		return $result;
	}
	
	/**
	 * 
	 * @param array $field_values {optional, we could have set the 
	 * various field values on the model prior to calling this method
	 */
	public function find(array $field_values = null) {
		$result = null;
		$this->set_field_values($field_values);
		// SELECT b, d FROM foo WHERE `b` = :b AND `d` = :d
		$find_statement = 
			'SELECT * FROM '.$this->model_name.$this->build_where_clause();
		ENV::$log->debug(__METHOD__.' built find QUERY: '.$find_statement);
		try {
			$statement = $this->db_handle->prepare($find_statement);
			foreach ($this->field_values as $field_name => $field_value) {
				$statement->bindValue(':'.$field_name, $field_value);
			}
			if ($this->id !== null) {
				$statement->bindValue(':'.$this->model_id_name, $this->id);
			}
			$statement->execute();
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			ENV::$log->error(__METHOD__.'-'.$e->getMessage());
		}
		return $result;
	}

	public function findOne(array $field_values = null) {
		$one = $this->find($field_values);
		return isset($one[0]) ? $one[0] : null;
	}
	/**
	 * ex: 
	 * return structure is: array('user_id'=>array('username','age'));
	 * row of results: array('user_id'=> 2, 'age'=>30, 'username' => 'sam');
	 * transformed row = array(2=>array(age=>30, username=>sam))
	 * 
	 * @param $return_structure
	 * array({key} => {field1})
	 * OR
	 * array({key} => array({field1}, {field2},...))
	 */
	private function apply_return_structure(array $return_structure, $results) {
		$structure_formatted_results = null;
		if (isset($results[0])) {
			foreach ($results as $result) {
				foreach ($return_structure as $structure_key => $field) {
					if( ! is_array($field)) {
						$structure_formatted_results[$result[$structure_key]] = $result[$field];
					} else {
						foreach ($field as $value) {
							$structure_formatted_results[$result[$structure_key]][$value] = $result[$value];
						}
					}
				}
			}
		}
		return $structure_formatted_results;
	}
	/**
	 * if $fields_is_return_struct is true, we blend the keys into the values and create the
	 * select from that
	 */
	private function build_select_clause(array $fields, $fields_is_return_struct=true) {
		$select_fields[] = key($fields);
		foreach ($fields as $field) {
			if (is_array($field)) {
				$select_fields = array_merge($select_fields, $field);
			} else {
				$select_fields[] = $field;
			}
		}
		return isset($select_fields[0]) ? 'SELECT '.implode(', ',$select_fields):null;
	}
	private function build_where_clause() {
		$where_clause = '';
		// if $this->id is set, just do
		if ($this->id !==null) {
			$where_clause = ' WHERE `'.$this->model_name.'_id` = :'.$this->model_name.'_id ';
		} else if(count($this->field_values)) {
			$where_clause = ' WHERE ';
			$and = '';
			foreach (array_keys($this->field_values) as $field_name) {		
				$where_clause .= $and.'`'.$field_name.'` '.$this->field_value_comparitors[$field_name].' :'.$field_name;
				$and = ' AND ';
			}
		}
		return $where_clause;	
	}
	private function build_insert_statement() {
		$insert_statement = 
			'INSERT INTO '.$this->model_name.'( `'.implode('`,`',array_keys($this->field_values)).'`, `modified`, `created` )'
			.' VALUES ( :'.implode(',:',array_keys($this->field_values)).', now(), now() )';
		return $insert_statement;
	}
	private function build_update_statement() {
		$update_statement = 'UPDATE `'.$this->model_name.'` SET modified = now(), ';
		$comma = '';
		foreach (array_keys($this->attribute_definitions) as $field_name) {		
			if (isset($this->field_values[$field_name])) {
				$update_statement .= $comma.'`'.$field_name.'` = :'.$field_name;
				$comma = ', ';
			}
		}
		return $update_statement . ' WHERE `'.$this->model_name.'_id` =  :'.$this->model_id_name;
	}
	/**
	 * store the cleansed submitted values and merge them with the
	 * attribute_definitions for this model. (we keep the submitted values for
	 * doing updates)
	 */
	private function set_field_values(array $submitted_data=null) {
		$submitted_data = array_get_else($submitted_data,$this->model_name);
		if ($submitted_data) {
			// keep `created` and `modified` internal
			if (isset($submitted_data['created']) || isset($submitted_data['modified']) ) {
				ENV::$log->notice(__METHOD__.' Found `created` and/or `modified` in submitted data.  These are for internal use only so they will be ignored');
				unset($submitted_data['created']);
				unset($submitted_data['modified']);
			}
			// check for model_id
			if (isset($submitted_data[$this->model_id_name])) {
				$this->id = $submitted_data[$this->model_id_name];
			}
			$submitted_data = array_intersect_key($submitted_data, $this->attribute_definitions);
			$this->field_values = array_merge($this->field_values, $submitted_data);
		}
	}
	private function have_data_to_save() {
		return (boolean)count($this->field_values);
	}
	private function is_new_model() {
		return $this->id !== null;
	}
	protected function field_values($key_name=null) {
		$return = null;
		if ($key_name!==null) {
			$return = array_get_else($this->field_values,$key_name);
		} else {
			$return = $this->field_values;
		}
		return $return;
	}
	protected function query($sql) {
		return $this->db_handle->query($sql);
	}
	protected function execute($sql) {
		return $this->db_handle->execute($sql);
	}
	
}
