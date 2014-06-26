<?php

class metahub_code_Scope_Definition {
	public function __construct($parent = null, $hub = null) {
		if(!php_Boot::$skip_constructor) {
		$this->depth = 0;
		$this->symbols = new haxe_ds_StringMap();
		$this->types = new _hx_array(array());
		$this->parent = $parent;
		if($parent !== null) {
			$this->hub = $parent->hub;
			$this->depth = $parent->depth + 1;
			$this->_this = $parent->_this;
		} else {
			$this->hub = $hub;
		}
	}}
	public $parent;
	public $types;
	public $symbols;
	public $depth;
	public $_this;
	public $hub;
	public function add_symbol($name, $type) {
		$symbol = new metahub_code_symbols_Local_Symbol($type, $this, $this->types->length, $name);
		$this->types->push($symbol);
		{
			$this->symbols->set($name, $symbol);
			$symbol;
		}
		return $symbol;
	}
	public function _find($name) {
		if($this->symbols->exists($name)) {
			return $this->symbols->get($name);
		}
		if($this->parent === null) {
			return null;
		}
		return $this->parent->_find($name);
	}
	public function find($name) {
		if($this->symbols->exists($name)) {
			return $this->symbols->get($name);
		}
		$result = null;
		if($this->parent !== null) {
			$result = $this->parent->_find($name);
		}
		if($result === null) {
			if($this->_this !== null) {
				return $this->_this->get_context_symbol($name);
			}
		}
		if($result === null && $this->hub->schema->trellis_keys->exists($name)) {
			$result = new metahub_code_symbols_Trellis_Symbol($this->hub->schema->trellis_keys->get($name));
		}
		if($result === null) {
			throw new HException("Could not find symbol: " . _hx_string_or_null($name) . ".");
		}
		return $result;
	}
	public function get_symbol_by_name($name) {
		return $this->symbols->get($name);
	}
	public function get_symbol_by_index($index) {
		return $this->types[$index];
	}
	public function size() {
		return $this->types->length;
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
	function __toString() { return 'metahub.code.Scope_Definition'; }
}
