<?php

class metahub_engine_General_Port {
	public function __construct($node, $id) {
		if(!php_Boot::$skip_constructor) {
		$this->connections = new _hx_array(array());
		$this->node = $node;
		$this->id = $id;
	}}
	public $connections;
	public $node;
	public $id;
	public function connect($port) {
		if($this->connections->indexOf($port, null) > -1) {
			return;
		}
		$this->connections->push($port);
		$port->connections->push($this);
	}
	public function get_node_value($context) {
		return $this->node->get_value($this->id, $context);
	}
	public function set_node_value($value, $context, $source) {
		$this->node->set_value($this->id, $value, $context, $source);
	}
	public function get_external_value($context) {
		if($this->connections->length > 1) {
			$result = new _hx_array(array());
			{
				$_g = 0;
				$_g1 = $this->connections;
				while($_g < $_g1->length) {
					$connection = $_g1[$_g];
					++$_g;
					$result->push($connection->get_node_value($context));
					unset($connection);
				}
			}
			return $result;
		}
		if($this->connections->length === 0) {
			throw new HException(new HException("Cannot get_external_value from an unconnected port.", null, null, _hx_anonymous(array("fileName" => "General_Port.hx", "lineNumber" => 53, "className" => "metahub.engine.General_Port", "methodName" => "get_external_value"))));
		}
		return _hx_array_get($this->connections, 0)->get_node_value($context);
	}
	public function set_external_value($value, $context) {
		$_g = 0;
		$_g1 = $this->connections;
		while($_g < $_g1->length) {
			$connection = $_g1[$_g];
			++$_g;
			$connection->set_node_value($value, $context, $this);
			unset($connection);
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
	function __toString() { return 'metahub.engine.General_Port'; }
}
