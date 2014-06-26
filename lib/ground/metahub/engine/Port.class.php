<?php

class metahub_engine_Port extends metahub_engine_Base_Port {
	public function __construct($node, $hub, $property, $value = null) { if(!php_Boot::$skip_constructor) {
		parent::__construct($node,$hub,$property,$value);
	}}
	function __toString() { return 'metahub.engine.Port'; }
}
