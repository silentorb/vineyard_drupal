<?php

class metahub_History {
	public function __construct() { 
	}
	public function log($message) {
		haxe_Log::trace($message, _hx_anonymous(array("fileName" => "History.hx", "lineNumber" => 14, "className" => "metahub.History", "methodName" => "log")));
	}
	function __toString() { return 'metahub.History'; }
}
