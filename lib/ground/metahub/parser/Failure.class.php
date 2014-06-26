<?php

class metahub_parser_Failure extends metahub_parser_Result {
	public function __construct($pattern, $start, $children = null) { if(!php_Boot::$skip_constructor) {
		$this->pattern = $pattern;
		$this->start = $start;
		$this->success = false;
		if($children !== null) {
			$this->children = $children;
		} else {
			$this->children = new _hx_array(array());
		}
	}}
	function __toString() { return 'metahub.parser.Failure'; }
}
