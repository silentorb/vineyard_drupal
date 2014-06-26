<?php

class metahub_parser_Pattern {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->backtrack = false;
	}}
	public $action;
	public $name;
	public $type;
	public $backtrack;
	public function test($position, $depth) {
		$result = $this->__test__($position, $depth);
		if(!$result->success && $this->backtrack) {
			$previous = $position->context->last_success;
			$messages = new _hx_array(array());
			$new_position = $previous->start->context->rewind($messages);
			if($previous->messages !== null) {
				$previous->messages = $previous->messages->concat($messages);
			} else {
				$previous->messages = $messages;
			}
			if($new_position === null) {
				return $result;
			}
			return $this->__test__($new_position, $depth);
		}
		return $result;
	}
	public function __test__($position, $depth) {
		throw new HException("__test__ is an abstract function");
	}
	public function debug_info() {
		return "";
	}
	public function failure($position, $children = null) {
		return new metahub_parser_Failure($this, $position, $children);
	}
	public function success($position, $length, $children = null, $matches = null) {
		return new metahub_parser_Match($this, $position, $length, $children, $matches);
	}
	public function get_data($match) {
		return null;
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
	function __toString() { return 'metahub.parser.Pattern'; }
}
