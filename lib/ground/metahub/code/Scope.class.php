<?php

class metahub_code_Scope {
	public function __construct($hub, $definition, $parent = null) {
		if(!php_Boot::$skip_constructor) {
		$this->hub = $hub;
		$this->definition = $definition;
		$this->parent = $parent;
		{
			$length = $definition->size();
			$this1 = null;
			$this1 = (new _hx_array(array()));
			$this1->length = $length;
			$this->values = $this1;
		}
	}}
	public $hub;
	public $definition;
	public $values;
	public $parent;
	public $_this;
	public function set_value($index, $value) {
		$val = $value;
		$this->values[$index] = $val;
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
	function __toString() { return 'metahub.code.Scope'; }
}
