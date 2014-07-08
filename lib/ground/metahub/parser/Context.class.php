<?php

class metahub_parser_Context {
	public function __construct($definition) {
		if(!php_Boot::$skip_constructor) {
		$this->draw_offsets = false;
		$this->debug = false;
		$this->definition = $definition;
	}}
	public $text;
	public $debug;
	public $draw_offsets;
	public $definition;
	public $last_success;
	public function parse($text, $silent = null) {
		if($silent === null) {
			$silent = true;
		}
		$this->text = $text;
		if($this->definition->patterns->length === 0) {
			throw new HException(new HException("Unable to parse; definition does not have any patterns.", null, null, _hx_anonymous(array("fileName" => "Context.hx", "lineNumber" => 19, "className" => "metahub.parser.Context", "methodName" => "parse"))));
		}
		$result = _hx_array_get($this->definition->patterns, 0)->test(new metahub_parser_Position($this, null, null, null), 0);
		if($result->success) {
			$match = $result;
			$offset = $match->start->move($match->length);
			if($offset->get_offset() < strlen($text)) {
				$result->success = false;
				if(!$silent) {
					throw new HException(new HException("Could not find match at " . _hx_string_or_null($offset->get_coordinate_string()) . " [" . _hx_string_or_null(_hx_substr($text, $offset->get_offset(), null)) . "]", null, null, _hx_anonymous(array("fileName" => "Context.hx", "lineNumber" => 28, "className" => "metahub.parser.Context", "methodName" => "parse"))));
				}
			}
		}
		return $result;
	}
	public function perform_action($name, $data, $match) {
		return null;
	}
	public function rewind($messages) {
		$previous = $this->last_success;
		if($previous === null) {
			$messages->push("Could not find previous text match.");
			return null;
		}
		$repetition = $previous->get_repetition($messages);
		$i = 0;
		while($repetition === null) {
			$previous = $previous->last_success;
			if($previous === null) {
				$messages->push("Could not find previous text match with repetition.");
				return null;
			}
			$repetition = $previous->get_repetition($messages);
			if($i++ > 20) {
				throw new HException(new HException("Infinite loop looking for previous repetition.", null, null, _hx_anonymous(array("fileName" => "Context.hx", "lineNumber" => 57, "className" => "metahub.parser.Context", "methodName" => "rewind"))));
			}
		}
		$pattern = $repetition->pattern;
		if($repetition->matches->length > $pattern->min) {
			$repetition->matches->pop();
			$messages->push("rewinding " . _hx_string_or_null($pattern->name) . " " . _hx_string_or_null($previous->start->get_coordinate_string()));
			$repetition->children->pop();
			return $previous->start;
		}
		$messages->push("cannot rewind " . _hx_string_or_null($pattern->name) . ", No other rewind options.");
		return null;
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
	function __toString() { return 'metahub.parser.Context'; }
}
