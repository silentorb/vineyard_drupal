<?php

class metahub_engine_Context {
	public function __construct($entry_node, $hub) {
		if(!php_Boot::$skip_constructor) {
		if($entry_node === null) {
			throw new HException(new HException("Context node cannot be null.", null, null, _hx_anonymous(array("fileName" => "Context.hx", "lineNumber" => 17, "className" => "metahub.engine.Context", "methodName" => "new"))));
		}
		$this->node = $entry_node;
		$this->hub = $hub;
	}}
	public $node;
	public $hub;
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
