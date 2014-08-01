<?php

class metahub_code_expressions_Function_Call implements metahub_code_expressions_Expression{
	public function __construct($func, $type, $inputs) {
		if(!php_Boot::$skip_constructor) {
		$this->func = $func;
		$this->inputs = $inputs;
		$this->type = $type;
	}}
	public $type;
	public $inputs;
	public $func;
	public function resolve($scope) {
		throw new HException(new HException("Code not written for imperative function calls.", null, null, _hx_anonymous(array("fileName" => "Function_Call.hx", "lineNumber" => 24, "className" => "metahub.code.expressions.Function_Call", "methodName" => "resolve"))));
	}
	public function to_port($scope, $group) {
		$hub = $scope->hub;
		if((is_object($_t = $this->func) && !($_t instanceof Enum) ? $_t === metahub_code_functions_Functions::$equals : $_t == metahub_code_functions_Functions::$equals)) {
			return _hx_array_get($this->inputs, 0)->to_port($scope, $group);
		}
		$info = $hub->function_library->get_function_class($this->func, $this->type->type);
		$node = Type::createInstance($info->type, (new _hx_array(array($hub, $hub->nodes->length, $info->trellis))));
		$hub->add_internal_node($node);
		$expressions = $this->inputs;
		$ports = $node->get_inputs();
		$target = null;
		{
			$_g1 = 0;
			$_g = $expressions->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				if($i < $ports->length) {
					$target = $ports[$i];
				}
				$source = _hx_array_get($expressions, $i)->to_port($scope, $group);
				$target->connect($source);
				unset($source,$i);
			}
		}
		$output = $node->get_port(0);
		$group->nodes->unshift($node);
		return $output;
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
	function __toString() { return 'metahub.code.expressions.Function_Call'; }
}
