<?php

class metahub_code_expressions_Create_Node implements metahub_code_expressions_Expression{
	public function __construct($trellis) {
		if(!php_Boot::$skip_constructor) {
		$this->assignments = new haxe_ds_IntMap();
		$this->trellis = $trellis;
		$this->type = new metahub_code_Type_Reference(3, $trellis);
	}}
	public $trellis;
	public $assignments;
	public $type;
	public function resolve($scope) {
		haxe_Log::trace("create node", _hx_anonymous(array("fileName" => "Create_Node.hx", "lineNumber" => 18, "className" => "metahub.code.expressions.Create_Node", "methodName" => "resolve", "customParams" => (new _hx_array(array($this->trellis->name))))));
		$node = $scope->hub->create_node($this->trellis);
		if(null == $this->assignments) throw new HException('null iterable');
		$__hx__it = $this->assignments->keys();
		while($__hx__it->hasNext()) {
			$i = $__hx__it->next();
			$expression = $this->assignments->get($i);
			$node->set_value($i, $expression->resolve($scope));
			unset($expression);
		}
		return $node->id;
	}
	public function to_port($scope) {
		return null;
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
	function __toString() { return 'metahub.code.expressions.Create_Node'; }
}
