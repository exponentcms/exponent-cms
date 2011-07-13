<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006 Maxim Mueller
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

?>
<h2><?php echo gt('Simple Site Upgrade'); ?></h2>
<p>
<?php echo gt("
Since your website has a configuration file already in place, we're going to perform a couple simple tasks to ensure you're up and running in no time.
"); ?>
</p>
<p>
<?php //echo gt("
//Next, we'll <a href=\"http://docs.exponentcms.org/docs/current/install-tables\" target=\"_blank\">Install Tables</a>, and run through any upgrade scripts needed to bring your code and database up to date.
//"); ?>
<!--</p>-->
<!--<a class="awesome large green" href="?page=upgrade-2">--><?php //echo gt("Continue to Install Tables"); ?><!--</a>-->
<?php echo gt("
Next, we'll run through any upgrade scripts needed to bring your code and database up to date.
"); ?>
</p>
<a class="awesome large green" href="?page=upgrade-2"><?php echo gt('Continue Upgrade') ?></a>
