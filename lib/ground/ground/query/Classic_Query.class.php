<?php

class ground_query_Classic_Query {
	public function __construct() {
		;
	}
	public $trellis;
	public $filters;
	public $sorts;
	public $range;
	public $expansions;
	public $properties;
	public function extend($source, $schema, $namespace) {
		$this->trellis = $schema->get_trellis($source->trellis, $namespace, null);
		$this->filters = $source->filters;
		$this->sorts = $source->sorts;
		if($source->properties !== null) {
			$this->properties = $source->properties;
		}
		$this->expansions = $source->expansions;
		if(_hx_field($source, "range") !== null) {
			$this->range = $source->range;
		} else {
			if(_hx_field($source, "pager") !== null) {
				$this->range = _hx_anonymous(array());
				if($source->pager->offset !== null) {
					$this->range->start = $source->pager->offset;
				}
				if($source->pager->limit !== null) {
					$this->range->length = $source->pager->limit;
				}
			}
		}
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
