<?php

class metahub_schema_Property_Chain_Helper {
	public function __construct(){}
	static function flip($chain) {
		$result = new _hx_array(array());
		$i = $chain->length - 1;
		while($i >= 0) {
			if(_hx_array_get($chain, $i)->other_property !== null) {
				$result->push(_hx_array_get($chain, $i)->other_property);
			}
			--$i;
		}
		return $result;
	}
	static function from_string($path, $trellis, $start_index = null) {
		if($start_index === null) {
			$start_index = 0;
		}
		$result = new _hx_array(array());
		{
			$_g1 = $start_index;
			$_g = $path->length;
			while($_g1 < $_g) {
				$x = $_g1++;
				$property = $trellis->get_property($path[$x]);
				$result->push($property);
				$trellis = $property->other_trellis;
				unset($x,$property);
			}
		}
		return $result;
	}
	static function perform($chain, $node, $action, $start = null) {
		if($start === null) {
			$start = 0;
		}
		{
			$_g1 = $start;
			$_g = $chain->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$link = $chain[$i];
				if((is_object($_t = $link->type) && !($_t instanceof Enum) ? $_t === 4 : $_t == 4)) {
					$list_port = $node->get_port($link->id);
					$array = $list_port->get_array();
					{
						$_g2 = 0;
						while($_g2 < $array->length) {
							$j = $array[$_g2];
							++$_g2;
							metahub_schema_Property_Chain_Helper::perform($chain, $node->hub->get_node($j), $action, $i + 1);
							unset($j);
						}
						unset($_g2);
					}
					return;
					unset($list_port,$array);
				} else {
					if((is_object($_t2 = $link->type) && !($_t2 instanceof Enum) ? $_t2 === 3 : $_t2 == 3)) {
						$id = $node->get_value($link->id);
						$node = $node->hub->nodes[$id];
						unset($id);
					} else {
						throw new HException(new HException("Not supported: " . _hx_string_or_null($link->name), null, null, _hx_anonymous(array("fileName" => "Property_Chain.hx", "lineNumber" => 61, "className" => "metahub.schema.Property_Chain_Helper", "methodName" => "perform"))));
					}
					unset($_t2);
				}
				unset($link,$i,$_t);
			}
		}
		call_user_func_array($action, array($node));
	}
	function __toString() { return 'metahub.schema.Property_Chain_Helper'; }
}
