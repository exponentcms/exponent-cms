<?php

// Array map workaround
if (!function_exists("array_map")) {
	function array_map($callback, $arr1) {
		if (func_num_args() > 2) return; // error here
		
		$empty = array();
		foreach ($arr1 as $key=>$value) {
			$empty[$key] = $callback($value);
		}
		return $empty;
	}
}

?>