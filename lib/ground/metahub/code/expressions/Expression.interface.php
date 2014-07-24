<?php

interface metahub_code_expressions_Expression {
	//;
	function resolve($scope);
	function to_port($scope, $group);
}
