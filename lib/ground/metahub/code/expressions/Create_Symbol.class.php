<?php

class metahub_code_expressions_Create_Symbol implements metahub_code_expressions_Expression{
	public function __construct($symbol, $expression) {
		if(!php_Boot::$skip_constructor) {
		$this->symbol = $symbol;
		$this->expression = $expression;
		$this->type = $expression->type;
	}}
	public $symbol;
	public $expression;
	public $type;
	public function resolve($scope) {
		$value = $this->expression->resolve($scope);
		$scope->set_value($this->symbol->index, $value);
		return $value;
	}
	public function to_port($scope, $group) {
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
	function __toString() { return 'metahub.code.expressions.Create_Symbol'; }
}
