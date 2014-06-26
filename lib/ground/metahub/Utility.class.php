<?php

class metahub_Utility {
	public function __construct(){}
	static function load_json($url) {
		$json = null;
		$json = sys_io_File::getContent($url);
		return haxe_Json::phpJsonDecode($json);
	}
	function __toString() { return 'metahub.Utility'; }
}
