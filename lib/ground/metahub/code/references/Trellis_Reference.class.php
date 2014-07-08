<?php

class metahub_code_references_Trellis_Reference extends metahub_code_references_Reference {
	public function __construct($symbol, $chain = null) { if(!php_Boot::$skip_constructor) {
		parent::__construct($symbol,$chain);
	}}
	public function get_port($scope) {
		throw new HException(new HException("Not implemented yet.", null, null, _hx_anonymous(array("fileName" => "Trellis_Reference.hx", "lineNumber" => 18, "className" => "metahub.code.references.Trellis_Reference", "methodName" => "get_port"))));
	}
	public function resolve($scope) {
		throw new HException(new HException("Not implemented yet.", null, null, _hx_anonymous(array("fileName" => "Trellis_Reference.hx", "lineNumber" => 22, "className" => "metahub.code.references.Trellis_Reference", "methodName" => "resolve"))));
	}
	function __toString() { return 'metahub.code.references.Trellis_Reference'; }
}
