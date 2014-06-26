<?php

class metahub_parser_Wrapper extends metahub_parser_Pattern {
	public function __construct($pattern, $action) {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$this->pattern = $pattern;
		$this->action = $action;
	}}
	public $pattern;
	public function __test__($start, $depth) {
		$result = $this->pattern->test($start, $depth);
		if(!$result->success) {
			return $this->failure($start, (new _hx_array(array($result))));
		}
		$match = $result;
		return $this->success($start, $match->length, (new _hx_array(array($result))), (new _hx_array(array($match))));
	}
	public function get_data($match) {
		return _hx_array_get($match->matches, 0)->get_data();
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
	function __toString() { return 'metahub.parser.Wrapper'; }
}
