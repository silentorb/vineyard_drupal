<?php

class metahub_schema_Property_Port implements metahub_engine_IPort{
	public function __construct($property) {
		if(!php_Boot::$skip_constructor) {
		$this->connections = new _hx_array(array());
		$this->property = $property;
	}}
	public $property;
	public $connections;
	public function connect($other) {
		$this->connections->push($other);
		$other->connections->push($this);
	}
	public function get_type() {
		return $this->property->type;
	}
	public function get_value($context) {
		return $context->node->get_value($this->property->id);
	}
	public function set_value($value, $context) {
		$context->node->set_value($this->property->id, $value);
		return $value;
	}
	public function output($value, $context) {
		{
			$_g = 0;
			$_g1 = $this->connections;
			while($_g < $_g1->length) {
				$other = $_g1[$_g];
				++$_g;
				$value = $other->set_value($value, $context);
				unset($other);
			}
		}
		return $value;
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
