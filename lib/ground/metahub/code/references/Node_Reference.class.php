<?php

class metahub_code_references_Node_Reference extends metahub_code_references_Reference {
	public function __construct($symbol, $chain = null) { if(!php_Boot::$skip_constructor) {
		parent::__construct($symbol,$chain);
	}}
	public function get_port($scope) {
		throw new HException(new HException("Not implemented yet.", null, null, _hx_anonymous(array("fileName" => "Node_Reference.hx", "lineNumber" => 14, "className" => "metahub.code.references.Node_Reference", "methodName" => "get_port"))));
	}
	public function resolve($scope) {
		return $this->get_node($scope);
	}
	public function get_node($scope) {
		$id = $this->symbol->resolve($scope);
		$node = $scope->hub->nodes[$id];
		$nodes = $scope->hub->nodes;
		$length = $this->chain->length - 1;
		{
			$_g = 0;
			while($_g < $length) {
				$i = $_g++;
				$id1 = $node->get_value(_hx_array_get($this->chain, $i)->id);
				$node = $nodes[$id1];
				unset($id1,$i);
			}
		}
		return $node;
	}
	function __toString() { return 'metahub.code.references.Node_Reference'; }
}
