<?php

interface metahub_engine_INode {
	function get_port($index);
	function get_value($index, $context);
	function set_value($index, $value, $context, $source = null);
}
