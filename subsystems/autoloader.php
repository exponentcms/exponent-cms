<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
# Written and Designed by James Hunt
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

if (!defined('EXPONENT')) exit('');

if (phpversion() >= 5) {
	/* exdoc
	 * The definition of this constant lets other parts
	 * of the system know that the AutoLoader Subsystem
	 * has been included for use.
	 * @node Subsystems:Autoloader
	 */
	define('SYS_AUTOLOADER',1);
	
	/* exdoc
	 * In PHP5, the autoloader function will check these
	 * directories when it tries to load a class definition
	 * file.  Other parts of the system should append to this
	 * directory as needed, in order to take full advantage
	 * of autoloading
	 * @node Subsystems:Autoloader
	 */
	//$auto_dirs = array(BASE.'datatypes', BASE.'subsystems/forms', BASE.'subsystems/forms/controls');
	$auto_dirs = array(
			BASE.'datatypes', 
			BASE.'subsystems/forms', 
			BASE.'subsystems/forms/controls',  
			BASE.'framework/datatypes',
			BASE.'framework/core/subsystems',
			BASE.'framework/core/datatypes',
			BASE.'framework/core/controllers',
			BASE.'framework/modules/ecommerce/billingcalculators',
			BASE.'framework/modules/ecommerce/shippingcalculators',
			BASE.'framework/modules/ecommerce/products/controllers',
			BASE.'framework/modules/ecommerce/products/datatypes',
	);
	
	/* exdoc
	 * This function overrides the default PHP5 autoloader,
	 * and instead looks at the $AutoLoadDirs global to look
	 * for class files.  This function is automatically
	 * invoked in PHP5
	 *
	 * @param string $class The name of the class to look for.
	 * @node Subsystems:Autoloader
	 */
	function __autoload($class) {
		global $auto_dirs;
		
		// check the standard directories for class files.
		foreach ($auto_dirs as $auto_dir) {
			if (is_readable($auto_dir.'/'.$class.'.php')) {
				include_once($auto_dir.'/'.$class.'.php');
				return;
			}
		}
	
		// load any classes under the framework/modules directory	
		//loadModulesDir('framework/modules', $class);
	}
	
	// recursive function used for autoloading class files.
	/*function loadModulesDir($dir, $class) {
		if (is_readable($dir)) {
			$dh = opendir($dir);
			while (($file = readdir($dh)) !== false) {
				if (is_dir($dir.'/'.$file) && ($file != '..' && $file != '.')) {
					loadModulesDir($dir.'/'.$file, $class);
				} elseif (is_readable($dir.'/'.$class.'.php')) {
					include_once($dir.'/'.$class.'.php');
					return true;
				}
			}
		}
		return false;
	}*/
	
} else {
	define('SYS_AUTOLOADER',2);
	if (is_readable(BASE.'datatypes')) {
		$dh = opendir(BASE.'datatypes');
		while (($file = readdir($dh)) !== false) {
			if (is_readable(BASE.'datatypes/'.$file) && substr($file,-4,4) == '.php') {
				include_once(BASE.'datatypes/'.$file);
			}
		}
	}
}

?>
