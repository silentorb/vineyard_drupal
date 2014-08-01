<?php

class metahub_schema_Trellis implements metahub_engine_INode{
	public function __construct($name, $schema, $namespace) {
		if(!php_Boot::$skip_constructor) {
		$this->copy = false;
		$this->properties = new _hx_array(array());
		$this->ports = new _hx_array(array());
		$this->property_keys = new haxe_ds_StringMap();
		$this->core_properties = new _hx_array(array());
		$this->name = $name;
		$this->schema = $schema;
		$this->{"namespace"} = $namespace;
		{
			$namespace->trellises->set($name, $this);
			$this;
		}
	}}
	public $name;
	public $schema;
	public $core_properties;
	public $all_properties;
	public $parent;
	public $id;
	public $identity_property;
	public $namespace;
	public $property_keys;
	public $ports;
	public $properties;
	public $copy;
	public function add_property($name, $source) {
		$property = new metahub_schema_Property($name, $source, $this);
		{
			$this->property_keys->set($name, $property);
			$property;
		}
		$property->id = $this->core_properties->length;
		$this->core_properties->push($property);
		return $property;
	}
	public function copy_identity($source, $target) {
		$identity_key = $this->identity_property->name;
		{
			$value = Reflect::field($source, $identity_key);
			$target->{$identity_key} = $value;
		}
	}
	public function get_all_properties() {
		$result = new haxe_ds_StringMap();
		$tree = $this->get_tree();
		{
			$_g = 0;
			while($_g < $tree->length) {
				$trellis = $tree[$_g];
				++$_g;
				{
					$_g1 = 0;
					$_g2 = $trellis->core_properties;
					while($_g1 < $_g2->length) {
						$property = $_g2[$_g1];
						++$_g1;
						{
							$result->set($property->name, $property);
							$property;
						}
						unset($property);
					}
					unset($_g2,$_g1);
				}
				unset($trellis);
			}
		}
		return $result;
	}
	public function get_identity($seed) {
		if($this->identity_property === null) {
			throw new HException(new HException("This trellis does not have an identity property set.", null, null, _hx_anonymous(array("fileName" => "Trellis.hx", "lineNumber" => 63, "className" => "metahub.schema.Trellis", "methodName" => "get_identity"))));
		}
		return Reflect::field($seed, $this->identity_property->name);
	}
	public function get_property($name) {
		$properties = $this->get_all_properties();
		if(!$properties->exists($name)) {
			throw new HException(new HException(_hx_string_or_null($this->name) . " does not contain a property named " . _hx_string_or_null($name) . ".", null, null, _hx_anonymous(array("fileName" => "Trellis.hx", "lineNumber" => 71, "className" => "metahub.schema.Trellis", "methodName" => "get_property"))));
		}
		return $properties->get($name);
	}
	public function get_property_or_null($name) {
		$properties = $this->get_all_properties();
		if(!$properties->exists($name)) {
			return null;
		}
		return $properties->get($name);
	}
	public function get_value($index, $context) {
		return $context->node->get_value($index);
	}
	public function set_value($index, $value, $context, $source = null) {
		if(!$context->node->trellis->is_a($this)) {
			throw new HException(new HException("Type mismatch: a " . _hx_string_or_null($context->node->trellis->name) . " node was passed to trellis " . _hx_string_or_null($this->name) . ".", null, null, _hx_anonymous(array("fileName" => "Trellis.hx", "lineNumber" => 90, "className" => "metahub.schema.Trellis", "methodName" => "set_value"))));
		}
		$context->node->set_value($index, $value, $source);
	}
	public function set_external_value($index, $value, $context, $source) {
		$port = $this->ports[$index];
		{
			$_g = 0;
			$_g1 = $port->connections;
			while($_g < $_g1->length) {
				$connection = $_g1[$_g];
				++$_g;
				if($connection === $source) {
					continue;
				}
				$connection->set_node_value($value, $context, $port);
				unset($connection);
			}
		}
	}
	public function get_tree() {
		$trellis = $this;
		$tree = new _hx_array(array());
		do {
			$tree->unshift($trellis);
			$trellis = $trellis->parent;
		} while($trellis !== null);
		return $tree;
	}
	public function is_a($trellis) {
		$current = $this;
		do {
			if($current === $trellis) {
				return true;
			}
			$current = $current->parent;
		} while($current !== null);
		return false;
	}
	public function load_properties($source) {
		$_g = 0;
		$_g1 = Reflect::fields($source->properties);
		while($_g < $_g1->length) {
			$name = $_g1[$_g];
			++$_g;
			$this->add_property($name, Reflect::field($source->properties, $name));
			unset($name);
		}
	}
	public function initialize1($source, $namespace) {
		$trellises = $this->schema->trellises;
		if($source->parent !== null) {
			$trellis = $this->schema->get_trellis($source->parent, $namespace, null);
			$this->set_parent($trellis);
		}
		if($source->primary_key !== null) {
			$primary_key = $source->primary_key;
			if($this->property_keys->exists($primary_key)) {
				$this->identity_property = $this->property_keys->get($primary_key);
			}
		} else {
			if($this->parent !== null) {
				$this->identity_property = $this->parent->identity_property;
			} else {
				if($this->property_keys->exists("id")) {
					$this->identity_property = $this->property_keys->get("id");
				}
			}
		}
		$tree = $this->get_tree();
		{
			$_g = 0;
			while($_g < $tree->length) {
				$trellis1 = $tree[$_g];
				++$_g;
				{
					$_g1 = 0;
					$_g2 = $trellis1->core_properties;
					while($_g1 < $_g2->length) {
						$property = $_g2[$_g1];
						++$_g1;
						$this->properties->push($property);
						$this->ports->push(new metahub_engine_General_Port($this, $this->ports->length));
						unset($property);
					}
					unset($_g2,$_g1);
				}
				unset($trellis1);
			}
		}
	}
	public function initialize2($source) {
		if(_hx_has_field($source, "copy")) {
			$this->copy = $source->copy;
		} else {
			if($this->parent !== null) {
				$this->copy = $this->parent->copy;
			}
		}
		if($source->properties !== null) {
			$_g = 0;
			$_g1 = Reflect::fields($source->properties);
			while($_g < $_g1->length) {
				$j = $_g1[$_g];
				++$_g;
				$property = $this->get_property($j);
				$property->initialize_link(Reflect::field($source->properties, $j));
				unset($property,$j);
			}
		}
	}
	public function set_parent($parent) {
		$this->parent = $parent;
	}
	public function get_port($index) {
		return $this->ports[$index];
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
	function __toString() { return 'metahub.schema.Trellis'; }
}
