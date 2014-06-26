<?php

class metahub_parser_Repetition extends metahub_parser_Pattern {
	public function __construct($min = null, $max = null) {
		if(!php_Boot::$skip_constructor) {
		if($max === null) {
			$max = 0;
		}
		if($min === null) {
			$min = 1;
		}
		parent::__construct();
		$this->min = $min;
		$this->max = $max;
	}}
	public $min;
	public $max;
	public $pattern;
	public $divider;
	public function __test__($start, $depth) {
		$context = $start->context;
		$position = $start;
		$step = 0;
		$matches = new _hx_array(array());
		$last_divider_length = 0;
		$length = 0;
		$info_items = new _hx_array(array());
		$dividers = new _hx_array(array());
		do {
			$result = $this->pattern->test($position, $depth + 1);
			$info_items->push($result);
			if(!$result->success) {
				break;
			}
			$match = $result;
			$position = $match->start->move($match->length);
			$length += $match->length + $last_divider_length;
			$matches->push($match);
			++$step;
			$result = $this->divider->test($position, $depth + 1);
			$info_items->push($result);
			if(!$result->success) {
				break;
			}
			$match = $result;
			$dividers->push($match);
			$last_divider_length = $match->length;
			$position = $position->move($match->length);
			unset($result,$match);
		} while($this->max < 1 || $step < $this->max);
		if($step < $this->min) {
			return $this->failure($start, $info_items);
		}
		$final = new metahub_parser_Repetition_Match($this, $start, $length, $info_items, $matches);
		$final->dividers = $dividers;
		return $final;
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
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	function __toString() { return 'metahub.parser.Repetition'; }
}
