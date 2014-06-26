<?php

class metahub_code_references_Trellis_Reference extends metahub_code_references_Reference {
	public function __construct($symbol, $chain = null) { if(!php_Boot::$skip_constructor) {
		parent::__construct($symbol,$chain);
	}}
	public function get_port($scope) {
		throw new HException("Not implemented yet.");
	}
	public function resolve($scope) {
		throw new HException("Not implemented yet.");
	}
	function __toString() { return 'metahub.code.references.Trellis_Reference'; }
}
