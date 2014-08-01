<?php

class metahub_engine_Literal_Node implements metahub_engine_INode{
	public function __construct($value) {
		if(!php_Boot::$skip_constructor) {
		$this->value = $value;
		$this->output = new metahub_engine_General_Port($this, 0);
	}}
	public $value;
	public $output;
	public function get_port($index) {
		return $this->output;
	}
	public function get_value($index, $context) {
		return $this->value;
	}
	public function set_value($index, $value, $context, $source = null) {
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
	function __toString() { return 'metahub.engine.Literal_Node'; }
}
