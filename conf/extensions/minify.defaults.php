<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

if (!defined('MINIFY')) define('MINIFY','0');
if (!defined('MINIFY_MAXAGE')) define('MINIFY_MAXAGE','180000');
if (!defined('MINIFY_URL_LENGTH')) define('MINIFY_URL_LENGTH','1500');
if (!defined('MINIFY_MAX_FILES')) define('MINIFY_MAX_FILES','30');

if (!defined('MINIFY_ERROR_LOGGER')) define('MINIFY_ERROR_LOGGER','0');
if (!defined('MINIFY_INLINE_CSS')) define('MINIFY_INLINE_CSS','1');
if (!defined('MINIFY_LINKED_CSS')) define('MINIFY_LINKED_CSS','1');
if (!defined('MINIFY_INLINE_JS')) define('MINIFY_INLINE_JS','1');
if (!defined('MINIFY_LINKED_JS')) define('MINIFY_LINKED_JS','1');
if (!defined('MINIFY_YUI3')) define('MINIFY_YUI3','0');
if (!defined('MINIFY_YUI2')) define('MINIFY_YUI2','0');

?>