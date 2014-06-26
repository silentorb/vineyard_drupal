<?php

class metahub_code_expressions_Trellis_Scope implements metahub_code_expressions_Expression{
	public function __construct($trellis, $expressions, $scope_definition) {
		if(!php_Boot::$skip_constructor) {
		$this->trellis = $trellis;
		$this->expressions = $expressions;
		$this->scope_definition = $scope_definition;
	}}
	public $trellis;
	public $expressions;
	public $type;
	public $scope_definition;
	public function resolve($scope) {
		$new_scope = new metahub_code_Scope($scope->hub, $this->scope_definition, $scope);
		{
			$_g = 0;
			$_g1 = $this->expressions;
			while($_g < $_g1->length) {
				$expression = $_g1[$_g];
				++$_g;
				$expression->resolve($new_scope);
				unset($expression);
			}
		}
		return null;
	}
	public function to_port($scope) {
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
	function __toString() { return 'metahub.code.expressions.Trellis_Scope'; }
}
