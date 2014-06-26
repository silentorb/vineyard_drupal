<?php

class metahub_parser_Group_And extends metahub_parser_Group {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function __test__($start, $depth) {
		$length = 0;
		$position = $start;
		$info_items = new _hx_array(array());
		$matches = new _hx_array(array());
		{
			$_g = 0;
			$_g1 = $this->patterns;
			while($_g < $_g1->length) {
				$pattern = $_g1[$_g];
				++$_g;
				$result = $pattern->test($position, $depth + 1);
				$info_items->push($result);
				if(!$result->success) {
					return $this->failure($start, $info_items);
				}
				$match = $result;
				$matches->push($match);
				$position = $position->move($match->length);
				$length += $match->length;
				unset($result,$pattern,$match);
			}
		}
		return $this->success($start, $length, $info_items, $matches);
	}
	public function get_data($match) {
		$result = new _hx_array(array());
		{
			$_g = 0;
			$_g1 = $match->matches;
			while($_g < $_g1->length) {
				$child = $_g1[$_g];
				++$_g;
				$result->push($child->get_data());
				unset($child);
			}
		}
		return $result;
	}
	function __toString() { return 'metahub.parser.Group_And'; }
}
