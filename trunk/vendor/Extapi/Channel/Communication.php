<?php
	interface Channel_Communication {
		public function collect_request_params();
		public function authenticate_request();
		public function interpret_request_statement();
		public function act_on_request_statement();
		public function gather_feedback();
	}
?>