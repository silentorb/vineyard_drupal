<?php

class metahub_code_functions_Count extends metahub_code_functions_Function {
	public function __construct($hub, $id, $trellis) { if(!php_Boot::$skip_constructor) {
		parent::__construct($hub,$id,$trellis);
	}}
	public function forward($args) {
		return _hx_len($args[0]);
	}
	function __toString() { return 'metahub.code.functions.Count'; }
}
