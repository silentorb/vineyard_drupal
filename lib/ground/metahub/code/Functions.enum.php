<?php

class metahub_code_Functions extends Enum {
	public static $none;
	public static $subtract;
	public static $sum;
	public static $__constructors = array(0 => 'none', 2 => 'subtract', 1 => 'sum');
	}
metahub_code_Functions::$none = new metahub_code_Functions("none", 0);
metahub_code_Functions::$subtract = new metahub_code_Functions("subtract", 2);
metahub_code_Functions::$sum = new metahub_code_Functions("sum", 1);
