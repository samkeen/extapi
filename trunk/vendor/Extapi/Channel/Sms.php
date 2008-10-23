<?php
/**
* SMS Service class file.  Currrently only responsible for page forwarding.
*
* @package		Extapi
*/

/**
* SMS Service class.
*
* @package		Extapi
* @subpackage	Service
*/
require 'Channel_Communicator.php';
class Extapi_Channel_Sms extends Channel_Communicator {
	public function __construct(array $request, Logger $logger) {
		$this->request = $request;
		$this->logger = $logger;
		parent::load_config('channels', 'sms');
	}
	/**
	 * @see Service_Communicator::collect_request_params()
	 *
	 */
	public function collect_request_params() {
	}
	
	/**
	 * @see Service_Communicator::act_on_request_statement()
	 *
	 */
	public function act_on_request_statement() {
	}
	
	/**
	 * @see Service_Communicator::authenticate_request()
	 *
	 */
	public function authenticate_request() {
	}
	
	/**
	 * @see Service_Communicator::gather_feedback()
	 *
	 */
	public function gather_feedback() {
	}
	
	/**
	 * @see Service_Communicator::interpret_request_statement()
	 *
	 */
	public function interpret_request_statement() {
	}

	
}
?>
