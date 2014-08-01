<?php

class metahub_engine_Change {
	public function __construct($node, $index, $value, $context, $source = null) {
		if(!php_Boot::$skip_constructor) {
		$this->node = $node;
		$this->index = $index;
		$this->value = $value;
		$this->context = $context;
		$this->source = $source;
	}}
	public $node;
	public $index;
	public $value;
	public $context;
	public $source;
	public function run() {
		$this->node->set_value($this->index, $this->value, $this->context, $this->source);
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
	function __toString() { return 'metahub.engine.Change'; }
}
