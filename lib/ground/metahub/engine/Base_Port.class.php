<?php

class metahub_engine_Base_Port implements metahub_engine_IPort{
	public function __construct($node, $hub, $property, $value = null) {
		if(!php_Boot::$skip_constructor) {
		$this->on_change = new _hx_array(array());
		$this->dependents = new _hx_array(array());
		$this->dependencies = new _hx_array(array());
		$this->parent = $node;
		$this->hub = $hub;
		$this->property = $property;
		$this->_value = $value;
	}}
	public $_value;
	public $property;
	public $parent;
	public $dependencies;
	public $dependents;
	public $hub;
	public $on_change;
	public function add_dependency($other, $operator) {
		$relationship = new metahub_engine_Relationship($this, $operator, $other);
		$this->dependencies->push($relationship);
		$other->dependents->push($relationship);
	}
	public function get_index() {
		return $this->property->id;
	}
	public function get_other_node() {
		$node_id = $this->_value;
		return $this->hub->get_node($node_id);
	}
	public function get_value($context = null) {
		return $this->_value;
	}
	public function set_value($new_value, $context = null) {
		if(!$this->property->multiple && _hx_equal($this->_value, $new_value)) {
			return $this->_value;
		}
		{
			$_g = 0;
			$_g1 = $this->dependencies;
			while($_g < $_g1->length) {
				$relationship = $_g1[$_g];
				++$_g;
				$new_value = $relationship->check_value($new_value, $context);
				unset($relationship);
			}
		}
		$new_value = $this->check_property_dependencies($new_value, $context);
		if(!$this->property->multiple && _hx_equal($this->_value, $new_value)) {
			return $this->_value;
		}
		$this->_value = $new_value;
		if((is_object($_t = $this->property->type) && !($_t instanceof Enum) ? $_t === 3 : $_t == 3)) {
			if((is_object($_t2 = $this->property->other_property->type) && !($_t2 instanceof Enum) ? $_t2 === 4 : $_t2 == 4)) {
				$other_node = $this->get_other_node();
				$other_port = $other_node->get_port($this->property->other_property->id);
				$other_port->add_value($this->parent->id);
			} else {
			}
		}
		if($this->dependents !== null && $this->dependents->length > 0) {
			$this->update_dependents($context);
		}
		if($this->property->ports !== null && $this->property->ports->length > 0) {
			$this->update_property_dependents();
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
	public function update_dependents($context) {
		$_g = 0;
		$_g1 = $this->dependents;
		while($_g < $_g1->length) {
			$other = $_g1[$_g];
			++$_g;
			$other->set_value($this->_value, $context);
			unset($other);
		}
	}
	public function update_property_dependents() {
		$_g = 0;
		$_g1 = $this->property->ports;
		while($_g < $_g1->length) {
			$port = $_g1[$_g];
			++$_g;
			$context = new metahub_engine_Context($port, $this->parent);
			$port->enter($this->_value, $context);
			unset($port,$context);
		}
	}
	public function check_property_dependencies($new_value, $context) {
		{
			$_g = 0;
			$_g1 = $this->property->ports;
			while($_g < $_g1->length) {
				$port = $_g1[$_g];
				++$_g;
				$context1 = new metahub_engine_Context($port, $this->parent);
				{
					$_g2 = 0;
					$_g3 = $port->dependencies;
					while($_g2 < $_g3->length) {
						$relationship = $_g3[$_g2];
						++$_g2;
						$new_value = $relationship->check_value($new_value, $context1);
						unset($relationship);
					}
					unset($_g3,$_g2);
				}
				unset($port,$context1);
			}
		}
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
	function __toString() { return 'metahub.engine.Base_Port'; }
}
