<?php

class metahub_code_Type_Reference {
	public function __construct($type, $trellis = null) {
		if(!php_Boot::$skip_constructor) {
		$this->type = $type;
		$this->trellis = $trellis;
	}}
	public $trellis;
	public $type;
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
	static function create_from_string($name) {
		$type_id = Reflect::field(_hx_qtype("metahub.schema.Types"), $name);
		return new metahub_code_Type_Reference($type_id, null);
	}
	function __toString() { return 'metahub.code.Type_Reference'; }
}
