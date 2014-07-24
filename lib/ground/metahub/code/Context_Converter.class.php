<?php

class metahub_code_Context_Converter {
	public function __construct($input_property, $output_property, $kind) {
		if(!php_Boot::$skip_constructor) {
		$_g = $this;
		$this->input_property = $input_property;
		$this->output_property = $output_property;
		$this->input_port = new metahub_engine_Signal_Port($kind, array(new _hx_lambda(array(&$_g, &$input_property, &$kind, &$output_property), "metahub_code_Context_Converter_0"), 'execute'), null);
		$this->output_port = new metahub_engine_Signal_Port($kind, array(new _hx_lambda(array(&$_g, &$input_property, &$kind, &$output_property), "metahub_code_Context_Converter_1"), 'execute'), null);
		$this->input_port->on_change->push(array(new _hx_lambda(array(&$_g, &$input_property, &$kind, &$output_property), "metahub_code_Context_Converter_2"), 'execute'));
		$this->output_port->on_change->push(array(new _hx_lambda(array(&$_g, &$input_property, &$kind, &$output_property), "metahub_code_Context_Converter_3"), 'execute'));
	}}
	public $input_property;
	public $input_port;
	public $output_property;
	public $output_port;
	public function create_context($context, $node_id) {
		$node = $context->hub->get_node($node_id);
		return new metahub_engine_Context($node, $context->hub);
	}
	public function process($port, $value, $property, $context) {
		if((is_object($_t = $property->type) && !($_t instanceof Enum) ? $_t === 4 : $_t == 4)) {
			$ids = $context->node->get_value($property->id);
			{
				$_g = 0;
				while($_g < $ids->length) {
					$i = $ids[$_g];
					++$_g;
					$port->output($value, $this->create_context($context, $i));
					unset($i);
				}
			}
		} else {
			$node_id = $context->node->get_value($property->id);
			$port->output($value, $this->create_context($context, $node_id));
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
function metahub_code_Context_Converter_0(&$_g, &$input_property, &$kind, &$output_property, $context) {
	{
		return $_g->output_port->get_external_value($context);
	}
}
function metahub_code_Context_Converter_1(&$_g, &$input_property, &$kind, &$output_property, $context1) {
	{
		return $_g->input_port->get_external_value($context1);
	}
}
function metahub_code_Context_Converter_2(&$_g, &$input_property, &$kind, &$output_property, $input, $value, $context2) {
	{
		$_g->process($_g->output_port, $value, $input_property, $context2);
	}
}
function metahub_code_Context_Converter_3(&$_g, &$input_property, &$kind, &$output_property, $input1, $value1, $context3) {
	{
		$_g->process($_g->input_port, $value1, $output_property, $context3);
	}
}
