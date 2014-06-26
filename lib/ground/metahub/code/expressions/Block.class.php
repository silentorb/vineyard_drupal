<?php

class metahub_code_expressions_Block implements metahub_code_expressions_Expression{
	public function __construct($scope_definition) {
		if(!php_Boot::$skip_constructor) {
		$this->type = new metahub_code_Type_Reference(0, null);
		$this->expressions = new _hx_array(array());
		$this->scope_definition = $scope_definition;
	}}
	public $expressions;
	public $type;
	public $scope_definition;
	public function resolve($scope) {
		$scope1 = new metahub_code_Scope($scope->hub, $this->scope_definition, $scope);
		{
			$_g = 0;
			$_g1 = $this->expressions;
			while($_g < $_g1->length) {
				$e = $_g1[$_g];
				++$_g;
				$e->resolve($scope1);
				unset($e);
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
	function __toString() { return 'metahub.code.expressions.Block'; }
}
