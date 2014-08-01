<?php

class metahub_code_Context_Converter implements metahub_engine_INode{
	public function __construct($output_property, $input_property) {
		if(!php_Boot::$skip_constructor) {
		$this->properties = new _hx_array(array());
		$this->ports = new _hx_array(array());
		$this->properties->push($output_property);
		$this->properties->push($input_property);
		$this->ports->push(new metahub_engine_General_Port($this, 0));
		$this->ports->push(new metahub_engine_General_Port($this, 1));
	}}
	public $ports;
	public $properties;
	public function create_context($context, $node_id) {
		$node = $context->hub->get_node($node_id);
		return new metahub_engine_Context($node, $context->hub);
	}
	public function get_port($index) {
		return $this->ports[$index];
	}
	public function get_value($index, $context) {
		$_g = $this;
		$port = $this->ports[1 - $index];
		$property = $this->properties[$index];
		if((is_object($_t = $property->type) && !($_t instanceof Enum) ? $_t === 4 : $_t == 4)) {
			$list = $context->node->get_value($property->id);
			return Lambda::harray(Lambda::map($list, array(new _hx_lambda(array(&$_g, &$_t, &$context, &$index, &$list, &$port, &$property), "metahub_code_Context_Converter_0"), 'execute')));
		} else {
			haxe_Log::trace("get - Converting " . _hx_string_or_null($property->fullname()) . " to " . _hx_string_or_null($property->other_property->fullname()), _hx_anonymous(array("fileName" => "Context_Converter.hx", "lineNumber" => 52, "className" => "metahub.code.Context_Converter", "methodName" => "get_value")));
			$node_id1 = $context->node->get_value($property->id);
			if($node_id1 === 0) {
				throw new HException(new HException("Context_Converter cannot get value for null reference.", null, null, _hx_anonymous(array("fileName" => "Context_Converter.hx", "lineNumber" => 55, "className" => "metahub.code.Context_Converter", "methodName" => "get_value"))));
			}
			return $port->get_external_value($this->create_context($context, $node_id1));
		}
	}
	public function set_value($index, $value, $context, $source = null) {
		$port = $this->ports[1 - $index];
		$property = $this->properties[$index];
		haxe_Log::trace("set - Converting " . _hx_string_or_null($property->fullname()) . " to " . _hx_string_or_null($property->other_property->fullname()), _hx_anonymous(array("fileName" => "Context_Converter.hx", "lineNumber" => 65, "className" => "metahub.code.Context_Converter", "methodName" => "set_value")));
		if((is_object($_t = $property->type) && !($_t instanceof Enum) ? $_t === 4 : $_t == 4)) {
			$ids = $context->node->get_value($property->id);
			{
				$_g = 0;
				while($_g < $ids->length) {
					$i = $ids[$_g];
					++$_g;
					if($i > 0) {
						$port->set_external_value($value, $this->create_context($context, $i));
					}
					unset($i);
				}
			}
		} else {
			$node_id = $context->node->get_value($property->id);
			if($node_id > 0) {
				$port->set_external_value($value, $this->create_context($context, $node_id));
			}
		}
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
	function __toString() { return 'metahub.code.Context_Converter'; }
}
function metahub_code_Context_Converter_0(&$_g, &$_t, &$context, &$index, &$list, &$port, &$property, $node_id) {
	{
		if($node_id === 0) {
			throw new HException(new HException("Context_Converter cannot get value for null reference.", null, null, _hx_anonymous(array("fileName" => "Context_Converter.hx", "lineNumber" => 46, "className" => "metahub.code.Context_Converter", "methodName" => "get_value"))));
		}
		return $port->get_external_value($_g->create_context($context, $node_id));
	}
}
