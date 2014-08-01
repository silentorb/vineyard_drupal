<?php

class metahub_History {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->print_logs = false;
	}}
	public $print_logs;
	public function log($message) {
		if($this->print_logs) {
			haxe_Log::trace($message, _hx_anonymous(array("fileName" => "History.hx", "lineNumber" => 17, "className" => "metahub.History", "methodName" => "log")));
		}
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
	function __toString() { return 'metahub.History'; }
}
