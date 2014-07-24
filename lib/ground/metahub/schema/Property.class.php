<?php

class metahub_schema_Property {
	public function __construct($name, $source, $trellis) {
		if(!php_Boot::$skip_constructor) {
		$this->multiple = false;
		$this->type = Reflect::field(_hx_qtype("metahub.schema._Kind.Kind_Impl_"), $source->type);
		if(_hx_field($source, "default_value") !== null) {
			$this->default_value = $source->default_value;
		}
		if($source->allow_null !== null) {
			$this->allow_null = $source->allow_null;
		}
		if($source->multiple !== null) {
			$this->multiple = $source->multiple;
		}
		$this->name = $name;
		$this->trellis = $trellis;
	}}
	public $name;
	public $type;
	public $default_value;
	public $allow_null;
	public $trellis;
	public $id;
	public $other_trellis;
	public $other_property;
	public $multiple;
	public $port;
	public function fullname() {
		return _hx_string_or_null($this->trellis->name) . "." . _hx_string_or_null($this->name);
	}
	public function get_default() {
		if(_hx_field($this, "default_value") !== null) {
			return $this->default_value;
		}
		{
			$_g = $this->type;
			switch($_g) {
			case 1:{
				return 0;
			}break;
			case 5:{
				return 0;
			}break;
			case 2:{
				return "";
			}break;
			case 6:{
				return false;
			}break;
			default:{
				return null;
			}break;
			}
		}
	}
	public function initialize_link($source) {
		$_g = $this;
		if($source->type !== "list" && $source->type !== "reference") {
			return;
		}
		$this->other_trellis = $this->trellis->schema->get_trellis($source->trellis, $this->trellis->{"namespace"}, null);
		if($source->other_property !== null) {
			$this->other_property = $this->other_trellis->get_property($source->other_property);
		} else {
			$other_properties = Lambda::filter($this->other_trellis->properties, array(new _hx_lambda(array(&$_g, &$source), "metahub_schema_Property_0"), 'execute'));
			if($other_properties->length > 1) {
				throw new HException(new HException("Multiple ambiguous other properties for " . _hx_string_or_null($this->trellis->name) . "." . _hx_string_or_null($this->name) . ".", null, null, _hx_anonymous(array("fileName" => "Property.hx", "lineNumber" => 88, "className" => "metahub.schema.Property", "methodName" => "initialize_link"))));
			} else {
				if($other_properties->length === 1) {
					$this->other_property = $other_properties->first();
					$this->other_property->other_trellis = $this->trellis;
					$this->other_property->other_property = $this;
				}
			}
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
	function __toString() { return 'metahub.schema.Property'; }
}
function metahub_schema_Property_0(&$_g, &$source, $p) {
	{
		return (is_object($_t = $p->other_trellis) && !($_t instanceof Enum) ? $_t === $_g->trellis : $_t == $_g->trellis);
	}
}
