<?php

class metahub_engine_Port {
	public function __construct($node, $id) {
		if(!php_Boot::$skip_constructor) {
		$this->connections = new _hx_array(array());
		$this->node = $node;
		$this->id = $id;
	}}
	public $connections;
	public $node;
	public $id;
	public function connect($port) {
		if($this->connections->indexOf($port, null) > -1) {
			return;
		}
		$this->connections->push($port);
		$port->connections->push($this);
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
	function __toString() { return 'metahub.engine.Port'; }
}
