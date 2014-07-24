<?php

interface metahub_code_symbols_Symbol {
	function resolve($scope);
	function get_trellis();
	function get_layer();
	function get_type();
}
