<?php

class metahub_code_references_Property_Reference extends metahub_code_references_Reference {
	public function __construct($symbol, $chain = null) { if(!php_Boot::$skip_constructor) {
		parent::__construct($symbol,$chain);
	}}
	public function resolve($scope) {
		throw new HException("Not implemented yet.");
	}
	public function get_port($scope) {
		$property = $this->get_property($scope);
		$origin_chain = $this->create_chain_to_origin($scope);
		$port = new metahub_schema_Property_Port($property, $origin_chain);
		$property->ports->push($port);
		return $port;
	}
	public function get_property($scope) {
		if($this->chain->length === 0) {
			$property_symbol = $this->symbol;
			return $property_symbol->get_property();
		}
		return $this->chain[$this->chain->length - 1];
	}
	public function create_chain_to_origin($scope) {
		if($this->chain->length > 0) {
			$property_symbol = $this->symbol;
			$property = $property_symbol->get_property();
			$full_chain = _hx_deref((new _hx_array(array($property))))->concat($this->chain);
			return metahub_schema_Property_Chain_Helper::flip($full_chain);
		}
		$_this = $scope->definition->_this;
		if($_this !== null && (is_object($_t = $_this->get_trellis()) && !($_t instanceof Enum) ? $_t === $this->symbol->get_parent_trellis() : $_t == $this->symbol->get_parent_trellis())) {
			return (new _hx_array(array()));
		}
		throw new HException("Not implemented");
	}
	function __toString() { return 'metahub.code.references.Property_Reference'; }
}
