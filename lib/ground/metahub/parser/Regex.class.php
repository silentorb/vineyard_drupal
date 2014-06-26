<?php

class metahub_parser_Regex extends metahub_parser_Pattern {
	public function __construct($text) {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		if(_hx_char_at($text, 0) !== "^") {
			$text = "^" . _hx_string_or_null($text);
		}
		$this->regex = new EReg($text, "");
		$this->text = $text;
	}}
	public $regex;
	public $text;
	public function __test__($start, $depth) {
		if(!$this->regex->matchSub($start->context->text, $start->get_offset(), null)) {
			return $this->failure($start, null);
		}
		$match = $this->regex->matched(0);
		return $this->success($start, strlen($match), null, null);
	}
	public function get_data($match) {
		$start = $match->start;
		$this->regex->matchSub($start->context->text, $start->get_offset(), null);
		return $this->regex->matched(0);
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
	function __toString() { return 'metahub.parser.Regex'; }
}
