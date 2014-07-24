<?php

class metahub_code_functions_Subtract_Int extends metahub_code_functions_Function {
	public function __construct($hub, $id, $trellis) { if(!php_Boot::$skip_constructor) {
		parent::__construct($hub,$id,$trellis);
	}}
	public function forward($args) {
		$first = $args[0];
		$second = $args[1];
		return $first - $second;
	}
	function __toString() { return 'metahub.code.functions.Subtract_Int'; }
}
