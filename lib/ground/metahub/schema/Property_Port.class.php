<?php

class metahub_schema_Property_Port implements metahub_engine_IPort{
	public function __construct($property, $origin) {
		if(!php_Boot::$skip_constructor) {
		$this->dependents = new _hx_array(array());
		$this->dependencies = new _hx_array(array());
		$this->property = $property;
		$this->origin = $origin;
	}}
	public $property;
	public $origin;
	public $dependencies;
	public $dependents;
	public function add_dependency($other, $operator) {
		$relationship = new metahub_engine_Relationship($this, $operator, $other);
		$this->dependencies->push($relationship);
		$other->dependents->push($relationship);
	}
	public function get_type() {
		return $this->property->type;
	}
	public function get_value($context = null) {
		return $context->entry_node->get_value($this->property->id);
	}
	public function set_value($value, $context = null) {
		$this->hexit($value, $context);
		return $value;
	}
	public function enter($value, $context = null) {
		$this->update_dependents($value, $context);
	}
	public function hexit($value, $context = null) {
		$_g = $this;
		if($context->property_port === null) {
			throw new HException(new HException("Not implemented.", null, null, _hx_anonymous(array("fileName" => "Property_Port.hx", "lineNumber" => 52, "className" => "metahub.schema.Property_Port", "methodName" => "exit"))));
		}
		$entry_node = $context->entry_node;
		metahub_schema_Property_Chain_Helper::perform($context->property_port->origin, $entry_node, array(new _hx_lambda(array(&$_g, &$context, &$entry_node, &$value), "metahub_schema_Property_Port_0"), 'execute'), null);
	}
	public function update_dependents($value, $context) {
		$_g = 0;
		$_g1 = $this->dependents;
		while($_g < $_g1->length) {
			$other = $_g1[$_g];
			++$_g;
			$other->set_value($value, $context);
			unset($other);
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
	function __toString() { return 'metahub.schema.Property_Port'; }
}
function metahub_schema_Property_Port_0(&$_g, &$context, &$entry_node, &$value, $node) {
	{
		$node->set_value($_g->property->id, $value);
	}
}
