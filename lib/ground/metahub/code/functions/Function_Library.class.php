<?php

class metahub_code_functions_Function_Library {
	public function __construct($hub) {
		if(!php_Boot::$skip_constructor) {
		$this->function_classes = new haxe_ds_EnumValueMap();
		$_g = $this;
		$add = array(new _hx_lambda(array(&$_g, &$hub), "metahub_code_functions_Function_Library_0"), 'execute');
		call_user_func_array($add, array(metahub_code_functions_Functions::$add, "Add_Int", null));
		call_user_func_array($add, array(metahub_code_functions_Functions::$subtract, "Subtract_Int", null));
		call_user_func_array($add, array(metahub_code_functions_Functions::$greater_than, "Greater_Than_Int", null));
		call_user_func_array($add, array(metahub_code_functions_Functions::$lesser_than, "Lesser_Than_Int", null));
		call_user_func_array($add, array(metahub_code_functions_Functions::$count, "Count", null));
	}}
	public $function_classes;
	public function get_function_class($func, $kind) {
		if(!$this->function_classes->exists($func)) {
			throw new HException(new HException("Function " . Std::string($func) . " is not yet implemented.", null, null, _hx_anonymous(array("fileName" => "Function_Library.hx", "lineNumber" => 62, "className" => "metahub.code.functions.Function_Library", "methodName" => "get_function_class"))));
		}
		$map = $this->function_classes->get($func);
		$type = $kind;
		if($map->exists($type)) {
			return $map->get($type);
		}
		$type = 0;
		if(!$map->exists($type)) {
			throw new HException(new HException("Function " . Std::string($func) . " is not yet implemented.", null, null, _hx_anonymous(array("fileName" => "Function_Library.hx", "lineNumber" => 71, "className" => "metahub.code.functions.Function_Library", "methodName" => "get_function_class"))));
		}
		if($map->get($type) === null) {
			throw new HException(new HException("Function " . Std::string($func) . " is not yet implemented.", null, null, _hx_anonymous(array("fileName" => "Function_Library.hx", "lineNumber" => 74, "className" => "metahub.code.functions.Function_Library", "methodName" => "get_function_class"))));
		}
		return $map->get($type);
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
	function __toString() { return 'metahub.code.functions.Function_Library'; }
}
function metahub_code_functions_Function_Library_0(&$_g, &$hub, $func, $class_name, $kind) {
	{
		if($kind === null) {
			$kind = 0;
		}
		$full_class_name = "metahub.code.functions." . _hx_string_or_null($class_name);
		$type = Type::resolveClass($full_class_name);
		if($type === null) {
			throw new HException(new HException("Could not find function class: " . _hx_string_or_null($full_class_name) . ".", null, null, _hx_anonymous(array("fileName" => "Function_Library.hx", "lineNumber" => 24, "className" => "metahub.code.functions.Function_Library", "methodName" => "new"))));
		}
		if(!$_g->function_classes->exists($func)) {
			$v = new haxe_ds_IntMap();
			$_g->function_classes->set($func, $v);
			$v;
		}
		$integer = $kind;
		$trellis = $hub->schema->get_trellis($class_name, $hub->metahub_namespace, true);
		{
			$this1 = $_g->function_classes->get($func);
			$v1 = _hx_anonymous(array("type" => $type, "trellis" => $trellis));
			$this1->set($integer, $v1);
			$v1;
		}
	}
}
