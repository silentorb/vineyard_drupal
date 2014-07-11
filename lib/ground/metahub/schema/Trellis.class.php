<?php

class metahub_schema_Trellis {
	public function __construct($name, $schema, $namespace) {
		if(!php_Boot::$skip_constructor) {
		$this->property_keys = new haxe_ds_StringMap();
		$this->properties = new _hx_array(array());
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
	public $properties;
	public $property_keys;
	public $parent;
	public $id;
	public $identity_property;
	public $namespace;
	public function add_property($name, $source) {
		$property = new metahub_schema_Property($name, $source, $this);
		{
			$this->property_keys->set($name, $property);
			$property;
		}
		$property->id = $this->properties->length;
		$this->properties->push($property);
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
					$_g2 = $trellis->properties;
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
			throw new HException(new HException("This trellis does not have an identity property set.", null, null, _hx_anonymous(array("fileName" => "Trellis.hx", "lineNumber" => 58, "className" => "metahub.schema.Trellis", "methodName" => "get_identity"))));
		}
		return Reflect::field($seed, $this->identity_property->name);
	}
	public function get_property($name) {
		$properties = $this->get_all_properties();
		if(!$properties->exists($name)) {
			throw new HException(new HException(_hx_string_or_null($this->name) . " does not contain a property named " . _hx_string_or_null($name) . ".", null, null, _hx_anonymous(array("fileName" => "Trellis.hx", "lineNumber" => 66, "className" => "metahub.schema.Trellis", "methodName" => "get_property"))));
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
	public function get_value($index) {
		throw new HException(new HException("Cannot get value of a trellis property.", null, null, _hx_anonymous(array("fileName" => "Trellis.hx", "lineNumber" => 80, "className" => "metahub.schema.Trellis", "methodName" => "get_value"))));
	}
	public function set_value($index, $value) {
		throw new HException(new HException("Cannot set value of a trellis property.", null, null, _hx_anonymous(array("fileName" => "Trellis.hx", "lineNumber" => 84, "className" => "metahub.schema.Trellis", "methodName" => "set_value"))));
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
	}
	public function initialize2($source) {
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
