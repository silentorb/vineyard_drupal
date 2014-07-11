<?php

class metahub_parser_MetaHub_Context extends metahub_parser_Context {
	public function __construct($definition) { if(!php_Boot::$skip_constructor) {
		parent::__construct($definition);
	}}
	public function perform_action($name, $data, $match) {
		$name1 = $match->pattern->name;
		switch($name1) {
		case "start":{
			return metahub_parser_MetaHub_Context::start($data);
		}break;
		case "create_symbol":{
			return metahub_parser_MetaHub_Context::create_symbol($data);
		}break;
		case "create_node":{
			return metahub_parser_MetaHub_Context::create_node($data);
		}break;
		case "create_constraint":{
			return metahub_parser_MetaHub_Context::create_constraint($data);
		}break;
		case "expression":{
			return metahub_parser_MetaHub_Context::expression($data, $match);
		}break;
		case "method":{
			return metahub_parser_MetaHub_Context::method($data);
		}break;
		case "reference":{
			return metahub_parser_MetaHub_Context::reference($data);
		}break;
		case "set_property_block":{
			return metahub_parser_MetaHub_Context::set_property_block($data);
		}break;
		case "set_property":{
			return metahub_parser_MetaHub_Context::set_property($data);
		}break;
		case "set_values":{
			return metahub_parser_MetaHub_Context::set_values($data);
		}break;
		case "trellis_scope":{
			return metahub_parser_MetaHub_Context::trellis_scope($data);
		}break;
		case "constraint_block":{
			return metahub_parser_MetaHub_Context::constraint_block($data);
		}break;
		case "constraint":{
			return metahub_parser_MetaHub_Context::constraint($data);
		}break;
		case "string":{
			return $data[1];
		}break;
		case "int":{
			return Std::parseInt($data);
		}break;
		case "value":{
			return metahub_parser_MetaHub_Context::value($data);
		}break;
		}
		return $data;
	}
	static function start($data) {
		return _hx_anonymous(array("type" => "block", "expressions" => $data[1]));
	}
	static function create_symbol($data) {
		return _hx_anonymous(array("type" => "symbol", "name" => $data[2], "expression" => $data[6]));
	}
	static function expression($data, $match) {
		if(_hx_len($data) < 2) {
			return $data[0];
		}
		$rep_match = $match;
		$operator = _hx_array_get(_hx_array_get($rep_match->dividers, 0)->matches, 1)->get_data();
		$operators = _hx_anonymous(array("+" => "sum", "-" => "subtract"));
		return _hx_anonymous(array("type" => "function", "name" => Reflect::field($operators, $operator), "inputs" => $data));
	}
	static function method($data) {
		return _hx_anonymous(array("type" => "function", "name" => $data[1], "inputs" => (new _hx_array(array()))));
	}
	static function create_constraint($data) {
		return _hx_anonymous(array("type" => "specific_constraint", "path" => $data[0], "expression" => $data[4]));
	}
	static function create_node($data) {
		$result = _hx_anonymous(array("type" => "node", "trellis" => $data[2]));
		if($data[4] !== null && _hx_len($data[4]) > 0) {
			$result->set = $data[4][0];
		}
		return $result;
	}
	static function reference($data) {
		$reference = _hx_anonymous(array("type" => "reference", "path" => $data[0]));
		$methods = $data[1];
		if($methods->length > 0) {
			$method = $methods[0];
			$method->inputs->unshift($reference);
			return $method;
		} else {
			return $reference;
		}
	}
	static function set_property_block($data) {
		$result = _hx_anonymous(array());
		$items = $data[2];
		{
			$_g = 0;
			while($_g < $items->length) {
				$item = $items[$_g];
				++$_g;
				$result[$item[0]] = $item[1];
				unset($item);
			}
		}
		return $result;
	}
	static function set_property($data) {
		return (new _hx_array(array($data[0], $data[4])));
	}
	static function set_values($data) {
		return _hx_anonymous(array("type" => "set", "path" => $data[2], "assignments" => $data[4]));
	}
	static function value($data) {
		return _hx_anonymous(array("type" => "literal", "value" => $data));
	}
	static function trellis_scope($data) {
		return _hx_anonymous(array("type" => "trellis_scope", "path" => $data[0], "statements" => $data[2]));
	}
	static function constraint_block($data) {
		return $data[2];
	}
	static function constraint($data) {
		return _hx_anonymous(array("type" => "constraint", "path" => (new _hx_array(array($data[0]))), "operator" => $data[2], "expression" => $data[4]));
	}
	function __toString() { return 'metahub.parser.MetaHub_Context'; }
}
