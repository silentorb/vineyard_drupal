<?php

class metahub_Hub {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->history = new metahub_History();
		$this->node_factories = new _hx_array(array());
		$this->internal_nodes = new _hx_array(array());
		$this->nodes = new _hx_array(array());
		$this->nodes->push(null);
		$this->node_factories->push(array(new _hx_lambda(array(), "metahub_Hub_0"), 'execute'));
		$this->root_scope_definition = new metahub_code_Scope_Definition(null, $this);
		$this->root_scope = new metahub_code_Scope($this, $this->root_scope_definition, null);
		$this->schema = new metahub_schema_Schema();
		$this->metahub_namespace = $this->schema->add_namespace("metahub");
		$this->load_internal_trellises();
		$this->function_library = new metahub_code_functions_Function_Library($this);
	}}
	public $nodes;
	public $internal_nodes;
	public $schema;
	public $root_scope;
	public $root_scope_definition;
	public $parser_definition;
	public $metahub_namespace;
	public $node_factories;
	public $function_library;
	public $history;
	public function load_parser() {
		$boot_definition = new metahub_parser_Definition();
		$boot_definition->load_parser_schema();
		$context = new metahub_parser_Bootstrap($boot_definition);
		$result = $context->parse("start = trim @(statement, newlines, 0, 0) final_trim\x0D\x0A\x0D\x0Anone = /&*/\x0D\x0Aws = /\\s+/\x0D\x0Atrim = /\\s*/\x0D\x0Afinal_trim = /\\s*/\x0D\x0Anewlines = /(\\s*\\n)+\\s*/\x0D\x0Acomma_or_newline = /\\s*((\\s*\\n)+|,)\\s*/\x0D\x0Adot = \".\"\x0D\x0A\x0D\x0Aid = /[a-zA-Z0-9_]+/\x0D\x0A\x0D\x0Apath = @(id, dot, 2, 0)\x0D\x0A\x0D\x0Apath_or_id = @(id, dot, 1, 0)\x0D\x0A\x0D\x0Areference = path_or_id @(method, none, 0, 0)\x0D\x0A\x0D\x0Amethod = \"|\" id\x0D\x0A\x0D\x0Astatement =\x0D\x0A    create_symbol\x0D\x0A  | set_values\x0D\x0A  | trellis_scope\x0D\x0A  | create_constraint\x0D\x0A\x09| create_node\x0D\x0A\x0D\x0Acreate_symbol = \"let\" ws id trim \"=\" trim expression\x0D\x0A\x0D\x0Acreate_constraint = path trim \"=\" trim expression\x0D\x0A\x0D\x0Aexpression =\x0D\x0A    @(expression_part, operation_separator, 1, 0)\x0D\x0A\x0D\x0Aoperation_separator = trim operator trim\x0D\x0A\x0D\x0Aexpression_part =\x0D\x0A    value\x0D\x0A  | create_node\x0D\x0A  | reference\x0D\x0A\x0D\x0Astring = ('\"' /[^\"]*/ '\"') | (\"'\" /[^']*/ \"'\")\x0D\x0Abool = \"true\" | \"false\"\x0D\x0Aint = /-?[0-9]+/\x0D\x0Afloat = /-?([0-9]*\\.)?[0-9]+f?/\x0D\x0Aoperator = '+' | '-' | '/' | '*' | '%'\x0D\x0Aconstraint_operator = '=' | '<' | '>' | '<=' | '>='\x0D\x0A\x0D\x0Avalue = string | bool | int | float\x0D\x0A\x0D\x0Adummy = \"@&^%\"\x0D\x0A\x0D\x0Acreate_node = \"new\" ws path_or_id trim @(set_property_block, dummy, 0, 1)\x0D\x0A\x0D\x0Aset_property_block = \"{\" trim @(set_property, comma_or_newline, 1, 0) trim \"}\"\x0D\x0A\x0D\x0Aset_property = id trim \":\" trim expression\x0D\x0A\x0D\x0Aset_values = \"set\" ws path_or_id trim set_property_block\x0D\x0A\x0D\x0Atrellis_scope = path_or_id trim constraint_block\x0D\x0A\x0D\x0Aconstraint_block = \"(\" trim @(constraint, comma_or_newline, 1, 0) trim \")\"\x0D\x0A\x0D\x0Aconstraint = id trim constraint_operator trim expression", false);
		$this->parser_definition = new metahub_parser_Definition();
		$this->parser_definition->load($result->get_data());
	}
	public function create_node($trellis) {
		$node = null;
		{
			$_g = 0;
			$_g1 = $this->node_factories;
			while($_g < $_g1->length) {
				$factory = $_g1[$_g];
				++$_g;
				$node = call_user_func_array($factory, array($this, $this->nodes->length, $trellis));
				if($node !== null) {
					break;
				}
				unset($factory);
			}
		}
		if($node === null) {
			throw new HException(new HException("Could not find valid factory to create node of type " . _hx_string_or_null($trellis->name) . ".", null, null, _hx_anonymous(array("fileName" => "Hub.hx", "lineNumber" => 68, "className" => "metahub.Hub", "methodName" => "create_node"))));
		}
		$this->add_node($node);
		return $node;
	}
	public function add_node($node) {
		$this->nodes->push($node);
	}
	public function add_internal_node($node) {
		$this->internal_nodes->push($node);
	}
	public function get_node($id) {
		if($id < 0 || $id >= $this->nodes->length) {
			throw new HException(new HException("There is no node with an id of " . _hx_string_rec($id, "") . ".", null, null, _hx_anonymous(array("fileName" => "Hub.hx", "lineNumber" => 84, "className" => "metahub.Hub", "methodName" => "get_node"))));
		}
		return $this->nodes[$id];
	}
	public function get_node_count() {
		return $this->nodes->length - 1;
	}
	public function load_schema_from_file($url, $namespace, $auto_identity = null) {
		if($auto_identity === null) {
			$auto_identity = false;
		}
		$data = metahub_Utility::load_json($url);
		$this->schema->load_trellises($data->trellises, new metahub_schema_Load_Settings($namespace, $auto_identity));
	}
	public function load_schema_from_string($json, $namespace, $auto_identity = null) {
		if($auto_identity === null) {
			$auto_identity = false;
		}
		$data = haxe_Json::phpJsonDecode($json);
		$this->schema->load_trellises($data->trellises, new metahub_schema_Load_Settings($namespace, $auto_identity));
	}
	public function load_schema_from_object($data, $namespace, $auto_identity = null) {
		if($auto_identity === null) {
			$auto_identity = false;
		}
		$this->schema->load_trellises($data->trellises, new metahub_schema_Load_Settings($namespace, $auto_identity));
	}
	public function run_data($source) {
		$coder = new metahub_code_Coder($this);
		return $coder->convert($source, $this->root_scope_definition, null);
	}
	public function run_code($code) {
		$result = $this->parse_code($code);
		if(!$result->success) {
			throw new HException(new HException("Error parsing code.", null, null, _hx_anonymous(array("fileName" => "Hub.hx", "lineNumber" => 115, "className" => "metahub.Hub", "methodName" => "run_code"))));
		}
		$match = $result;
		$expression = $this->run_data($match->get_data());
		$expression->resolve($this->root_scope);
	}
	public function parse_code($code) {
		if($this->parser_definition === null) {
			$this->load_parser();
		}
		$context = new metahub_parser_MetaHub_Context($this->parser_definition);
		$without_comments = metahub_Hub::$remove_comments->replace($code, "");
		return $context->parse($without_comments, null);
	}
	public function load_internal_trellises() {
		$functions = "{\x0D\x0A  \"trellises\": {\x0D\x0A    \"string\": {\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"string\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A    },\x0D\x0A    \"int\": {\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"int\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A\x09\x09},\x0D\x0A\x09\x09\"float\": {\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"float\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A    },\x0D\x0A    \"function\": {},\x0D\x0A\x09\x09\"int_operation\": {\x0D\x0A      \"parent\": \"function\",\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        },\x0D\x0A        \"input\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A    },\x0D\x0A\x09\x09\"int_single\": {\x0D\x0A      \"parent\": \"function\",\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        },\x0D\x0A        \"input\": {\x0D\x0A          \"type\": \"int\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A    },\x0D\x0A    \"Add_Int\": {\x0D\x0A      \"parent\": \"int_operation\"\x0D\x0A    },\x0D\x0A    \"subtract\": {\x0D\x0A      \"parent\": \"function\",\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        },\x0D\x0A        \"input\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A    },\x0D\x0A    \"Count\": {\x0D\x0A      \"parent\": \"function\",\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"any\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        },\x0D\x0A        \"input\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A    },\x0D\x0A\x09\x09\"Greater_Than_Int\": {\x0D\x0A      \"parent\": \"int_single\"\x0D\x0A    },\x0D\x0A\x09\x09\"Lesser_Than_Int\": {\x0D\x0A      \"parent\": \"int_single\"\x0D\x0A    },\x0D\x0A\x09\x09\"Subtract_Int\": {\x0D\x0A      \"parent\": \"int_single\"\x0D\x0A    }\x0D\x0A  }\x0D\x0A}";
		$data = haxe_Json::phpJsonDecode($functions);
		$this->schema->load_trellises($data->trellises, new metahub_schema_Load_Settings($this->metahub_namespace, null));
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
	static $remove_comments;
	function __toString() { return 'metahub.Hub'; }
}
metahub_Hub::$remove_comments = new EReg("#[^\x0A]*", "g");
function metahub_Hub_0($hub, $id, $trellis) {
	{
		return new metahub_engine_Node($hub, $id, $trellis);
	}
}
