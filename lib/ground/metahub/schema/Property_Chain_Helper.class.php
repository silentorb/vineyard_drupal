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
	function __toString() { return 'metahub.schema.Property_Chain_Helper'; }
}
