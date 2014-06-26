<?php

interface metahub_engine_INode {
	//;
	function get_port($index);
	function get_value($index);
	function set_value($index, $value);
	//;
}
