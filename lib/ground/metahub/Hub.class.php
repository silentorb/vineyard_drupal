<?php

class metahub_Hub {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->nodes = new _hx_array(array());
		$this->nodes->push(null);
		$this->root_scope_definition = new metahub_code_Scope_Definition(null, $this);
		$this->root_scope = new metahub_code_Scope($this, $this->root_scope_definition, null);
		$this->schema = new metahub_schema_Schema();
		$this->metahub_namespace = $this->schema->add_namespace("metahub");
		$this->create_functions();
	}}
	public $nodes;
	public $schema;
	public $root_scope;
	public $root_scope_definition;
	public $parser_definition;
	public $metahub_namespace;
	public function load_parser() {
		$boot_definition = new metahub_parser_Definition();
		$boot_definition->load_parser_schema();
		$context = new metahub_parser_Bootstrap($boot_definition);
		$result = $context->parse("start = trim @(statement, newlines, 0, 0) final_trim\x0D\x0A\x0D\x0Anone = /&*/\x0D\x0Aws = /\\s+/\x0D\x0Atrim = /\\s*/\x0D\x0Afinal_trim = /\\s*/\x0D\x0Anewlines = /(\\s*\\n)+\\s*/\x0D\x0Acomma_or_newline = /\\s*((\\s*\\n)+|,)\\s*/\x0D\x0Adot = \".\"\x0D\x0A\x0D\x0Aid = /[a-zA-Z0-9_]+/\x0D\x0A\x0D\x0Apath = @(id, dot, 2, 0)\x0D\x0A\x0D\x0Apath_or_id = @(id, dot, 1, 0)\x0D\x0A\x0D\x0Areference = path_or_id @(method, none, 0, 0)\x0D\x0A\x0D\x0Amethod = \":\" id\x0D\x0A\x0D\x0Astatement =\x0D\x0A    create_symbol\x0D\x0A  | set_values\x0D\x0A  | trellis_scope\x0D\x0A  | create_constraint\x0D\x0A\x0D\x0Acreate_symbol = \"let\" ws id trim \"=\" trim expression\x0D\x0A\x0D\x0Acreate_constraint = path trim \"=\" trim expression\x0D\x0A\x0D\x0Aexpression =\x0D\x0A    @(expression_part, operation_separator, 1, 0)\x0D\x0A\x0D\x0Aoperation_separator = trim operator trim\x0D\x0A\x0D\x0Aexpression_part =\x0D\x0A    value\x0D\x0A  | create_node\x0D\x0A  | reference\x0D\x0A\x0D\x0Astring = ('\"' /[^\"]*/ '\"') | (\"'\" /[^']*/ \"'\")\x0D\x0Abool = \"true\" | \"false\"\x0D\x0Aint = /-?[0-9]+/\x0D\x0Afloat = /-?([0-9]*\\.)?[0-9]+f?/\x0D\x0Aoperator = '+' | '-' | '/' | '*' | '%'\x0D\x0A\x0D\x0Avalue = string | bool | int | float\x0D\x0A\x0D\x0Adummy = \"@&^%\"\x0D\x0A\x0D\x0Acreate_node = \"new\" ws id trim @(set_property_block, dummy, 0, 1)\x0D\x0A\x0D\x0Aset_property_block = \"{\" trim @(set_property, comma_or_newline, 1, 0) trim \"}\"\x0D\x0A\x0D\x0Aset_property = id trim \":\" trim expression\x0D\x0A\x0D\x0Aset_values = \"set\" ws path_or_id trim set_property_block\x0D\x0A\x0D\x0Atrellis_scope = id trim constraint_block\x0D\x0A\x0D\x0Aconstraint_block = \"(\" trim @(constraint, comma_or_newline, 1, 0) trim \")\"\x0D\x0A\x0D\x0Aconstraint = id trim \"=\" trim expression", false);
		$this->parser_definition = new metahub_parser_Definition();
		$this->parser_definition->load($result->get_data());
	}
	public function create_node($trellis) {
		$node = new metahub_engine_Node($this, $this->nodes->length, $trellis);
		$this->nodes->push($node);
		return $node;
	}
	public function get_node($id) {
		if($id < 0 || $id >= $this->nodes->length) {
			throw new HException(new HException("There is no node with an id of " . _hx_string_rec($id, "") . ".", null, null, _hx_anonymous(array("fileName" => "Hub.hx", "lineNumber" => 51, "className" => "metahub.Hub", "methodName" => "get_node"))));
		}
		return $this->nodes[$id];
	}
	public function get_node_count() {
		return $this->nodes->length - 1;
	}
	public function load_schema_from_file($url, $namespace) {
		$data = metahub_Utility::load_json($url);
		$this->schema->load_trellises($data->trellises, $namespace);
	}
	public function run_data($source) {
		$coder = new metahub_code_Coder($this);
		$expression = $coder->convert($source, $this->root_scope_definition);
		$expression->resolve($this->root_scope);
	}
	public function run_code($code) {
		$result = $this->parse_code($code);
		if(!$result->success) {
			throw new HException(new HException("Error parsing code.", null, null, _hx_anonymous(array("fileName" => "Hub.hx", "lineNumber" => 82, "className" => "metahub.Hub", "methodName" => "run_code"))));
		}
		$match = $result;
		$this->run_data($match->get_data());
	}
	public function parse_code($code) {
		if($this->parser_definition === null) {
			$this->load_parser();
		}
		$context = new metahub_parser_MetaHub_Context($this->parser_definition);
		$without_comments = metahub_Hub::$remove_comments->replace($code, "");
		return $context->parse($without_comments, null);
	}
	public function create_functions() {
		$functions = "{\x0D\x0A  \"trellises\": {\x0D\x0A    \"string\": {\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"string\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A    },\x0D\x0A    \"int\": {\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"int\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A    },\x0D\x0A    \"function\": {},\x0D\x0A    \"sum\": {\x0D\x0A      \"parent\": \"function\",\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        },\x0D\x0A        \"input\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A    },\x0D\x0A    \"subtract\": {\x0D\x0A      \"parent\": \"function\",\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        },\x0D\x0A        \"input\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A    },\x0D\x0A    \"count\": {\x0D\x0A      \"parent\": \"function\",\x0D\x0A      \"properties\": {\x0D\x0A        \"output\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        },\x0D\x0A        \"input\": {\x0D\x0A          \"type\": \"int\",\x0D\x0A          \"multiple\": \"true\"\x0D\x0A        }\x0D\x0A      }\x0D\x0A    }\x0D\x0A  }\x0D\x0A}";
		$data = haxe_Json::phpJsonDecode($functions);
		$this->schema->load_trellises($data->trellises, $this->metahub_namespace);
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
