<?php

class metahub_code_Constraint {
	public function __construct(){}
	static $operators;
	function __toString() { return 'metahub.code.Constraint'; }
}
metahub_code_Constraint::$operators = (new _hx_array(array("=", "<", ">", "<=", ">=")));
