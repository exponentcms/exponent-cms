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

$i18n = exponent_lang_loadFile('install/popups/db_create.php');

?>
<script type="text/javascript">
<!--

var ids = new Array();
var tpls = new Array();
var base_url = document.location.href;

function buildHelp(dbname,username,password,target) {
	for (var i = 0; i < ids.length; i++) {
		var elem = document.getElementById(ids[i]);
		elem.removeChild(elem.firstChild);

		str = tpls[i];
		str = str.replace("__DBNAME__",dbname);
		str = str.replace("__USERNAME__",username);
		str = str.replace("__PASSWORD__",password);

		elem.appendChild(document.createTextNode(str));
	}

	document.location.href = base_url + "#" + target;
}

//-->
</script>
<b><?php echo $i18n['title']; ?></b>

<div class="bodytext">
<?php echo $i18n['header']; ?>
<br /><br />
<div align="center">
|&nbsp;<a href="#mysql"><?php echo $i18n['mysql']; ?></a>&nbsp;|&nbsp;<a href="#pgsql"><?php echo $i18n['postgres']; ?></a>&nbsp;|
</div>
<br />

<a name="form"></a>
<br />
<div class="important_box">
<?php echo $i18n['instructions']; ?>
<br />
<form>
<table>
<tr>
	<td><?php echo $i18n['database']; ?>:&nbsp;</td>
	<td><input class="text" type="text" name="dbname" value="" /></td>
</tr><tr>
	<td><?php echo $i18n['username']; ?>:&nbsp;</td>
	<td><input class="text" type="text" name="username" value="" /></td>
</tr><tr>
	<td><?php echo $i18n['password']; ?>:&nbsp;</td>
	<td><input class="text" type="text" name="password" value="" /></td>
</tr><tr>
	<td></td>
	<td><input class="text" type="button" value="<?php echo $i18n['for_mysql']; ?>" onclick="buildHelp(this.form.dbname.value,this.form.username.value,this.form.password.value,'mysql'); return false" /></td>
</tr>
</form>
</table>
<br />
</div>
<br /><br />
<a name="mysql"></a>
<hr size="1" />
<img src="images/mysql.png" /><br />
<b><?php echo $i18n['mysql_title']; ?></b>
<br /><br />
<?php echo $i18n['mysql_instructions']; ?>
<br /><br />
<b><?php echo $i18n['create_db']; ?></b><br />
<textarea id="mysql_create" rows="1" style="width: 100%">(<?php echo $i18n['fill_out']; ?>)</textarea>
<b><?php echo $i18n['create_privs']; ?></b><br />
<textarea id="mysql_perms" rows="3" style="width: 100%">(<?php echo $i18n['fill_out']; ?>)</textarea>
<script type="text/javascript">
ids.push("mysql_create");
tpls.push("CREATE DATABASE __DBNAME__;");

ids.push("mysql_perms");
tpls.push("GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP to __USERNAME__ identified by '__PASSWORD__';");
</script>

<br /><br />
<a name="pgsql"></a>
<hr size="1" />
<img src="images/pgsql.gif" /><br />
<b><?php echo $i18n['postgres_title']; ?></b>
<br /><br />
<?php echo $i18n['postgres_instructions']; ?>
</div>