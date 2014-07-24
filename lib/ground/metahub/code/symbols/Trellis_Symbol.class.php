<?php

class metahub_code_symbols_Trellis_Symbol implements metahub_code_This, metahub_code_symbols_ISchema_Symbol{
	public function __construct($trellis) {
		if(!php_Boot::$skip_constructor) {
		$this->trellis = $trellis;
	}}
	public $trellis;
	public $symbol;
	public function get_trellis() {
		return $this->trellis;
	}
	public function get_parent_trellis() {
		return $this->trellis;
	}
	public function get_port($scope, $path = null) {
		throw new HException(new HException("Not supported", null, null, _hx_anonymous(array("fileName" => "Trellis_Symbol.hx", "lineNumber" => 27, "className" => "metahub.code.symbols.Trellis_Symbol", "methodName" => "get_port"))));
	}
	public function get_type() {
		throw new HException(new HException("Trellis_Symbol.get_type() is not implemented.", null, null, _hx_anonymous(array("fileName" => "Trellis_Symbol.hx", "lineNumber" => 31, "className" => "metahub.code.symbols.Trellis_Symbol", "methodName" => "get_type"))));
	}
	public function resolve($scope) {
		return null;
	}
	public function get_context_symbol($name) {
		$property = $this->trellis->get_property($name);
		if($property === null) {
			return null;
		}
		return new metahub_code_symbols_Property_Symbol($property);
	}
	public function get_layer() {
		return metahub_code_Layer::$schema;
	}
	public function create_reference($path) {
		$trellis = $this->symbol->get_trellis();
		$chain = metahub_schema_Property_Chain_Helper::from_string($path, $trellis, null);
		$last_property = $chain[$chain->length - 1];
		if($last_property->other_trellis === null) {
			return new metahub_code_references_Trellis_Reference($this->symbol, $chain);
		}
		return new metahub_code_references_Property_Reference($this->symbol, $chain);
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
	function __toString() { return 'metahub.code.symbols.Trellis_Symbol'; }
}
