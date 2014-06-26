<?php

class metahub_engine_List_Port extends metahub_engine_Base_Port {
	public function __construct($node, $hub, $property, $value = null) { if(!php_Boot::$skip_constructor) {
		if($value === null) {
			$value = new _hx_array(array());
		}
		parent::__construct($node,$hub,$property,$value);
	}}
	public function get_array() {
		return $this->_value;
	}
	public function get_value_at($index) {
		return $this->_value[$index];
	}
	public function set_value_at($new_value, $index) {
		return $this->_value[$index] = $new_value;
	}
	public function add_value($new_value) {
		$this->_value->push($new_value);
		haxe_Log::trace("list changed.", _hx_anonymous(array("fileName" => "List_Port.hx", "lineNumber" => 31, "className" => "metahub.engine.List_Port", "methodName" => "add_value")));
		$this->update_property_dependents();
	}
	public function set_value($new_value, $context = null) {
		throw new HException("Not supported.");
	}
	function __toString() { return 'metahub.engine.List_Port'; }
}
