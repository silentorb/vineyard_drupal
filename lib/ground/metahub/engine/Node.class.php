<?php

class metahub_engine_Node implements metahub_engine_INode{
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
			$port = null;
			if((is_object($_t = $property->type) && !($_t instanceof Enum) ? $_t === 4 : $_t == 4)) {
				$port = new metahub_engine_List_Port($this, $hub, $property, null);
			} else {
				$port = new metahub_engine_Port($this, $hub, $property, $property->get_default());
			}
			$this->ports->push($port);
			unset($port,$_t);
		}
		$this->initialize();
	}}
	public $hub;
	public $ports;
	public $id;
	public $trellis;
	public $port_count;
	public function get_port_count() {
		return $this->ports->length;
	}
	public function initialize() {
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
			throw new HException(new HException("Cannot follow empty property chain.", null, null, _hx_anonymous(array("fileName" => "Node.hx", "lineNumber" => 73, "className" => "metahub.engine.Node", "methodName" => "get_port_from_chain"))));
		}
		$current_node = $this;
		$i = 0;
		{
			$_g = 0;
			while($_g < $chain->length) {
				$link = $chain[$_g];
				++$_g;
				$port = $current_node->get_port($link->id);
				if((is_object($_t = $link->type) && !($_t instanceof Enum) ? $_t === 3 : $_t == 3)) {
					$reference = $port;
					$current_node = $reference->get_other_node();
					unset($reference);
				} else {
					if($i < $chain->length - 1) {
						throw new HException(new HException("Invalid chain. " . _hx_string_or_null($link->fullname()) . " is not a reference.", null, null, _hx_anonymous(array("fileName" => "Node.hx", "lineNumber" => 85, "className" => "metahub.engine.Node", "methodName" => "get_port_from_chain"))));
					}
					return $current_node->get_port($link->id);
				}
				++$i;
				unset($port,$link,$_t);
			}
		}
		throw new HException(new HException("Could not follow chain", null, null, _hx_anonymous(array("fileName" => "Node.hx", "lineNumber" => 93, "className" => "metahub.engine.Node", "methodName" => "get_port_from_chain"))));
	}
	public function get_value($index) {
		$port = $this->get_port($index);
		return $port->get_value(null);
	}
	public function get_value_by_name($name) {
		$property = $this->trellis->get_property($name);
		$port = $this->ports[$property->id];
		return $port->get_value(null);
	}
	public function set_value($index, $value) {
		$port = $this->ports[$index];
		$port->set_value($value, null);
	}
	public function get_input_values($context) {
		$result = new _hx_array(array());
		{
			$_g1 = 1;
			$_g = $this->ports->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$port = $this->get_port($i);
				$value = null;
				if($port->property->multiple) {
					$value = $port->connections->map(array(new _hx_lambda(array(&$_g, &$_g1, &$context, &$i, &$port, &$result, &$value), "metahub_engine_Node_0"), 'execute'));
				} else {
					$value = _hx_array_get($port->connections, 0)->get_value($context);
				}
				$result->push($value);
				unset($value,$port,$i);
			}
		}
		return $result;
	}
	public function add_list_value($index, $value) {
		$port = $this->ports[$index];
		$port->add_value($value);
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
function metahub_engine_Node_0(&$_g, &$_g1, &$context, &$i, &$port, &$result, &$value, $d) {
	{
		return $d->get_value($context);
	}
}
