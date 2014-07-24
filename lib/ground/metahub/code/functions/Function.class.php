<?php

class metahub_code_functions_Function implements metahub_engine_INode{
	public function __construct($hub, $id, $trellis) {
		if(!php_Boot::$skip_constructor) {
		$this->ports = new _hx_array(array());
		$this->hub = $hub;
		$this->id = $id;
		$this->trellis = $trellis;
		$properties = $trellis->get_all_properties();
		if(null == $properties) throw new HException('null iterable');
		$__hx__it = $properties->iterator();
		while($__hx__it->hasNext()) {
			$property = $__hx__it->next();
			if($property === $trellis->identity_property) {
				continue;
			}
			$port = new metahub_engine_Signal_Port($property->type, metahub_code_functions_Function_0($this, $hub, $id, $properties, $property, $trellis), null);
			$this->ports->push($port);
			unset($port);
		}
		$properties1 = $trellis->get_all_properties();
		if(null == $properties1) throw new HException('null iterable');
		$__hx__it = $properties1->iterator();
		while($__hx__it->hasNext()) {
			$property1 = $__hx__it->next();
			$port1 = $this->get_port($property1->id);
			if($property1->name !== "output") {
				$port1->on_change->push((isset($this->run_forward) ? $this->run_forward: array($this, "run_forward")));
			} else {
				$port1->on_change->push((isset($this->run_reverse) ? $this->run_reverse: array($this, "run_reverse")));
			}
			unset($port1);
		}
	}}
	public $hub;
	public $ports;
	public $trellis;
	public $id;
	public function get_inputs() {
		$properties = $this->trellis->get_all_properties();
		$result = new _hx_array(array());
		if(null == $properties) throw new HException('null iterable');
		$__hx__it = $properties->iterator();
		while($__hx__it->hasNext()) {
			$property = $__hx__it->next();
			if($property->name !== "output") {
				$result->push($this->get_port($property->id));
			}
		}
		return $result;
	}
	public function get_port($index) {
		return $this->ports[$index];
	}
	public function get_input_values($context) {
		$result = new _hx_array(array());
		{
			$_g1 = 1;
			$_g = $this->ports->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$port = $this->get_port($i);
				$result->push($port->get_external_value($context));
				unset($port,$i);
			}
		}
		return $result;
	}
	public function get_forward($context) {
		return $this->forward($this->get_input_values($context));
	}
	public function get_reverse($context) {
		return $this->get_input_values($context);
	}
	public function run_forward($input, $value, $context) {
		$args = $this->get_input_values($context);
		$context->hub->history->log("function " . _hx_string_or_null($this->trellis->name) . " forward()" . Std::string($args));
		$new_value = $this->forward($args);
		$input->output($new_value, $context);
	}
	public function run_reverse($input, $value, $context) {
		$context->hub->history->log("function " . _hx_string_or_null($this->trellis->name) . " reverse()");
		$new_value = $this->reverse($value, $this->get_input_values($context));
		if(!_hx_equal($new_value, $value)) {
			$input->output($new_value, $context);
		}
		return $new_value;
	}
	public function forward($args) {
		throw new HException(new HException("Function.forward is abstract.", null, null, _hx_anonymous(array("fileName" => "Function.hx", "lineNumber" => 106, "className" => "metahub.code.functions.Function", "methodName" => "forward"))));
	}
	public function reverse($new_value, $args) {
		throw new HException(new HException("Function.reverse is abstract.", null, null, _hx_anonymous(array("fileName" => "Function.hx", "lineNumber" => 110, "className" => "metahub.code.functions.Function", "methodName" => "reverse"))));
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
	function __toString() { return 'metahub.code.functions.Function'; }
}
function metahub_code_functions_Function_0(&$__hx__this, &$hub, &$id, &$properties, &$property, &$trellis) {
	if($property->name === "output") {
		return (isset($__hx__this->get_forward) ? $__hx__this->get_forward: array($__hx__this, "get_forward"));
	} else {
		return (isset($__hx__this->get_reverse) ? $__hx__this->get_reverse: array($__hx__this, "get_reverse"));
	}
}
