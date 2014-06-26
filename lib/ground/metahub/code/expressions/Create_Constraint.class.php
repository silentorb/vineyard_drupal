<?php

class metahub_code_expressions_Create_Constraint implements metahub_code_expressions_Expression{
	public function __construct($reference, $expression) {
		if(!php_Boot::$skip_constructor) {
		$this->reference = $reference;
		$this->expression = $expression;
	}}
	public $type;
	public $reference;
	public $expression;
	public function resolve($scope) {
		haxe_Log::trace("constraint", _hx_anonymous(array("fileName" => "Create_Constraint.hx", "lineNumber" => 20, "className" => "metahub.code.expressions.Create_Constraint", "methodName" => "resolve", "customParams" => (new _hx_array(array(Type::getClassName(Type::getClass($this->reference))))))));
		$other_port = $this->expression->to_port($scope);
		if((is_object($_t = $this->reference->get_layer()) && !($_t instanceof Enum) ? $_t === metahub_code_Layer::$schema : $_t == metahub_code_Layer::$schema)) {
			$property_reference = $this->reference;
			$port = $property_reference->get_port($scope);
			$port->add_dependency($other_port);
			return null;
		} else {
		}
		throw new HException("Not implemented yet.");
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
	function __toString() { return 'metahub.code.expressions.Create_Constraint'; }
}
