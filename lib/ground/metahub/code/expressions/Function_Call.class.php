<?php

class metahub_code_expressions_Function_Call implements metahub_code_expressions_Expression{
	public function __construct($trellis, $inputs) {
		if(!php_Boot::$skip_constructor) {
		$this->trellis = $trellis;
		$this->inputs = $inputs;
	}}
	public $type;
	public $inputs;
	public $trellis;
	public function resolve($scope) {
		throw new HException("Code not written for imperative function calls.");
	}
	public function to_port($scope) {
		$node = $scope->hub->create_node($this->trellis);
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
				$source = _hx_array_get($expressions, $i)->to_port($scope);
				$target->add_dependency($source);
				unset($source,$i);
			}
		}
		$output = $node->get_port(0);
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
