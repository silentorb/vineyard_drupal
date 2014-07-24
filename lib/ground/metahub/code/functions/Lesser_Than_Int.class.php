<?php

class metahub_code_functions_Lesser_Than_Int extends metahub_code_functions_Function {
	public function __construct($hub, $id, $trellis) { if(!php_Boot::$skip_constructor) {
		parent::__construct($hub,$id,$trellis);
	}}
	public function forward($args) {
		throw new HException(new HException("Lesser_Than_Int.forward() is not yet implemented.", null, null, _hx_anonymous(array("fileName" => "Lesser_Than_Int.hx", "lineNumber" => 9, "className" => "metahub.code.functions.Lesser_Than_Int", "methodName" => "forward"))));
	}
	public function reverse($new_value, $args) {
		$first = $new_value;
		$second = $args[0];
		if($first < $second) {
			return $first;
		} else {
			return $second;
		}
	}
	function __toString() { return 'metahub.code.functions.Lesser_Than_Int'; }
}
