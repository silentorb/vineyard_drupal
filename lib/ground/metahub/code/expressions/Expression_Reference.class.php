<?php

class metahub_code_expressions_Expression_Reference implements metahub_code_expressions_Expression{
	public function __construct($reference) {
		if(!php_Boot::$skip_constructor) {
		$this->reference = $reference;
	}}
	public $reference;
	public $type;
	public function resolve($scope) {
		return $this->reference->resolve($scope)->id;
	}
	public function to_port($scope) {
		return $this->reference->get_port($scope);
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
	function __toString() { return 'metahub.code.expressions.Expression_Reference'; }
}
