<?php

class metahub_code_references_Property_Reference extends metahub_code_references_Reference {
	public function __construct($symbol, $chain = null) { if(!php_Boot::$skip_constructor) {
		parent::__construct($symbol,$chain);
	}}
	public function resolve($scope) {
		throw new HException(new HException("Not implemented yet.", null, null, _hx_anonymous(array("fileName" => "Property_Reference.hx", "lineNumber" => 19, "className" => "metahub.code.references.Property_Reference", "methodName" => "resolve"))));
	}
	public function get_port($scope) {
		$property = $this->get_property($scope);
		$port = $property->port;
		if($port === null) {
			$port = $property->port = new metahub_schema_Property_Port($property);
		}
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
		throw new HException(new HException("Not implemented", null, null, _hx_anonymous(array("fileName" => "Property_Reference.hx", "lineNumber" => 54, "className" => "metahub.code.references.Property_Reference", "methodName" => "create_chain_to_origin"))));
	}
	public function create_converter($scope) {
		$prop = $this->get_property($scope);
		if($prop->other_property === null) {
			return null;
		}
		return new metahub_code_Context_Converter($prop, $prop->other_property, $prop->type);
	}
	function __toString() { return 'metahub.code.references.Property_Reference'; }
}
