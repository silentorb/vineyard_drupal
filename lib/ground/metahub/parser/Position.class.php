<?php

class metahub_parser_Position {
	public function __construct($context, $offset = null, $y = null, $x = null) {
		if(!php_Boot::$skip_constructor) {
		if($x === null) {
			$x = 1;
		}
		if($y === null) {
			$y = 1;
		}
		if($offset === null) {
			$offset = 0;
		}
		$this->context = $context;
		$this->offset = $offset;
		$this->y = $y;
		$this->x = $x;
	}}
	public $offset;
	public $y;
	public $x;
	public $context;
	public function get_offset() {
		return $this->offset;
	}
	public function get_coordinate_string() {
		return _hx_string_rec($this->y, "") . ":" . _hx_string_rec($this->x, "") . _hx_string_or_null((metahub_parser_Position_0($this)));
	}
	public function move($modifier) {
		if($modifier === 0) {
			return $this;
		}
		$position = new metahub_parser_Position($this->context, $this->offset, $this->y, $this->x);
		$i = 0;
		if($modifier > 0) {
			do {
				if(_hx_char_at($this->context->text, $this->offset + $i) === "\x0A") {
					++$position->y;
					$position->x = 1;
				} else {
					++$position->x;
				}
			} while(++$i < $modifier);
		}
		$position->offset += $modifier;
		return $position;
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
	static function pad($depth) {
		$result = "";
		$i = 0;
		while($i++ < $depth) {
			$result .= "  ";
		}
		return $result;
	}
	function __toString() { return 'metahub.parser.Position'; }
}
function metahub_parser_Position_0(&$__hx__this) {
	if($__hx__this->context->draw_offsets) {
		return " " . _hx_string_rec($__hx__this->offset, "");
	} else {
		return "";
	}
}
