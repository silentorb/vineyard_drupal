<?php

class metahub_parser_Result {
	public function __construct(){}
	public $start;
	public $children;
	public $success;
	public $pattern;
	public $messages;
	public function debug_info() {
		return "";
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	function __toString() { return 'metahub.parser.Result'; }
}
