<?php

class ground_query_Classic_Query {
	public function __construct() {
		;
	}
	public $trellis;
	public $filters;
	public $sorts;
	public $pager;
	public $expansions;
	public function extend($source, $schema) {
		$this->trellis = $schema->get_trellis($source->trellis);
		$this->filters = $source->filters;
		$this->sorts = $source->sorts;
		$this->pager = $source->pager;
		$this->expansions = $source->expansions;
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
	function __toString() { return 'ground.query.Classic_Query'; }
}
