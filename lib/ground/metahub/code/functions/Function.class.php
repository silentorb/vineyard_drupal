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
			$port = new metahub_engine_General_Port($this, $this->ports->length);
			$this->ports->push($port);
			unset($port);
		}
	}}
	public $hub;
	public $ports;
	public $trellis;
	public $id;
	public function get_value($index, $context) {
		if($index === 0) {
			return $this->run_forward($context);
		} else {
			throw new HException(new HException("Not implemented.", null, null, _hx_anonymous(array("fileName" => "Function.hx", "lineNumber" => 37, "className" => "metahub.code.functions.Function", "methodName" => "get_value"))));
		}
	}
	public function set_value($index, $value, $context, $source = null) {
		if($source === $this->ports[0]) {
			{
				$_g1 = 0;
				$_g = $this->ports->length;
				while($_g1 < $_g) {
					$i = $_g1++;
					_hx_array_get($this->ports, $i)->set_external_value($value, $context);
					unset($i);
				}
			}
			return;
		}
		if($index === 1) {
			$new_value = $this->run_forward($context);
			if(!_hx_equal($new_value, $value)) {
				$this->hub->add_change($this, $index, $new_value, $context, $this->ports[0]);
			} else {
				_hx_array_get($this->ports, 0)->set_external_value($new_value, $context);
			}
		} else {
			$new_value1 = $this->run_reverse($value, $context);
			if(!_hx_equal($new_value1, $value)) {
				$this->hub->add_change($this, $index, $new_value1, $context, $this->ports[0]);
			} else {
				$_g11 = 1;
				$_g2 = $this->ports->length;
				while($_g11 < $_g2) {
					$i1 = $_g11++;
					_hx_array_get($this->ports, $i1)->set_external_value($new_value1, $context);
					unset($i1);
				}
			}
		}
	}
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
	public function run_forward($context) {
		$args = $this->get_input_values($context);
		$context->hub->history->log("function " . _hx_string_or_null($this->trellis->name) . " forward()" . Std::string($args));
		return $this->forward($args);
	}
	public function run_reverse($value, $context) {
		$context->hub->history->log("function " . _hx_string_or_null($this->trellis->name) . " reverse()");
		return $this->reverse($value, $this->get_input_values($context));
	}
	public function forward($args) {
		throw new HException(new HException("Function.forward is abstract.", null, null, _hx_anonymous(array("fileName" => "Function.hx", "lineNumber" => 117, "className" => "metahub.code.functions.Function", "methodName" => "forward"))));
	}
	public function reverse($new_value, $args) {
		throw new HException(new HException("Function.reverse is abstract.", null, null, _hx_anonymous(array("fileName" => "Function.hx", "lineNumber" => 121, "className" => "metahub.code.functions.Function", "methodName" => "reverse"))));
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
