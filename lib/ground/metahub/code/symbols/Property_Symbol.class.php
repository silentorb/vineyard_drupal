<?php

class metahub_code_symbols_Property_Symbol implements metahub_code_symbols_ISchema_Symbol{
	public function __construct($property) {
		if(!php_Boot::$skip_constructor) {
		$this->property = $property;
	}}
	public $property;
	public function get_port($scope, $path = null) {
		throw new HException(new HException("Not supported", null, null, _hx_anonymous(array("fileName" => "Property_Symbol.hx", "lineNumber" => 17, "className" => "metahub.code.symbols.Property_Symbol", "methodName" => "get_port"))));
	}
	public function resolve($scope) {
		return null;
	}
	public function get_layer() {
		return metahub_code_Layer::$schema;
	}
	public function get_trellis() {
		return $this->property->other_trellis;
	}
	public function get_parent_trellis() {
		return $this->property->trellis;
	}
	public function get_property() {
		return $this->property;
	}
	public function create_reference($path) {
		$trellis = $this->get_trellis();
		$chain = metahub_schema_Property_Chain_Helper::from_string($path, $trellis, null);
		if($chain->length === 0) {
			if((is_object($_t = $this->property->type) && !($_t instanceof Enum) ? $_t === 3 : $_t == 3)) {
				return new metahub_code_references_Trellis_Reference($this, $chain);
			}
			return new metahub_code_references_Property_Reference($this, $chain);
		} else {
			$last_property = $chain[$chain->length - 1];
			if($last_property->other_trellis === null) {
				return new metahub_code_references_Property_Reference($this, $chain);
			}
			return new metahub_code_references_Trellis_Reference($this, $chain);
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
	function __toString() { return 'metahub.code.symbols.Property_Symbol'; }
}
