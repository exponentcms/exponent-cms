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
<h1 id="subtitle"><?php echo $i18n['title']; ?></h1>
<p>
    <?php echo $i18n['thanks']; ?>
</p>
<p>
    <a class="awesome large green" href="?page=sanity&type=new"><?php echo $i18n['new']; ?></a>
    <a class="awesome large green" href="?page=sanity&type=upgrade"><?php echo $i18n['upgrade']; ?></a>
</p>