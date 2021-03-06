<?php

class metahub_code_Coder {
	public function __construct($hub) {
		if(!php_Boot::$skip_constructor) {
		$this->hub = $hub;
	}}
	public $hub;
	public function convert($source, $scope_definition, $type = null) {
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
				return $this->function_expression($source, $scope_definition, $type);
			}break;
			case "set":{
				return $this->set($source, $scope_definition);
			}break;
			case "trellis_scope":{
				return $this->trellis_scope($source, $scope_definition);
			}break;
			}
		}
		throw new HException(new HException("Invalid block: " . Std::string($source->type), null, null, _hx_anonymous(array("fileName" => "Coder.hx", "lineNumber" => 45, "className" => "metahub.code.Coder", "methodName" => "convert"))));
	}
	public function constraint($source, $scope_definition) {
		if((is_object($_t = $scope_definition->_this->get_layer()) && !($_t instanceof Enum) ? $_t === metahub_code_Layer::$schema : $_t == metahub_code_Layer::$schema)) {
			$reference = $this->path_to_schema_reference($source->path, $scope_definition);
			$expression = $this->convert($source->expression, $scope_definition, $reference->get_type_reference());
			return new metahub_code_expressions_Create_Constraint($reference, $expression);
		} else {
			throw new HException(new HException("Not currently maintained.", null, null, _hx_anonymous(array("fileName" => "Coder.hx", "lineNumber" => 57, "className" => "metahub.code.Coder", "methodName" => "constraint"))));
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
				$block->expressions->push($this->convert($child, $new_scope_definition, null));
				unset($e,$child);
			}
		}
		return $block;
	}
	public function create_literal($source, $scope_definition) {
		$type = metahub_code_Coder::get_type($source->value);
		return new metahub_code_expressions_Literal($source->value, new metahub_code_Type_Reference($type, null));
	}
	public function get_namespace($path, $start) {
		$current_namespace = $start;
		$i = 0;
		{
			$_g = 0;
			while($_g < $path->length) {
				$token = $path[$_g];
				++$_g;
				if($current_namespace->children->exists($token)) {
					$current_namespace = $current_namespace->children->get($token);
				} else {
					if($current_namespace->trellises->exists($token) && $i === $path->length - 1) {
						return $current_namespace;
					} else {
						return null;
					}
				}
				++$i;
				unset($token);
			}
		}
		return $current_namespace;
	}
	public function create_node($source, $scope_definition) {
		$path = $source->trellis;
		if($path->length === 0) {
			throw new HException(new HException("Trellis path is empty for node creation.", null, null, _hx_anonymous(array("fileName" => "Coder.hx", "lineNumber" => 102, "className" => "metahub.code.Coder", "methodName" => "create_node"))));
		}
		$namespace = $this->get_namespace($path, $this->hub->schema->root_namespace);
		$trellis = $this->hub->schema->get_trellis($path[$path->length - 1], $namespace, true);
		$result = new metahub_code_expressions_Create_Node($trellis);
		if(_hx_field($source, "set") !== null) {
			$_g = 0;
			$_g1 = Reflect::fields($source->set);
			while($_g < $_g1->length) {
				$key = $_g1[$_g];
				++$_g;
				$property = $trellis->get_property($key);
				{
					$v = $this->convert(Reflect::field($source->set, $key), $scope_definition, null);
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
		$symbol = $scope_definition->find($path[0], $this->hub->schema->root_namespace);
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
		$symbol = $scope_definition->find($path[0], $this->hub->schema->root_namespace);
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
		$expression = $this->convert($source->expression, $scope_definition, null);
		$symbol = $scope_definition->add_symbol($source->name, $expression->type);
		return new metahub_code_expressions_Create_Symbol($symbol, $expression);
	}
	public function function_expression($source, $scope_definition, $type) {
		$_g = $this;
		if($type === null) {
			throw new HException(new HException("Function expressions do not currently support unspecified return types.", null, null, _hx_anonymous(array("fileName" => "Coder.hx", "lineNumber" => 180, "className" => "metahub.code.Coder", "methodName" => "function_expression"))));
		}
		$func = Type::createEnum(_hx_qtype("metahub.code.functions.Functions"), $source->name, null);
		$expressions = $source->inputs;
		$inputs = Lambda::harray(Lambda::map($expressions, array(new _hx_lambda(array(&$_g, &$expressions, &$func, &$scope_definition, &$source, &$type), "metahub_code_Coder_0"), 'execute')));
		return new metahub_code_expressions_Function_Call($func, $type, $inputs);
	}
	public function set($source, $scope_definition) {
		$reference = $scope_definition->find($source->path, $this->hub->schema->root_namespace);
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
				$result->add_assignment($property->id, $this->convert($assignment, $scope_definition, null));
				unset($property,$e,$assignment);
			}
		}
		return $result;
	}
	public function trellis_scope($source, $scope_definition) {
		$path = $source->path;
		if($path->length === 0) {
			throw new HException(new HException("Trellis path is empty for node creation.", null, null, _hx_anonymous(array("fileName" => "Coder.hx", "lineNumber" => 205, "className" => "metahub.code.Coder", "methodName" => "trellis_scope"))));
		}
		$namespace = $this->get_namespace($path, $this->hub->schema->root_namespace);
		$new_scope_definition = new metahub_code_Scope_Definition($scope_definition, null);
		$trellis = $this->hub->schema->get_trellis($path[$path->length - 1], $namespace, null);
		$new_scope_definition->_this = new metahub_code_symbols_Trellis_Symbol($trellis);
		$statements = new _hx_array(array());
		{
			$_g = 0;
			$_g1 = Reflect::fields($source->statements);
			while($_g < $_g1->length) {
				$i = $_g1[$_g];
				++$_g;
				$statement = Reflect::field($source->statements, $i);
				$statements->push($this->convert($statement, $new_scope_definition, null));
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
		throw new HException(new HException("Could not find type.", null, null, _hx_anonymous(array("fileName" => "Coder.hx", "lineNumber" => 175, "className" => "metahub.code.Coder", "methodName" => "get_type"))));
	}
	function __toString() { return 'metahub.code.Coder'; }
}
function metahub_code_Coder_0(&$_g, &$expressions, &$func, &$scope_definition, &$source, &$type, $e) {
	{
		return $_g->convert($e, $scope_definition, $type);
	}
}
