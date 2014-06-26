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
		throw new HException("Abstract class.  Not implemented.");
	}
	public function get_layer() {
		return $this->symbol->get_layer();
	}
	public function resolve($scope) {
		throw new HException("Not implemented yet.");
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
