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
	protected function save(array $field_values = null) {
		$insert_statement_table = $this->model_name.'(';
		$insert_statement_values = '(';
		if ($field_values) {
			// do intersect to delete any $field_values not present in $this->field_names;
			
			
//			// prepare the statement
//			$stmt = $pdo->prepare($sql);
//			
//			// bind php variables to the placeholders in the statement
//			$stmt->bindParam(':n', $name);
//			$stmt->bindParam(':a', $age);
//			$stmt->bindParam(':s', $sex);
//			$stmt->bindParam(':l', $location);
			# // insert one row  
			# $name = 'Christer';  
			# $age = 26;  
			# $sex = 'm';  
			# $location = 'norway';  
			#   
			# // execute the statement  
			# $stmt->execute();
		}
		
	}
	protected function query($sql) {
		return $this->db_handle->query($sql);
	}
	protected function execute($sql) {
		return $this->db_handle->execute($sql);
	}
	
}
