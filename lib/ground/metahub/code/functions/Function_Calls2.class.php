<?php

class metahub_code_functions_Function_Calls2 {
	public function __construct(){}
	static function sum($args, $type) {
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
	static function subtract($args, $type) {
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
	static function count($args, $type) {
		$result = _hx_len(_hx_array_get($args->first(), 0));
		haxe_Log::trace("count", _hx_anonymous(array("fileName" => "Functions.hx", "lineNumber" => 57, "className" => "metahub.code.functions.Function_Calls2", "methodName" => "count", "customParams" => (new _hx_array(array($result))))));
		return $result;
	}
	function __toString() { return 'metahub.code.functions.Function_Calls2'; }
}
