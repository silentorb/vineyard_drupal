<?php

class metahub_code_functions_Add_Int extends metahub_code_functions_Function {
	public function __construct($hub, $id, $trellis) { if(!php_Boot::$skip_constructor) {
		parent::__construct($hub,$id,$trellis);
	}}
	public function forward($args) {
		$total = 0;
		{
			$_g = 0;
			while($_g < $args->length) {
				$arg = $args[$_g];
				++$_g;
				$value = $arg;
				$total += $value;
				unset($value,$arg);
			}
		}
		return $total;
	}
	function __toString() { return 'metahub.code.functions.Add_Int'; }
}
