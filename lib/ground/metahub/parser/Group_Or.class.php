<?php

class metahub_parser_Group_Or extends metahub_parser_Group {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function __test__($position, $depth) {
		$info_items = new _hx_array(array());
		{
			$_g = 0;
			$_g1 = $this->patterns;
			while($_g < $_g1->length) {
				$pattern = $_g1[$_g];
				++$_g;
				$result = $pattern->test($position, $depth + 1);
				$info_items->push($result);
				if($result->success) {
					$match = $result;
					return $this->success($position, $match->length, $info_items, (new _hx_array(array($match))));
					unset($match);
				}
				unset($result,$pattern);
			}
		}
		return $this->failure($position, $info_items);
	}
	public function get_data($match) {
		return _hx_array_get($match->matches, 0)->get_data();
	}
	function __toString() { return 'metahub.parser.Group_Or'; }
}
