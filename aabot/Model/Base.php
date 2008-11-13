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
	protected $attributes = array();
	private $field_values = array();
	
	/**
	 * Allow an injected db_handle, else create on the fly
	 */
	public function __construct($model_class, $db_handle=null) {
		if ($db_handle===null && $config = ENV::load_config_file('db_conf')) {
			$db_handle = new Model_DBHandle($config);
		} else {
			ENV::$log->error(__METHOD__.' Unable to load db config file');
		}
		$this->db_handle = $db_handle;
		$this->model_name = strtolower(str_replace('Model_','',$model_class));
		$this->model_id_name = $this->model_name.'_id';
	}
	public function set($field_name, $field_value) {
		/*
		 * look to see if setting id
		 */
		if ($field_name==$this->model_id_name) {
			$this->id = $field_value;
		} else if (key_exists($field_name,$this->attributes)) {
			$this->field_values[$field_name] = $field_value;
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
			try {
				$statement = $this->db_handle->prepare($save_statement);
				foreach ($this->field_values as $field_name => $field_value) {
					 $statement->bindValue(':'.$field_name, $field_value);
				}
				if ($this->is_new_model()) {
					$statement->bindValue(':'.$this->model_id_name, $this->id);
				}
				$rows_affected = $statement->execute();
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
	 * 
	 * @param array $model_id {optional, we could have set the 
	 * various field values on the model prior to calling this method
	 */
//	public function load($model_id=null) {
//		$result = null;
//		$model_id = $model_id===null ? $this->id : $model_id;
//		if ($model_id) {
//			$result = $this->find(array($this->name.'_id'=>$model_id));
//		} else {
//			ENV::$log->error(__METHOD__. ' Valid model id not supplied as param and not currently set on $this');
//		}
//		return  $result;
//	}
	private function build_where_clause() {
		$where_clause = '';
		// if $this->id is set, just do
		if ($this->id !==null) {
			$where_clause = ' WHERE `'.$this->model_name.'_id` = :'.$this->model_name.'_id ';
		} else if(count($this->field_values)) {
			$where_clause = ' WHERE ';
			$and = '';
			foreach (array_keys($this->field_values) as $field_name) {		
				$where_clause .= $and.'`'.$field_name.'` = :'.$field_name;
				$and = ' AND ';
			}
		}
		return $where_clause;	
	}
	private function build_insert_statement() {
		$insert_statement = 
			'INSERT INTO '.$this->model_name.'( `'.implode('`,`',array_keys($this->field_values)).'` )'
			.' VALUES ( :'.implode(',:',array_keys($this->field_values)).' )';
		return $insert_statement;
	}
	private function build_update_statement() {
		$update_statement = 'UPDATE `'.$this->model_name.'` SET ';
		$comma = '';
		foreach (array_keys($this->attributes) as $field_name) {		
			if (isset($this->field_values[$field_name])) {
				$update_statement .= $comma.'`'.$field_name.'` = :'.$field_name;
				$comma = ', ';
			}
		}
		return $update_statement . ' WHERE `'.$this->model_name.'_id` =  :'.$this->model_id_name;
	}
	/**
	 * store the cleansed submitted values and merge them with the
	 * attributes for this model. (we keep the submitted values for
	 * doing updates)
	 */
	private function set_field_values(array $submitted_data=null) {
		$submitted_data = array_get_else($submitted_data,$this->model_name);
		if ($submitted_data) {
			// check for model_id
			if (isset($submitted_data[$this->model_id_name])) {
				$this->id = $submitted_data[$this->model_id_name];
			}
			$submitted_data = array_intersect_key($submitted_data, $this->attributes);
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
