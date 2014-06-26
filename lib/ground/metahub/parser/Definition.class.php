<?php

class metahub_parser_Definition {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->pattern_keys = new haxe_ds_StringMap();
		$this->patterns = new _hx_array(array());
	}}
	public $patterns;
	public $pattern_keys;
	public function load($source) {
		{
			$_g = 0;
			$_g1 = Reflect::fields($source);
			while($_g < $_g1->length) {
				$key = $_g1[$_g];
				++$_g;
				$pattern = $this->create_pattern(Reflect::field($source, $key), true);
				$pattern->name = $key;
				{
					$this->pattern_keys->set($key, $pattern);
					$pattern;
				}
				$this->patterns->push($pattern);
				unset($pattern,$key);
			}
		}
		{
			$_g2 = 0;
			$_g11 = Reflect::fields($source);
			while($_g2 < $_g11->length) {
				$key1 = $_g11[$_g2];
				++$_g2;
				$this->initialize_pattern(Reflect::field($source, $key1), $this->pattern_keys->get($key1), true);
				unset($key1);
			}
		}
	}
	public function __create_pattern($source) {
		if(Std::is($source, _hx_qtype("String"))) {
			return new metahub_parser_Literal($source);
		}
		{
			$_g = $source->type;
			switch($_g) {
			case "reference":{
				if(!metahub_parser_Definition_0($this, $_g, $source)) {
					throw new HException("There is no pattern named: " . Std::string($source->name));
				}
				if(_hx_has_field($source, "action")) {
					return new metahub_parser_Wrapper(metahub_parser_Definition_1($this, $_g, $source), $source->action);
				} else {
					$key2 = $source->name;
					return $this->pattern_keys->get($key2);
				}
			}break;
			case "regex":{
				return new metahub_parser_Regex($source->text);
			}break;
			case "and":{
				return new metahub_parser_Group_And();
			}break;
			case "or":{
				return new metahub_parser_Group_Or();
			}break;
			case "repetition":{
				return new metahub_parser_Repetition(null, null);
			}break;
			}
		}
		haxe_Log::trace($source, _hx_anonymous(array("fileName" => "Definition.hx", "lineNumber" => 55, "className" => "metahub.parser.Definition", "methodName" => "__create_pattern")));
		throw new HException("Invalid parser pattern type: " . Std::string($source->type) . ".");
	}
	public function create_pattern($source, $root = null) {
		if($root === null) {
			$root = false;
		}
		if($root && _hx_equal($source->type, "reference")) {
			return new metahub_parser_Wrapper(null, null);
		}
		$pattern = $this->__create_pattern($source);
		if($pattern->type === null) {
			if(_hx_field($source, "type") !== null) {
				$pattern->type = $source->type;
			} else {
				$pattern->type = "literal";
			}
		}
		if(_hx_has_field($source, "backtrack")) {
			$pattern->backtrack = $source->backtrack;
		}
		return $pattern;
	}
	public function initialize_pattern($source, $pattern, $root = null) {
		if($root === null) {
			$root = false;
		}
		if($root && _hx_equal($source->type, "reference")) {
			if(!metahub_parser_Definition_2($this, $pattern, $root, $source)) {
				throw new HException("There is no pattern named: " . Std::string($source->name));
			}
			$wrapper = $pattern;
			{
				$key1 = $source->name;
				$wrapper->pattern = $this->pattern_keys->get($key1);
			}
			if(_hx_has_field($source, "action")) {
				$wrapper->action = $source->action;
			}
			return;
		}
		if(_hx_equal($source->type, "and") || _hx_equal($source->type, "or")) {
			$group = $pattern;
			if(_hx_has_field($source, "action")) {
				$group->action = $source->action;
			}
			{
				$_g = 0;
				$_g1 = Reflect::fields($source->patterns);
				while($_g < $_g1->length) {
					$key2 = $_g1[$_g];
					++$_g;
					$child = Reflect::field($source->patterns, $key2);
					$child_pattern = $this->create_pattern($child, null);
					if($child_pattern === null) {
						throw new HException("Null child pattern!");
					}
					$this->initialize_pattern($child, $child_pattern, null);
					$group->patterns->push($child_pattern);
					unset($key2,$child_pattern,$child);
				}
			}
		} else {
			if(_hx_equal($source->type, "repetition")) {
				$repetition = $pattern;
				$repetition->pattern = $this->create_pattern($source->pattern, null);
				$this->initialize_pattern($source->pattern, $repetition->pattern, null);
				$repetition->divider = $this->create_pattern($source->divider, null);
				$this->initialize_pattern($source->divider, $repetition->divider, null);
				if(_hx_has_field($source, "min")) {
					$repetition->min = $source->min;
				}
				if(_hx_has_field($source, "max")) {
					$repetition->min = $source->max;
				}
				if(_hx_has_field($source, "action")) {
					$repetition->action = $source->action;
				}
			}
		}
	}
	public function load_parser_schema() {
		$data = "{\x0D\x0A  \"start\": {\x0D\x0A    \"type\": \"repetition\",\x0D\x0A    \"action\": \"start\",\x0D\x0A    \"pattern\": {\x0D\x0A      \"type\": \"reference\",\x0D\x0A      \"name\": \"rule\"\x0D\x0A    },\x0D\x0A    \"divider\": {\x0D\x0A      \"type\": \"reference\",\x0D\x0A      \"name\": \"whitespace\"\x0D\x0A    }\x0D\x0A  },\x0D\x0A\x0D\x0A  \"id\": {\x0D\x0A    \"type\": \"regex\",\x0D\x0A    \"text\": \"[a-zA-Z0-9_]+\"\x0D\x0A  },\x0D\x0A\x0D\x0A  \"whitespace\": {\x0D\x0A    \"type\": \"regex\",\x0D\x0A    \"text\": \"\\\\s+\"\x0D\x0A  },\x0D\x0A\x0D\x0A  \"trim\": {\x0D\x0A    \"type\": \"regex\",\x0D\x0A    \"text\": \"\\\\s*\"\x0D\x0A  },\x0D\x0A\x0D\x0A  \"comma\": {\x0D\x0A    \"type\": \"regex\",\x0D\x0A    \"text\": \"[ \\\\r\\\\n]*,[ \\\\r\\\\n]*\"\x0D\x0A  },\x0D\x0A\x0D\x0A  \"semicolon\": {\x0D\x0A    \"type\": \"regex\",\x0D\x0A    \"text\": \"[ \\\\r\\\\n]*;[ \\\\r\\\\n]*\"\x0D\x0A  },\x0D\x0A\x0D\x0A  \"literal\": {\x0D\x0A    \"type\": \"and\",\x0D\x0A    \"action\": \"literal\",\x0D\x0A    \"patterns\": [\x0D\x0A      \"\\\"\",\x0D\x0A      {\x0D\x0A        \"type\": \"regex\",\x0D\x0A        \"text\": \"([^\\\"]|\\\\\\\\\\\")+\"\x0D\x0A      },\x0D\x0A      \"\\\"\"\x0D\x0A    ]\x0D\x0A  },\x0D\x0A\x0D\x0A  \"literal_single_quote\": {\x0D\x0A    \"type\": \"and\",\x0D\x0A    \"action\": \"literal\",\x0D\x0A    \"patterns\": [\x0D\x0A      \"'\",\x0D\x0A      {\x0D\x0A        \"type\": \"regex\",\x0D\x0A        \"text\": \"([^']|\\\\\\\\')+\"\x0D\x0A      },\x0D\x0A      \"'\"\x0D\x0A    ]\x0D\x0A  },\x0D\x0A\x0D\x0A  \"regex\": {\x0D\x0A    \"type\": \"and\",\x0D\x0A    \"action\": \"regex\",\x0D\x0A    \"patterns\": [\x0D\x0A      \"/\",\x0D\x0A      {\x0D\x0A        \"type\": \"regex\",\x0D\x0A        \"text\": \"([^/]|\\\\\\\\/)+\"\x0D\x0A      },\x0D\x0A      \"/\"\x0D\x0A    ]\x0D\x0A  },\x0D\x0A\x0D\x0A  \"reference\": {\x0D\x0A    \"type\": \"reference\",\x0D\x0A    \"name\": \"id\",\x0D\x0A    \"action\": \"reference\"\x0D\x0A  },\x0D\x0A\x0D\x0A  \"repetition\": {\x0D\x0A    \"type\": \"and\",\x0D\x0A    \"action\": \"repetition\",\x0D\x0A    \"patterns\": [\x0D\x0A      \"@(\",\x0D\x0A      {\x0D\x0A        \"type\": \"repetition\",\x0D\x0A        \"pattern\": {\x0D\x0A          \"type\": \"reference\",\x0D\x0A          \"name\": \"id\"\x0D\x0A        },\x0D\x0A        \"divider\": {\x0D\x0A          \"type\": \"reference\",\x0D\x0A          \"name\": \"comma\"\x0D\x0A        }\x0D\x0A      },\x0D\x0A      \")\"\x0D\x0A    ]\x0D\x0A  },\x0D\x0A\x0D\x0A  \"rule\": {\x0D\x0A    \"type\": \"and\",\x0D\x0A    \"action\": \"rule\",\x0D\x0A    \"backtrack\": true,\x0D\x0A    \"patterns\": [\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"id\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"trim\"\x0D\x0A      },\x0D\x0A      \"=\",\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"trim\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"patterns\"\x0D\x0A      }\x0D\x0A    ]\x0D\x0A  },\x0D\x0A\x0D\x0A  \"patterns\": {\x0D\x0A    \"type\": \"repetition\",\x0D\x0A    \"action\": \"pattern\",\x0D\x0A    \"pattern\": {\x0D\x0A      \"type\": \"reference\",\x0D\x0A      \"name\": \"pattern\"\x0D\x0A    },\x0D\x0A    \"divider\": {\x0D\x0A      \"type\": \"reference\",\x0D\x0A      \"name\": \"whitespace\"\x0D\x0A    }\x0D\x0A  },\x0D\x0A\x0D\x0A  \"pattern\": {\x0D\x0A    \"type\": \"or\",\x0D\x0A    \"patterns\": [\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"or\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"group\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"repetition\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"reference\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"literal\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"regex\"\x0D\x0A      }\x0D\x0A    ]\x0D\x0A  },\x0D\x0A\x0D\x0A  \"or_divider\": {\x0D\x0A    \"type\": \"and\",\x0D\x0A    \"patterns\": [\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"trim\"\x0D\x0A      },\x0D\x0A      \"|\",\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"trim\"\x0D\x0A      }\x0D\x0A    ]\x0D\x0A  },\x0D\x0A\x0D\x0A  \"or\": {\x0D\x0A    \"type\": \"repetition\",\x0D\x0A    \"action\": \"or\",\x0D\x0A    \"min\": 2,\x0D\x0A    \"pattern\": {\x0D\x0A      \"type\": \"reference\",\x0D\x0A      \"name\": \"sub_patterns\"\x0D\x0A    },\x0D\x0A    \"divider\": {\x0D\x0A      \"type\": \"reference\",\x0D\x0A      \"name\": \"or_divider\"\x0D\x0A    }\x0D\x0A  },\x0D\x0A\x0D\x0A  \"sub_patterns\": {\x0D\x0A    \"type\": \"repetition\",\x0D\x0A    \"action\": \"pattern\",\x0D\x0A    \"pattern\": {\x0D\x0A      \"type\": \"reference\",\x0D\x0A      \"name\": \"sub_pattern\"\x0D\x0A    },\x0D\x0A    \"divider\": {\x0D\x0A      \"type\": \"reference\",\x0D\x0A      \"name\": \"whitespace\"\x0D\x0A    }\x0D\x0A  },\x0D\x0A\x0D\x0A  \"sub_pattern\": {\x0D\x0A    \"type\": \"or\",\x0D\x0A    \"patterns\": [\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"group\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"repetition\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"reference\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"literal\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"literal_single_quote\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"regex\"\x0D\x0A      }\x0D\x0A    ]\x0D\x0A  },\x0D\x0A\x0D\x0A  \"group\": {\x0D\x0A    \"type\": \"and\",\x0D\x0A    \"action\": \"group\",\x0D\x0A    \"patterns\": [\x0D\x0A      \"(\",\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"trim\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"sub_patterns\"\x0D\x0A      },\x0D\x0A      {\x0D\x0A        \"type\": \"reference\",\x0D\x0A        \"name\": \"trim\"\x0D\x0A      },\x0D\x0A      \")\"\x0D\x0A    ]\x0D\x0A  }\x0D\x0A}";
		$this->load(haxe_Json::phpJsonDecode($data));
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
	function __toString() { return 'metahub.parser.Definition'; }
}
function metahub_parser_Definition_0(&$__hx__this, &$_g, &$source) {
	{
		$key = $source->name;
		return $__hx__this->pattern_keys->exists($key);
	}
}
function metahub_parser_Definition_1(&$__hx__this, &$_g, &$source) {
	{
		$key1 = $source->name;
		return $__hx__this->pattern_keys->get($key1);
	}
}
function metahub_parser_Definition_2(&$__hx__this, &$pattern, &$root, &$source) {
	{
		$key = $source->name;
		return $__hx__this->pattern_keys->exists($key);
	}
}
