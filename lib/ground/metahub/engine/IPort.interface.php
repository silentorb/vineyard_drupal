<?php

interface metahub_engine_IPort {
	//;
	function connect($other);
	function get_value($context);
	function set_value($v, $context);
	function get_type();
}
