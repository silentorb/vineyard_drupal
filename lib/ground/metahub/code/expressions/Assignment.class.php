<?php

class metahub_code_expressions_Assignment {
	public function __construct($index, $expression) {
		if(!php_Boot::$skip_constructor) {
		$this->index = $index;
		$this->expression = $expression;
	}}
	public $index;
	public $expression;
	public function apply($node, $scope) {
		$node->set_value($this->index, $this->expression->resolve($scope));
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
	function __toString() { return 'metahub.code.expressions.Assignment'; }
}
