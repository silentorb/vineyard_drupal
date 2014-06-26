<?php

class metahub_schema_Schema {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->trellis_keys = new haxe_ds_StringMap();
		$this->trellises = new _hx_array(array());
	}}
	public $trellises;
	public $trellis_keys;
	public function add_trellis($name, $trellis) {
		{
			$this->trellis_keys->set($name, $trellis);
			$trellis;
		}
		$this->trellises->push($trellis);
		return $trellis;
	}
	public function load_trellises($trellises) {
		$trellis = null;
		$source = null;
		$name = null;
		{
			$_g = 0;
			$_g1 = Reflect::fields($trellises);
			while($_g < $_g1->length) {
				$name1 = $_g1[$_g];
				++$_g;
				$source = Reflect::field($trellises, $name1);
				$trellis = $this->trellis_keys->get($name1);
				if($trellis === null) {
					$trellis = $this->add_trellis($name1, new metahub_schema_Trellis($name1, $this));
				}
				$trellis->load_properties($source);
				unset($name1);
			}
		}
		{
			$_g2 = 0;
			$_g11 = Reflect::fields($trellises);
			while($_g2 < $_g11->length) {
				$name2 = $_g11[$_g2];
				++$_g2;
				$source = Reflect::field($trellises, $name2);
				$trellis = $this->trellis_keys->get($name2);
				$trellis->initialize1($source);
				unset($name2);
			}
		}
		{
			$_g3 = 0;
			$_g12 = Reflect::fields($trellises);
			while($_g3 < $_g12->length) {
				$name3 = $_g12[$_g3];
				++$_g3;
				$source = Reflect::field($trellises, $name3);
				$trellis = $this->trellis_keys->get($name3);
				$trellis->initialize2($source);
				unset($name3);
			}
		}
	}
	public function get_trellis($name) {
		if(!$this->trellis_keys->exists($name)) {
			throw new HException("Could not find trellis named: " . _hx_string_or_null($name) . ".");
		}
		return $this->trellis_keys->get($name);
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
	function __toString() { return 'metahub.schema.Schema'; }
}
