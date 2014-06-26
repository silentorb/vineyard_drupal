<?php

class metahub_parser_Repetition_Match extends metahub_parser_Match {
	public function __construct($pattern, $start, $length = null, $children = null, $matches = null) {
		if(!php_Boot::$skip_constructor) {
		parent::__construct($pattern,$start,$length,$children,$matches);
	}}
	public $dividers;
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
	function __toString() { return 'metahub.parser.Repetition_Match'; }
}
