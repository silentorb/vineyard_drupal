<?php

class metahub_code_expressions_Literal implements metahub_code_expressions_Expression{
	public function __construct($value, $type) {
		if(!php_Boot::$skip_constructor) {
		$this->value = $value;
		$this->type = $type;
	}}
	public $value;
	public $type;
	public function resolve($scope) {
		return $this->value;
	}
	public function to_port($scope) {
		$trellis = $scope->hub->schema->get_trellis(metahub_code_expressions_Literal::get_type_string($this->type->type), $scope->hub->metahub_namespace, null);
		$node = $scope->hub->create_node($trellis);
		$port = $node->get_port(0);
		$port->set_value($this->value, null);
		return $port;
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
	static function get_type_string($id) {
		$fields = Reflect::fields(_hx_qtype("metahub.schema.Types"));
		$index = $id;
		return $fields[$index + 1];
	}
	function __toString() { return 'metahub.code.expressions.Literal'; }
}
