<?php

class metahub_code_references_Reference {
	public function __construct($symbol, $chain = null) {
		if(!php_Boot::$skip_constructor) {
		$this->symbol = $symbol;
		$this->chain = $chain;
	}}
	public $symbol;
	public $chain;
	public function get_port($scope) {
		throw new HException(new HException("Abstract class.  Not implemented.", null, null, _hx_anonymous(array("fileName" => "Reference.hx", "lineNumber" => 25, "className" => "metahub.code.references.Reference", "methodName" => "get_port"))));
	}
	public function get_layer() {
		return $this->symbol->get_layer();
	}
	public function get_type_reference() {
		if($this->chain !== null && $this->chain->length > 0) {
			return metahub_code_Type_Reference::create_from_property($this->chain[$this->chain->length - 1]);
		} else {
			return $this->symbol->get_type();
		}
	}
	public function resolve($scope) {
		throw new HException(new HException("Not implemented yet.", null, null, _hx_anonymous(array("fileName" => "Reference.hx", "lineNumber" => 39, "className" => "metahub.code.references.Reference", "methodName" => "resolve"))));
	}
	public function create_converter($scope) {
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
	function __toString() { return 'metahub.code.references.Reference'; }
}
