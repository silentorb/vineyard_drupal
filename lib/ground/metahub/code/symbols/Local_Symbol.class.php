<?php

class metahub_code_symbols_Local_Symbol implements metahub_code_symbols_Symbol{
	public function __construct($type, $scope_definition, $index, $name) {
		if(!php_Boot::$skip_constructor) {
		$this->type = $type;
		$this->scope_definition = $scope_definition;
		$this->index = $index;
		$this->name = $name;
	}}
	public $type;
	public $scope_definition;
	public $index;
	public $name;
	public function get_node($scope) {
		$id = $this->resolve($scope);
		return $scope->hub->nodes[$id];
	}
	public function get_trellis() {
		return $this->type->trellis;
	}
	public function get_layer() {
		return metahub_code_Layer::$engine;
	}
	public function get_type() {
		throw new HException(new HException("Local_Symbol.get_type() is not implemented.", null, null, _hx_anonymous(array("fileName" => "Local_Symbol.hx", "lineNumber" => 40, "className" => "metahub.code.symbols.Local_Symbol", "methodName" => "get_type"))));
	}
	public function resolve($scope) {
		if($this->scope_definition->depth === $scope->definition->depth) {
			return $scope->values[$this->index];
		}
		if($scope->parent === null) {
			throw new HException(new HException("Could not find scope for symbol: " . _hx_string_or_null($this->name) . ".", null, null, _hx_anonymous(array("fileName" => "Local_Symbol.hx", "lineNumber" => 48, "className" => "metahub.code.symbols.Local_Symbol", "methodName" => "resolve"))));
		}
		return $this->resolve($scope->parent);
	}
	public function get_port($scope, $path = null) {
		$node = $this->get_node2($scope, $path);
		return $node->get_port(_hx_array_get($path, $path->length - 1)->id);
	}
	public function get_node2($scope, $path) {
		$node = $this->get_node($scope);
		$nodes = $scope->hub->nodes;
		$i = 0;
		$length = $path->length - 1;
		while($i < $length) {
			$id = $node->get_value(_hx_array_get($path, $i)->id);
			$node = $nodes[$id];
			++$i;
			unset($id);
		}
		return $node;
	}
	public function create_reference($path) {
		$trellis = $this->get_trellis();
		$chain = metahub_schema_Property_Chain_Helper::from_string($path, $trellis, null);
		if($chain->length === 0) {
			if((is_object($_t = $this->type->type) && !($_t instanceof Enum) ? $_t === 3 : $_t == 3)) {
				return new metahub_code_references_Node_Reference($this, $chain);
			}
			return new metahub_code_references_Port_Reference($this, $chain);
		} else {
			haxe_Log::trace($chain, _hx_anonymous(array("fileName" => "Local_Symbol.hx", "lineNumber" => 82, "className" => "metahub.code.symbols.Local_Symbol", "methodName" => "create_reference")));
			$last_property = $chain[$chain->length - 1];
			if($last_property->other_trellis === null) {
				return new metahub_code_references_Node_Reference($this, $chain);
			}
			return new metahub_code_references_Port_Reference($this, $chain);
		}
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
	function __toString() { return 'metahub.code.symbols.Local_Symbol'; }
}
