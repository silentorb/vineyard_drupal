<?php

class metahub_schema_Namespace {
	public function __construct($name, $fullname) {
		if(!php_Boot::$skip_constructor) {
		$this->children = new haxe_ds_StringMap();
		$this->trellises = new haxe_ds_StringMap();
		$this->name = $name;
		$this->fullname = $fullname;
	}}
	public $name;
	public $fullname;
	public $trellises;
	public $children;
	public $parent;
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
	function __toString() { return 'metahub.schema.Namespace'; }
}
