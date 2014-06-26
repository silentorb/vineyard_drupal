<?php

class ground_Ground {
	public function __construct($hub, $storage) {
		if(!php_Boot::$skip_constructor) {
		$this->hub = $hub;
		$this->storage = $storage;
	}}
	public $hub;
	public $storage;
	public function run_query($source, $response) {
		$query = new ground_query_Classic_Query();
		$query->extend($source, $this->hub->schema);
		$this->storage->run_query($query, $response);
		return;
	}
	public function run_update($source, $response) {
		$update = new ground_Update();
		$this->storage->run_update($update, $response);
		return;
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
	function __toString() { return 'ground.Ground'; }
}
