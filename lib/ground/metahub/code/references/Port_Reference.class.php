<?php

class metahub_code_references_Port_Reference extends metahub_code_references_Reference {
	public function __construct($symbol, $chain = null) { if(!php_Boot::$skip_constructor) {
		parent::__construct($symbol,$chain);
	}}
	public function get_port($scope) {
		throw new HException(new HException("Not implemented yet.", null, null, _hx_anonymous(array("fileName" => "Port_Reference.hx", "lineNumber" => 13, "className" => "metahub.code.references.Port_Reference", "methodName" => "get_port"))));
	}
	public function resolve($scope) {
		throw new HException(new HException("Not implemented yet.", null, null, _hx_anonymous(array("fileName" => "Port_Reference.hx", "lineNumber" => 17, "className" => "metahub.code.references.Port_Reference", "methodName" => "resolve"))));
	}
	function __toString() { return 'metahub.code.references.Port_Reference'; }
}
