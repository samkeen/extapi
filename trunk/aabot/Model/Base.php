<?php
/**
 * This is the base DataObject Class.
 *
 * @package default
 * @author Sam
 **/
abstract class Model_Base {
	
	private $db_handle;
	protected $field_names = array();
	
	/**
	 * Allow an injected db_handle, else create on the fly
	 */
	protected function __construct($db_handle=null) {
		if ($db_handle===null && $config = ENV::load_config_file('db_config')) {
			$db_handle = new Model_DB_Handle($config);
		} else {
			ENV::$log->error(__METHOD__.' Unable to load db config file');
		}
		$this->db_handle = $db_handle;
		
	}
	/**
	 * 
	 * @param array $field_values {optional, we could have set the 
	 * various field values on the model prior to calling this method
	 */
	protected function save(array $field_values = null) {
		if ($field_values) {
			// do intersect to delete any $field_values not present in $this->field_names;
			$field_values = array_intersect_key($field_values, $this->field_names);
			$insert_statement = 
				'INSERT INTO '.$this->model_name.'( `'.implode('`,`',array_keys($field_values)).'` )'
				.' VALUES ( :'.implode(',:',array_keys($field_values)).' )';
			try {
				$statement = $this->db_handle->prepare($insert_statement);
				foreach ($field_values as $field_name => $field_value) {
					 $statement->bindParam(':'.$field_name, $field_value);
				}
				$statement->execute();
			} catch (Exception $e) {
				ENV::$log->error(__METHOD__.'-'.$e->getMessage());
			}
		}
	}
	/**
	 * 
	 * @param array $field_values {optional, we could have set the 
	 * various field values on the model prior to calling this method
	 */
	protected function find(array $field_values = null) {
		if ($field_values) {
			// do intersect to delete any $field_values not present in $this->field_names;
			$field_values = array_intersect_key($field_values, $this->field_names);
			$find_statement = 
				'SELECT '.implode(', ',array_keys($field_values)).' FROM '.$this->model_name.' WHERE ';
			try {
				$statement = $this->db_handle->prepare($find_statement);
				foreach ($field_values as $field_name => $field_value) {
					 $statement->bindParam(':'.$field_name, $field_value);
				}
				$statement->execute();
			} catch (Exception $e) {
				ENV::$log->error(__METHOD__.'-'.$e->getMessage());
			}
		}
	}
	protected function query($sql) {
		return $this->db_handle->query($sql);
	}
	protected function execute($sql) {
		return $this->db_handle->execute($sql);
	}
	
}
