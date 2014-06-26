<?php

interface metahub_code_symbols_ISchema_Symbol extends metahub_code_symbols_Symbol{
	function create_reference($path);
	function get_parent_trellis();
}
