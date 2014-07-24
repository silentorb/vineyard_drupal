<?php

class Type {
	public function __construct(){}
	static function resolveClass($name) {
		$c = _hx_qtype($name);
		if($c instanceof _hx_class || $c instanceof _hx_interface) {
			return $c;
		} else {
			return null;
		}
	}
	static function createInstance($cl, $args) {
		if($cl->__qname__ === "Array") {
			return (new _hx_array(array()));
		}
		if($cl->__qname__ === "String") {
			return $args[0];
		}
		$c = $cl->__rfl__();
		if($c === null) {
			return null;
		}
		return $inst = $c->getConstructor() ? $c->newInstanceArgs($args->a) : $c->newInstanceArgs();
	}
	static function createEnum($e, $constr, $params = null) {
		$f = Reflect::field($e, $constr);
		if($f === null) {
			throw new HException("No such constructor " . _hx_string_or_null($constr));
		}
		if(Reflect::isFunction($f)) {
			if($params === null) {
				throw new HException("Constructor " . _hx_string_or_null($constr) . " need parameters");
			}
			return Reflect::callMethod($e, $f, $params);
		}
		if($params !== null && $params->length !== 0) {
			throw new HException("Constructor " . _hx_string_or_null($constr) . " does not need parameters");
		}
		return $f;
	}
	static function enumParameters($e) {
		if(_hx_field($e, "params") === null) {
			return (new _hx_array(array()));
		} else {
			return new _hx_array($e->params);
		}
	}
	function __toString() { return 'Type'; }
}
