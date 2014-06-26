<?php

class metahub_parser_Bootstrap extends metahub_parser_Context {
	public function __construct($definition) { if(!php_Boot::$skip_constructor) {
		parent::__construct($definition);
	}}
	public function perform_action($name, $data, $match) {
		if($name === null) {
			return $data;
		}
		switch($name) {
		case "group":{
			return $this->group($data);
		}break;
		case "and_group":{
			return $this->and_group($data);
		}break;
		case "or":{
			return $this->or_group($data);
		}break;
		case "literal":{
			return $this->literal($data);
		}break;
		case "pattern":{
			return $this->pattern($data, $match);
		}break;
		case "start":{
			return $this->start($data);
		}break;
		case "repetition":{
			return $this->repetition($data);
		}break;
		case "reference":{
			return $this->reference($data);
		}break;
		case "regex":{
			return $this->regex($data);
		}break;
		case "rule":{
			return $this->rule($data);
		}break;
		default:{
			throw new HException("Invalid parser method: " . _hx_string_or_null($name) . ".");
		}break;
		}
	}
	public function literal($data) {
		return $data[1];
	}
	public function regex($data) {
		return _hx_anonymous(array("type" => "regex", "text" => $data[1]));
	}
	public function reference($data) {
		return _hx_anonymous(array("type" => "reference", "name" => $data));
	}
	public function and_group($data) {
		return _hx_anonymous(array("type" => "and", "patterns" => $data));
	}
	public function group($data) {
		return $data[2];
	}
	public function or_group($data) {
		return _hx_anonymous(array("type" => "or", "patterns" => $data));
	}
	public function pattern($data, $match) {
		$value = $data;
		$w = $value->length;
		if(_hx_equal(_hx_len($data), 0)) {
			return null;
		} else {
			if(_hx_equal(_hx_len($data), 1)) {
				return $data[0];
			} else {
				return _hx_anonymous(array("type" => "and", "patterns" => $data));
			}
		}
	}
	public function repetition($data) {
		$settings = $data[1];
		$result = _hx_anonymous(array("type" => "repetition", "pattern" => _hx_anonymous(array("type" => "reference", "name" => $settings[0])), "divider" => _hx_anonymous(array("type" => "reference", "name" => $settings[1]))));
		if($settings->length > 2) {
			$result->{"min"} = $settings[2];
			if($settings->length > 3) {
				$result->{"max"} = $settings[3];
			}
		}
		return $result;
	}
	public function rule($data) {
		$value = $data[4];
		return _hx_anonymous(array("name" => $data[0], "value" => metahub_parser_Bootstrap_0($this, $data, $value)));
	}
	public function start($data) {
		$map = _hx_anonymous(array());
		{
			$_g = 0;
			$_g1 = Reflect::fields($data);
			while($_g < $_g1->length) {
				$index = $_g1[$_g];
				++$_g;
				$item = Reflect::field($data, $index);
				$map->{$item->name} = $item->value;
				unset($item,$index);
			}
		}
		return $map;
	}
	function __toString() { return 'metahub.parser.Bootstrap'; }
}
function metahub_parser_Bootstrap_0(&$__hx__this, &$data, &$value) {
	if($value !== null && $value->length === 1) {
		return $value[0];
	} else {
		return $value;
	}
}
