<?php

class metahub_code_expressions_Set implements metahub_code_expressions_Expression{
	public function __construct($reference) {
		if(!php_Boot::$skip_constructor) {
		$this->assignments = new _hx_array(array());
		$this->reference = $reference;
	}}
	public $type;
	public $reference;
	public $assignments;
	public function add_assignment($index, $expression) {
		$this->assignments->push(new metahub_code_expressions_Assignment($index, $expression));
	}
	public function resolve($scope) {
		$node = $this->reference->get_node($scope);
		{
			$_g = 0;
			$_g1 = $this->assignments;
			while($_g < $_g1->length) {
				$assignment = $_g1[$_g];
				++$_g;
				$assignment->apply($node, $scope);
				unset($assignment);
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
	function __toString() { return 'metahub.code.expressions.Set'; }
}
