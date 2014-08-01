<?php

interface metahub_engine_IPort {
	//;
	//;
	function add_dependency($other, $operator);
	function get_value($context = null);
	function set_value($v, $context = null);
	function get_type();
}
