<?php

class metahub_schema_Types {
	public function __construct(){}
	static $void = 0;
	static $int = 1;
	static $string = 2;
	static $reference = 3;
	static $hlist = 4;
	static $float = 5;
	static $bool = 6;
	static $unknown = 7;
	function __toString() { return 'metahub.schema.Types'; }
}
