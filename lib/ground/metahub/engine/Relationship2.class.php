<?php

class metahub_engine_Relationship2 {
	public function __construct($dependent, $operator, $dependency) {
		if(!php_Boot::$skip_constructor) {
		$this->dependent = $dependent;
		$this->operator = $operator;
		$this->dependency = $dependency;
	}}
	public $dependent;
	public $dependency;
	public $operator;
	public function set_value($value, $context = null) {
		return $this->dependency->set_value($value, $context);
	}
	public function check_value($value, $context) {
		$other_value = $this->dependency->get_value($context);
		if((is_object($_t = $this->operator) && !($_t instanceof Enum) ? $_t === 0 : $_t == 0)) {
			return $other_value;
		}
		{
			$_g = $this->dependency->get_type();
			switch($_g) {
			case 1:{
				$first = $value;
				$second = $other_value;
				return metahub_engine_Relationship2::numeric_operation($first, $this->operator, $second);
			}break;
			case 5:{
				$first1 = $value;
				$second1 = $other_value;
				return metahub_engine_Relationship2::numeric_operation($first1, $this->operator, $second1);
			}break;
			default:{
				throw new HException(new HException("Operator " . Std::string($this->operator) . " can only be used with numeric types.", null, null, _hx_anonymous(array("fileName" => "Relationship.hx", "lineNumber" => 43, "className" => "metahub.engine.Relationship2", "methodName" => "check_value"))));
			}break;
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
	static function numeric_operation($first, $operator, $second) {
		switch($operator) {
		case 1:{
			if($first < $second) {
				return $first;
			} else {
				return $second;
			}
		}break;
		case 2:{
			if($first > $second) {
				return $first;
			} else {
				return $second;
			}
		}break;
		case 3:{
			if($first <= $second) {
				return $first;
			} else {
				return $second;
			}
		}break;
		case 4:{
			if($first >= $second) {
				return $first;
			} else {
				return $second;
			}
		}break;
		default:{
			throw new HException(new HException("Operator " . Std::string($operator) . " is not yet supported.", null, null, _hx_anonymous(array("fileName" => "Relationship.hx", "lineNumber" => 72, "className" => "metahub.engine.Relationship2", "methodName" => "numeric_operation"))));
		}break;
		}
	}
	function __toString() { return 'metahub.engine.Relationship2'; }
}
