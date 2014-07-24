<?php

class metahub_engine_Signal_Port implements metahub_engine_IPort{
	public function __construct($kind, $getter, $multiple = null) {
		if(!php_Boot::$skip_constructor) {
		if($multiple === null) {
			$multiple = false;
		}
		$this->on_change = new _hx_array(array());
		$this->connections = new _hx_array(array());
		$this->kind = $kind;
		$this->getter = $getter;
		$this->multiple = $multiple;
	}}
	public $connections;
	public $on_change;
	public $multiple;
	public $kind;
	public $getter;
	public function connect($other) {
		$this->connections->push($other);
		$other->connections->push($this);
	}
	public function get_value($context) {
		return $this->getter($context);
	}
	public function get_external_value($context) {
		if($this->multiple) {
			return $this->connections->map(array(new _hx_lambda(array(&$context), "metahub_engine_Signal_Port_0"), 'execute'));
		} else {
			return _hx_array_get($this->connections, 0)->get_value($context);
		}
	}
	public function set_value($new_value, $context) {
		if($this->on_change !== null && $this->on_change->length > 0) {
			$_g = 0;
			$_g1 = $this->on_change;
			while($_g < $_g1->length) {
				$action = $_g1[$_g];
				++$_g;
				call_user_func_array($action, array($this, $new_value, $context));
				unset($action);
			}
		}
		return $new_value;
	}
	public function output($new_value, $context) {
		$_g = 0;
		$_g1 = $this->connections;
		while($_g < $_g1->length) {
			$connection = $_g1[$_g];
			++$_g;
			$new_value = $connection->set_value($new_value, $context);
			unset($connection);
		}
	}
	public function get_type() {
		return $this->kind;
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
	function __toString() { return 'metahub.engine.Signal_Port'; }
}
function metahub_engine_Signal_Port_0(&$context, $d) {
	{
		return $d->get_value($context);
	}
}
