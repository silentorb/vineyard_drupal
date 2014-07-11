<?php

class metahub_schema_Load_Settings {
	public function __construct($namespace, $auto_identity = null) {
		if(!php_Boot::$skip_constructor) {
		if($auto_identity === null) {
			$auto_identity = false;
		}
		$this->{"namespace"} = $namespace;
		$this->auto_identity = $auto_identity;
	}}
	public $namespace;
	public $auto_identity;
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
	function __toString() { return 'metahub.schema.Load_Settings'; }
}
