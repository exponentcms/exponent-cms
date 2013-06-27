<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

if (!defined('EXPONENT_VERSION_MAJOR')) {
	// the RELEASE constant is changed by the build scripts at code freeze
	define('RELEASE','RELEASE');
	if (RELEASE != '%%RELEASE%%') {

		/* exdoc
		 * This is the major version number of Exponent; the 1 in 1.96.2-beta3
		 */
		define('EXPONENT_VERSION_MAJOR','2');
		/* exdoc
		 * This is the minor version number of Exponent; the 96 in 1.96.2-beta3
		 */
		define('EXPONENT_VERSION_MINOR','2');
		/* exdoc
		 * This is the revision version number of Exponent; the 2 in 1.96.2-beta3
		 */
		define('EXPONENT_VERSION_REVISION','0');
		/* exdoc
		 * This specifies the type of release, either 'alpha','beta','release-candidate' or '' (for stable).
		 */
		define('EXPONENT_VERSION_TYPE','patch');
		/* exdoc
		 * This number is bumped each time a distribution of a single version is
		 * released.  For instance, the 3rd beta has an version type iteration of 3.
		 */
		define('EXPONENT_VERSION_ITERATION','5'); // only applies to betas, alphas, or release candidates
		/* exdoc
		 * This is the date that this version of Exponent was released.
		 */
		define('EXPONENT_VERSION_BUILDDATE','1372342175');
	} else {
		// the info for the "next" version if we are a pre-release from the repository
		define('EXPONENT_VERSION_MAJOR','2');
		define('EXPONENT_VERSION_MINOR','2');
		define('EXPONENT_VERSION_REVISION','0');
		define('EXPONENT_VERSION_TYPE','develop');
		define('EXPONENT_VERSION_ITERATION','');
		define('EXPONENT_VERSION_BUILDDATE',time());
        // set DEVELOPMENT to 1 to debug the install process
//        define('DEVELOPMENT','1');
	}
}

?>