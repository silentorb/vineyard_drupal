<?php

class metahub_parser_Literal extends metahub_parser_Pattern {
	public function __construct($text) {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$this->text = $text;
	}}
	public $value;
	public $text;
	public function __test__($start, $depth) {
		if(_hx_substr($start->context->text, $start->get_offset(), strlen($this->text)) === $this->text) {
			return $this->success($start, strlen($this->text), null, null);
		}
		return $this->failure($start, null);
	}
	public function get_data($match) {
		return $this->text;
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
	function __toString() { return 'metahub.parser.Literal'; }
}
