<?php

if (!function_exists('gd_info')) {
	function gd_info() {
		$array = array(
			'GD Version'=>'Not Supported',
			'FreeType Support'=>false,
			'FreeType Support'=>false,
			'FreeType Linkage'=>'',
			'T1Lib Support'=>false,
			'TtrueLib Support'=>false,
			'GIF Read Support'=>false,
			'GIF Create Support'=>false,
			'JPG Support'=>false,
			'PNG Support'=>false,
			'WBMP Support'=>false,
			'XBM Support'=>false,
			'JIS-mapped Japanese Font Support'=>false
		);
		$gif_support = 0;
	
		ob_start();
		phpinfo();
		$info = ob_get_contents();
		ob_end_clean();
		
		foreach (explode("\n", $info) as $line) {
			if (strpos($line,'GD Version') !== false) {
				$array['GD Version'] = trim(str_replace('GD Version','',strip_tags($line)));
			}
			if (strpos($line,'FreeType Support') !== false) {
				$array['FreeType Support'] = trim(str_replace('FreeType Support','',strip_tags($line)));
			}
			if (strpos($line,'FreeType Linkage') !== false) {
				$array['FreeType Linkage'] = trim(str_replace('FreeType Linkage','',strip_tags($line)));
			}
			if (strpos($line,'T1Lib Support') !== false) {
				$array['T1Lib Support'] = trim(str_replace('T1Lib Support','',strip_tags($line)));
			}
			if (strpos($line,'GIF Read Support') !== false) {
				$array['GIF Read Support'] = trim(str_replace('GIF Read Support','',strip_tags($line)));
			}
			if (strpos($line,'GIF Create Support') !== false) {
				$array['GIF Create Support'] = trim(str_replace('GIF Create Support','',strip_tags($line)));
			}
			if (strpos($line,'GIF Support') !== false) {
				$gif_support = trim(str_replace('GIF Support','',strip_tags($line)));
			}
			if (strpos($line,'JPG Support') !== false) {
				$array['JPG Support'] = trim(str_replace('JPG Support','',strip_tags($line)));
			}
			if (strpos($line,'PNG Support') !== false) {
				$array['PNG Support'] = trim(str_replace('PNG Support','',strip_tags($line)));
			}
			if (strpos($line,'WBMP Support') !== false) {
				$array['WBMP Support'] = trim(str_replace('WBMP Support','',strip_tags($line)));
			}
			if (strpos($line,'XBM Support') !== false) {
				$array['XBM Support'] = trim(str_replace('XBM Support','',strip_tags($line)));
			}
		}
		if ($gif_support === 'enabled') {
			$array['GIF Read Support']  = true;
			$array['GIF Create Support'] = true;
		}
		if ($array['FreeType Support'] === 'enabled') {
			$array['FreeType Support'] = true;
		}
		if ($array['TtrueLib Support'] === 'enabled') {
			$array['TtrueLib Support'] = true;
		}
		if ($array['GIF Read Support'] === 'enabled') {
			$array['GIF Read Support'] = true;
		}
		if ($array['GIF Create Support'] === 'enabled') {
			$array['GIF Create Support'] = true;
		}
		if ($array['JPG Support'] === 'enabled') {
			$array['JPG Support'] = true;
		}
		if ($array['PNG Support'] === 'enabled') {
			$array['PNG Support'] = true;
		}
		if ($array['WBMP Support'] === 'enabled') {
			$array['WBMP Support'] = true;
		}
		if ($array['XBM Support'] === 'enabled') {
			$array['XBM Support'] = true;
		}
		return $array;
	}

}

$info = gd_info();
define('EXPONENT_HAS_GD',($info['GD Version'] == 'Not Supported' ? 0 : 1));

?>