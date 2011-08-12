<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

?>
<b><?php echo gt('Using an Existing Database'); ?></b>
<div class="bodytext">
<?php echo gt('A pre-existing database can be used to store the content of your website, however a few issues must be dealt with.'); ?>
<br /><br />
<?php echo gt('Exponent needs its own set of tables within a pre-existing database in order to function properly.  This can be accomplished by specifying a new table prefix.'); ?>
<br /><br />
<?php echo gt('The table prefix is used to make each table\'s name in the database unique.  It is prepended to the name of each table.  This means that two Exponent sites can use the database "db" if one has a table prefix of "exponent" and the other uses "cms".'); ?>
<br /><br />
<?php echo gt('Exponent will prepend your table prefix with an underscore.  This improves database readability, and helps with troubleshooting.'); ?>
</div>