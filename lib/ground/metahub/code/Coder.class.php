<?php

class metahub_code_Coder {
	public function __construct($hub) {
		if(!php_Boot::$skip_constructor) {
		$this->hub = $hub;
	}}
	public $hub;
	public function convert($source, $scope_definition) {
		{
			$_g = $source->type;
			switch($_g) {
			case "block":{
				return $this->create_block($source, $scope_definition);
			}break;
			case "constraint":{
				return $this->constraint($source, $scope_definition);
			}break;
			case "node":{
				return $this->create_node($source, $scope_definition);
			}break;
			case "symbol":{
				return $this->create_symbol($source, $scope_definition);
			}break;
			case "literal":{
				return $this->create_literal($source, $scope_definition);
			}break;
			case "reference":{
				return $this->create_reference($source, $scope_definition);
			}break;
			case "function":{
				return $this->function_expression($source, $scope_definition);
			}break;
			case "set":{
				return $this->set($source, $scope_definition);
			}break;
			case "trellis_scope":{
				return $this->trellis_scope($source, $scope_definition);
			}break;
			}
		}
		throw new HException("Invalid block: " . Std::string($source->type));
	}
	public function constraint($source, $scope_definition) {
		$expression = $this->convert($source->expression, $scope_definition);
		if((is_object($_t = $scope_definition->_this->get_layer()) && !($_t instanceof Enum) ? $_t === metahub_code_Layer::$schema : $_t == metahub_code_Layer::$schema)) {
			$reference = $this->path_to_schema_reference($source->path, $scope_definition);
			return new metahub_code_expressions_Create_Constraint($reference, $expression);
		} else {
			$reference1 = $this->path_to_engine_reference($source->path, $scope_definition);
			return new metahub_code_expressions_Create_Constraint($reference1, $expression);
		}
	}
	public function create_block($source, $scope_definition) {
		$new_scope_definition = new metahub_code_Scope_Definition($scope_definition, null);
		$block = new metahub_code_expressions_Block($new_scope_definition);
		{
			$_g = 0;
			$_g1 = Reflect::fields($source->expressions);
			while($_g < $_g1->length) {
				$e = $_g1[$_g];
				++$_g;
				$child = Reflect::field($source->expressions, $e);
				$block->expressions->push($this->convert($child, $new_scope_definition));
				unset($e,$child);
			}
		}
		return $block;
	}
	public function create_literal($source, $scope_definition) {
		$type = metahub_code_Coder::get_type($source->value);
		return new metahub_code_expressions_Literal($source->value, new metahub_code_Type_Reference($type, null));
	}
	public function create_node($source, $scope_definition) {
		$trellis = $this->hub->schema->get_trellis($source->trellis);
		$result = new metahub_code_expressions_Create_Node($trellis);
		if(_hx_field($source, "set") !== null) {
			$_g = 0;
			$_g1 = Reflect::fields($source->set);
			while($_g < $_g1->length) {
				$key = $_g1[$_g];
				++$_g;
				$property = $trellis->get_property($key);
				{
					$v = $this->convert(Reflect::field($source->set, $key), $scope_definition);
					$result->assignments->set($property->id, $v);
					$v;
					unset($v);
				}
				unset($property,$key);
			}
		}
		return $result;
	}
	public function path_to_engine_reference($path, $scope_definition) {
		$symbol = $scope_definition->find($path[0]);
		return $symbol->create_reference($this->extract_path($path));
	}
	public function extract_path($path) {
		$result = new _hx_array(array());
		{
			$_g1 = 1;
			$_g = _hx_len($path);
			while($_g1 < $_g) {
				$i = $_g1++;
				$result->push($path[$i]);
				unset($i);
			}
		}
		return $result;
	}
	public function path_to_schema_reference($path, $scope_definition) {
		$symbol = $scope_definition->find($path[0]);
		return $symbol->create_reference($this->extract_path($path));
	}
	public function create_reference($source, $scope_definition) {
		if($scope_definition->_this !== null && (is_object($_t = $scope_definition->_this->get_layer()) && !($_t instanceof Enum) ? $_t === metahub_code_Layer::$schema : $_t == metahub_code_Layer::$schema)) {
			$reference = $this->path_to_schema_reference($source->path, $scope_definition);
			return new metahub_code_expressions_Expression_Reference($reference);
		} else {
			$reference1 = $this->path_to_engine_reference($source->path, $scope_definition);
			return new metahub_code_expressions_Expression_Reference($reference1);
		}
	}
	public function create_symbol($source, $scope_definition) {
		$expression = $this->convert($source->expression, $scope_definition);
		$symbol = $scope_definition->add_symbol($source->name, $expression->type);
		return new metahub_code_expressions_Create_Symbol($symbol, $expression);
	}
	public function function_expression($source, $scope_definition) {
		$_g = $this;
		$trellis = $this->hub->schema->get_trellis($source->name);
		$expressions = $source->inputs;
		$inputs = Lambda::harray(Lambda::map($expressions, array(new _hx_lambda(array(&$_g, &$expressions, &$scope_definition, &$source, &$trellis), "metahub_code_Coder_0"), 'execute')));
		return new metahub_code_expressions_Function_Call($trellis, $inputs);
	}
	public function set($source, $scope_definition) {
		$reference = $scope_definition->find($source->path);
		$trellis = $reference->get_trellis();
		$result = new metahub_code_expressions_Set($reference);
		{
			$_g = 0;
			$_g1 = Reflect::fields($source->assignments);
			while($_g < $_g1->length) {
				$e = $_g1[$_g];
				++$_g;
				$assignment = Reflect::field($source->assignments, $e);
				$property = $trellis->get_property($e);
				$result->add_assignment($property->id, $this->convert($assignment, $scope_definition));
				unset($property,$e,$assignment);
			}
		}
		return $result;
	}
	public function trellis_scope($source, $scope_definition) {
		$new_scope_definition = new metahub_code_Scope_Definition($scope_definition, null);
		$trellis = $this->hub->schema->get_trellis($source->path);
		$new_scope_definition->_this = new metahub_code_symbols_Trellis_Symbol($trellis);
		$statements = new _hx_array(array());
		{
			$_g = 0;
			$_g1 = Reflect::fields($source->statements);
			while($_g < $_g1->length) {
				$i = $_g1[$_g];
				++$_g;
				$statement = Reflect::field($source->statements, $i);
				$statements->push($this->convert($statement, $new_scope_definition));
				unset($statement,$i);
			}
		}
		return new metahub_code_expressions_Trellis_Scope($trellis, $statements, $new_scope_definition);
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
	static function get_type($value) {
		if(Std::is($value, _hx_qtype("Int"))) {
			return 1;
		}
		if(Std::is($value, _hx_qtype("Float"))) {
			return 5;
		}
		if(Std::is($value, _hx_qtype("Bool"))) {
			return 6;
		}
		if(Std::is($value, _hx_qtype("String"))) {
			return 2;
		}
		throw new HException("Could not find type.");
	}
	function __toString() { return 'metahub.code.Coder'; }
}
function metahub_code_Coder_0(&$_g, &$expressions, &$scope_definition, &$source, &$trellis, $e) {
	{
		return $_g->convert($e, $scope_definition);
	}
}
