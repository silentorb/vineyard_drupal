<?php

class metahub_engine_Port implements metahub_engine_IPort{
	public function __construct($node, $hub, $property, $value = null) {
		if(!php_Boot::$skip_constructor) {
		$this->on_change = new _hx_array(array());
		$this->connections = new _hx_array(array());
		$this->parent = $node;
		$this->hub = $hub;
		$this->property = $property;
		$this->_value = $value;
	}}
	public $_value;
	public $property;
	public $parent;
	public $connections;
	public $hub;
	public $on_change;
	public function connect($other) {
		$this->connections->push($other);
		$other->connections->push($this);
	}
	public function get_index() {
		return $this->property->id;
	}
	public function get_other_node() {
		$node_id = $this->_value;
		return $this->hub->get_node($node_id);
	}
	public function get_value($context) {
		return $this->_value;
	}
	public function set_value($new_value, $context) {
		if(!$this->property->multiple && _hx_equal($this->_value, $new_value)) {
			$this->hub->history->log("attempted " . _hx_string_or_null($this->property->fullname()) . "|set_value " . Std::string($new_value));
			return $this->_value;
		}
		{
			$_g = 0;
			$_g1 = $this->connections;
			while($_g < $_g1->length) {
				$connection = $_g1[$_g];
				++$_g;
				$new_value = $connection->set_value($new_value, $context);
				unset($connection);
			}
		}
		$old_value = $this->_value;
		$this->_value = $new_value;
		$this->hub->history->log(_hx_string_or_null($this->property->fullname()) . "|set_value " . Std::string($new_value));
		$new_value = $this->update_property_connections($new_value, $context);
		if(!$this->property->multiple && _hx_equal($old_value, $new_value)) {
			$this->_value = $new_value;
			return $this->_value;
		}
		if((is_object($_t = $this->property->type) && !($_t instanceof Enum) ? $_t === 3 : $_t == 3)) {
			if((is_object($_t2 = $this->property->other_property->type) && !($_t2 instanceof Enum) ? $_t2 === 4 : $_t2 == 4)) {
				$other_node = $this->get_other_node();
				$other_port = $other_node->get_port($this->property->other_property->id);
				$other_port->add_value($this->parent->id);
			} else {
			}
		}
		if($this->property->port !== null) {
			$this->update_property_connections($new_value, null);
		}
		if($this->on_change !== null && $this->on_change->length > 0) {
			$_g2 = 0;
			$_g11 = $this->on_change;
			while($_g2 < $_g11->length) {
				$action = $_g11[$_g2];
				++$_g2;
				call_user_func_array($action, array($this, $this->_value, $context));
				unset($action);
			}
		}
		return $this->_value;
	}
	public function get_type() {
		return $this->property->type;
	}
	public function update_connections($context) {
		$_g = 0;
		$_g1 = $this->connections;
		while($_g < $_g1->length) {
			$other = $_g1[$_g];
			++$_g;
			$other->set_value($this->_value, $context);
			unset($other);
		}
	}
	public function update_property_connections($new_value, $context) {
		if($this->property->port === null) {
			return $new_value;
		}
		$context1 = new metahub_engine_Context($this->parent, $this->hub);
		$new_value = $this->property->port->output($new_value, $context1);
		return $new_value;
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
	function __toString() { return 'metahub.engine.Port'; }
}
