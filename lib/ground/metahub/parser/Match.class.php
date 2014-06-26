<?php

class metahub_parser_Match extends metahub_parser_Result {
	public function __construct($pattern, $start, $length = null, $children = null, $matches = null) {
		if(!php_Boot::$skip_constructor) {
		if($length === null) {
			$length = 0;
		}
		$this->pattern = $pattern;
		$this->start = $start;
		$this->length = $length;
		$this->success = true;
		if($pattern->type === "regex" || $pattern->type === "literal") {
			$this->last_success = $start->context->last_success;
			$start->context->last_success = $this;
		}
		if($children !== null) {
			$this->children = $children;
		} else {
			$this->children = new _hx_array(array());
		}
		if($matches !== null) {
			$this->matches = $matches;
			{
				$_g = 0;
				while($_g < $matches->length) {
					$match = $matches[$_g];
					++$_g;
					$match->parent = $this;
					unset($match);
				}
			}
		} else {
			$this->matches = new _hx_array(array());
		}
	}}
	public $length;
	public $matches;
	public $last_success;
	public $parent;
	public function debug_info() {
		return _hx_substr($this->start->context->text, $this->start->get_offset(), $this->length);
	}
	public function get_data() {
		$data = $this->pattern->get_data($this);
		return $this->start->context->perform_action($this->pattern->action, $data, $this);
	}
	public function get_repetition($messages) {
		if($this->parent === null) {
			$messages->push("Parent of " . _hx_string_or_null($this->pattern->name) . " is null.");
			return null;
		}
		if($this->parent->pattern->type === "repetition") {
			return $this->parent;
		}
		$messages->push("Trying parent of " . _hx_string_or_null($this->pattern->name) . ".");
		return $this->parent->get_repetition($messages);
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
	function __toString() { return 'metahub.parser.Match'; }
}
