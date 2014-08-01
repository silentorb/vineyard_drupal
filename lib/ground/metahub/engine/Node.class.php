<?php

class metahub_engine_Node {
	public function __construct($hub, $id, $trellis) {
		if(!php_Boot::$skip_constructor) {
		$this->ports = new _hx_array(array());
		$this->values = new _hx_array(array());
		$this->hub = $hub;
		$this->id = $id;
		$this->trellis = $trellis;
		$properties = $trellis->get_all_properties();
		if(null == $properties) throw new HException('null iterable');
		$__hx__it = $properties->iterator();
		while($__hx__it->hasNext()) {
			$property = $__hx__it->next();
			$this->values->push($this->get_default_value($property));
			if($property === $trellis->identity_property) {
				continue;
			}
			$port = new metahub_engine_Port($this, $property->id);
			$this->ports->push($port);
			unset($port);
		}
		$this->initialize();
	}}
	public $hub;
	public $values;
	public $ports;
	public $id;
	public $trellis;
	public $port_count;
	public function initialize() {
	}
	public function get_port_count() {
		return $this->ports->length;
	}
	public function get_default_value($property) {
		$_g = $property->type;
		switch($_g) {
		case 4:{
			return new _hx_array(array());
		}break;
		case 1:{
			return 0;
		}break;
		case 5:{
			return 0.0;
		}break;
		case 3:{
			return 0;
		}break;
		case 2:{
			return "";
		}break;
		case 6:{
			return false;
		}break;
		default:{
			throw new HException(new HException("No default is implemented for type " . Std::string($property->type) . ".", null, null, _hx_anonymous(array("fileName" => "Node.hx", "lineNumber" => 67, "className" => "metahub.engine.Node", "methodName" => "get_default_value"))));
		}break;
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
	public function get_port_by_name($name) {
		$property = $this->trellis->get_property($name);
		return $this->get_port($property->id);
	}
	public function get_port_from_chain($chain) {
		if($chain->length === 0) {
			throw new HException(new HException("Cannot follow empty property chain.", null, null, _hx_anonymous(array("fileName" => "Node.hx", "lineNumber" => 98, "className" => "metahub.engine.Node", "methodName" => "get_port_from_chain"))));
		}
		$current_node = $this;
		$i = 0;
		{
			$_g = 0;
			while($_g < $chain->length) {
				$link = $chain[$_g];
				++$_g;
				if((is_object($_t = $link->type) && !($_t instanceof Enum) ? $_t === 3 : $_t == 3)) {
					$current_node = $this->hub->get_node($link->id);
				} else {
					if($i < $chain->length - 1) {
						throw new HException(new HException("Invalid chain. " . _hx_string_or_null($link->fullname()) . " is not a reference.", null, null, _hx_anonymous(array("fileName" => "Node.hx", "lineNumber" => 108, "className" => "metahub.engine.Node", "methodName" => "get_port_from_chain"))));
					}
					return $current_node->get_port($link->id);
				}
				++$i;
				unset($link,$_t);
			}
		}
		throw new HException(new HException("Could not follow chain", null, null, _hx_anonymous(array("fileName" => "Node.hx", "lineNumber" => 116, "className" => "metahub.engine.Node", "methodName" => "get_port_from_chain"))));
	}
	public function get_value($index) {
		return $this->values[$index];
	}
	public function get_value_by_name($name) {
		$property = $this->trellis->get_property($name);
		return $this->values[$property->id];
	}
	public function equals($first, $second, $property) {
		if($property->other_trellis !== null && $property->other_trellis->copy) {
			$first_node = $this->hub->get_node($first);
			$second_node = $this->hub->get_node($second);
			$properties = $property->trellis->properties;
			{
				$_g1 = 0;
				$_g = $properties->length;
				while($_g1 < $_g) {
					$i = $_g1++;
					if(!$this->equals($first_node->get_value($i), $second_node->get_value($i), $properties[$i])) {
						return false;
					}
					unset($i);
				}
			}
			return true;
		} else {
			return _hx_equal($first, $second);
		}
	}
	public function set_value($index, $value, $source = null) {
		$old_value = $this->values[$index];
		$port = $this->ports[$index];
		$property = $this->trellis->properties[$index];
		if((is_object($_t = $property->type) && !($_t instanceof Enum) ? $_t === 4 : $_t == 4)) {
			throw new HException(new HException(_hx_string_or_null($property->fullname()) . " is a list and cannot be directly assigned to.", null, null, _hx_anonymous(array("fileName" => "Node.hx", "lineNumber" => 149, "className" => "metahub.engine.Node", "methodName" => "set_value"))));
		}
		if($this->equals($old_value, $value, $property)) {
			$this->hub->history->log("attempted " . _hx_string_or_null($property->fullname()) . "|set_value " . Std::string($value));
			return;
		}
		$this->hub->set_entry_node($this);
		if($property->other_trellis !== null && $property->other_trellis->copy) {
			$original = $this->hub->get_node($value);
			$new_node = $original->hclone();
			$value = $new_node->id;
		}
		$this->values[$index] = $value;
		$this->hub->history->log(_hx_string_or_null($property->fullname()) . "|set_value " . Std::string($value));
		$context = new metahub_engine_Context($this, $this->hub);
		$tree = $this->trellis->get_tree();
		{
			$_g = 0;
			while($_g < $tree->length) {
				$t = $tree[$_g];
				++$_g;
				$t->set_external_value($index, $value, $context, $source);
				unset($t);
			}
		}
		if((is_object($_t2 = $property->type) && !($_t2 instanceof Enum) ? $_t2 === 3 : $_t2 == 3)) {
			if((is_object($_t3 = $property->other_property->type) && !($_t3 instanceof Enum) ? $_t3 === 4 : $_t3 == 4)) {
				$other_node = $this->hub->get_node($value);
				$other_node->add_item($property->other_property->id, $this->id);
			} else {
				throw new HException(new HException("Not implemented.", null, null, _hx_anonymous(array("fileName" => "Node.hx", "lineNumber" => 178, "className" => "metahub.engine.Node", "methodName" => "set_value"))));
			}
		}
		$this->hub->run_change_queue($this);
	}
	public function add_item($index, $value) {
		$port = $this->ports[$index];
		$property = $this->trellis->properties[$index];
		if((is_object($_t = $property->type) && !($_t instanceof Enum) ? $_t !== 4 : $_t != 4)) {
			throw new HException(new HException("Cannot add items to " . Std::string((isset($property->fullname) ? $property->fullname: array($property, "fullname"))) . " because it is not a list.", null, null, _hx_anonymous(array("fileName" => "Node.hx", "lineNumber" => 190, "className" => "metahub.engine.Node", "methodName" => "add_item"))));
		}
		$list = $this->values[$index];
		$list->push($value);
		$this->hub->history->log(_hx_string_or_null($property->fullname()) . "|add_item " . Std::string($value));
		$context = new metahub_engine_Context($this, $this->hub);
		$this->trellis->set_external_value($index, $value, $context, null);
		if((is_object($_t2 = $property->other_property->type) && !($_t2 instanceof Enum) ? $_t2 === 3 : $_t2 == 3)) {
			$other_node = $this->hub->get_node($value);
			if(!_hx_equal($other_node->get_value($property->other_property->id), $this->id)) {
				$other_node->set_value($property->other_property->id, $this->id, null);
			}
		} else {
			throw new HException(new HException("Not implemented.", null, null, _hx_anonymous(array("fileName" => "Node.hx", "lineNumber" => 205, "className" => "metahub.engine.Node", "methodName" => "add_item"))));
		}
	}
	public function hclone() {
		$result = $this->hub->create_node($this->trellis);
		{
			$_g1 = 0;
			$_g = $this->trellis->properties->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$result->set_value($i, $this->get_value($i), null);
				unset($i);
			}
		}
		return $result;
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
	static $__properties__ = array("get_port_count" => "get_port_count");
	function __toString() { return 'metahub.engine.Node'; }
}
