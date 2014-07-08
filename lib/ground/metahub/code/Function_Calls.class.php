<?php

class metahub_code_Function_Calls {
	public function __construct(){}
	static function call($id, $args, $type) {
		if(!_hx_has_field(_hx_qtype("metahub.code.Function_Calls"), $id)) {
			throw new HException(new HException("Invalid function name " . _hx_string_or_null($id) . ".", null, null, _hx_anonymous(array("fileName" => "Functions.hx", "lineNumber" => 19, "className" => "metahub.code.Function_Calls", "methodName" => "call"))));
		}
		$func = Reflect::field(_hx_qtype("metahub.code.Function_Calls"), $id);
		return Reflect::callMethod(_hx_qtype("metahub.code.Function_Calls"), $func, (new _hx_array(array($args))));
	}
	static function sum($args) {
		$total = 0;
		if(null == $args) throw new HException('null iterable');
		$__hx__it = $args->iterator();
		while($__hx__it->hasNext()) {
			$arg = $__hx__it->next();
			$value = $arg;
			$total += $value;
			unset($value);
		}
		return $total;
	}
	static function subtract($args) {
		$total = 0;
		$numbers = $args->first();
		$i = 0;
		if(null == $numbers) throw new HException('null iterable');
		$__hx__it = $numbers->iterator();
		while($__hx__it->hasNext()) {
			$arg = $__hx__it->next();
			$value = $arg;
			if($i === 0) {
				$i = 1;
				$total = $value;
			} else {
				$total -= $value;
			}
			unset($value);
		}
		return $total;
	}
	static function count($args) {
		$result = _hx_len(_hx_array_get($args->first(), 0));
		haxe_Log::trace("count", _hx_anonymous(array("fileName" => "Functions.hx", "lineNumber" => 66, "className" => "metahub.code.Function_Calls", "methodName" => "count", "customParams" => (new _hx_array(array($result))))));
		return $result;
	}
	function __toString() { return 'metahub.code.Function_Calls'; }
}
