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

$i18n = exponent_lang_loadFile('install/pages/welcome.php');

?>
<h2 id="subtitle"><?php echo $i18n['title']; ?></h2>
<?php echo $i18n['thanks']; ?>
<br /><br />
<?php echo $i18n['guide']; ?>
<br /><br />

<ul>
	<li><a href="?page=sanity&type=new"><?php echo $i18n['new']; ?></a></li>
	<li><a href="?page=sanity&type=upgrade"><?php echo $i18n['upgrade']; ?></a></li>
</ul>