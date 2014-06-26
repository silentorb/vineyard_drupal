<?php

class metahub_engine_Context {
	public function __construct($property_port, $entry_node) {
		if(!php_Boot::$skip_constructor) {
		$this->property_port = $property_port;
		$this->entry_node = $entry_node;
	}}
	public $property_port;
	public $entry_node;
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
	function __toString() { return 'metahub.engine.Context'; }
}
