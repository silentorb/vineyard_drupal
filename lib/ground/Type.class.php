<?php

class Type {
	public function __construct(){}
	static function getClass($o) {
		if($o === null) {
			return null;
		}
		if(is_array($o)) {
			if(count($o) === 2 && is_callable($o)) {
				return null;
			}
			return _hx_ttype("Array");
		}
		if(is_string($o)) {
			if(_hx_is_lambda($o)) {
				return null;
			}
			return _hx_ttype("String");
		}
		if(!is_object($o)) {
			return null;
		}
		$c = get_class($o);
		if($c === false || $c === "_hx_anonymous" || is_subclass_of($c, "enum")) {
			return null;
		} else {
			return _hx_ttype($c);
		}
	}
	static function getClassName($c) {
		if($c === null) {
			return null;
		}
		return $c->__qname__;
	}
	function __toString() { return 'Type'; }
}
