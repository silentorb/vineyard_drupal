<?php

class php_Lib {
	public function __construct(){}
	static function associativeArrayOfObject($ob) {
		return (array) $ob;
	}
	function __toString() { return 'php.Lib'; }
}
