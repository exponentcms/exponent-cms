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

$i18n = exponent_lang_loadFile('install/popups/db_priv.php');

?>
<b><?php echo $i18n['title']; ?></b>
<div class="bodytext">
<?php echo $i18n['header']; ?>
<br /><br />

<tt><?php echo $i18n['create']; ?></tt><br />
&nbsp;&nbsp;-&nbsp;<?php echo $i18n['create_desc']; ?>
<br /><br />

<tt><?php echo $i18n['alter']; ?></tt><br />
&nbsp;&nbsp;-&nbsp;<?php echo $i18n['alter_desc']; ?>
<br /><br />

<tt><?php echo $i18n['drop']; ?></tt><br />
&nbsp;&nbsp;-&nbsp;<?php echo $i18n['drop_desc']; ?>
<br /><br />

<tt><?php echo $i18n['select']; ?></tt><br />
&nbsp;&nbsp;-&nbsp;<?php echo $i18n['select_desc']; ?>
<br /><br />

<tt><?php echo $i18n['insert']; ?></tt><br />
&nbsp;&nbsp;-&nbsp;<?php echo $i18n['insert_desc']; ?>
<br /><br />

<tt><?php echo $i18n['update']; ?></tt><br />
&nbsp;&nbsp;-&nbsp;<?php echo $i18n['update_desc']; ?>
<br /><br />

<tt><?php echo $i18n['delete']; ?></tt><br />
&nbsp;&nbsp;-&nbsp;<?php echo $i18n['delete_desc']; ?>
</div>