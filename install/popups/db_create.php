<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
<script type="text/javascript">
<!--

var ids = new Array();
var tpls = new Array();
var base_url = document.location.href;

function buildHelp(dbname,username,password,target) {
	var str;
	for (var i = 0; i < ids.length; i++) {
		var elem = document.getElementById(ids[i]);
		elem.removeChild(elem.firstChild);

		str = tpls[i];
		str = str.replace("__DBNAME__", dbname);
		str = str.replace("__USERNAME__", username);
		str = str.replace("__PASSWORD__", password);

		elem.appendChild(document.createTextNode(str));
	}

	document.location.href = base_url + "#" + target;
}

//-->
</script>
<strong><?php echo gt('Creating a New Database'); ?></strong>

<div class="bodytext">
<?php echo gt('Exponent supports both the MySQL database server and the PostGreSQL database server as backends.'); ?>
<br /><br />
<div align="center">
|&#160;<a href="#mysql"><?php echo gt('MySQL'); ?></a>&#160;|&#160;<a href="#pgsql"><?php echo gt('PostGreSQL'); ?></a>&#160;|
</div>
<br />
<br />
<div id="form" class="important_box">
<?php echo gt('Fill out the form below and click "Go" to generate SQL statements for each supported database server.'); ?>
<br />
<table>
<form>
<tr>
	<td><?php echo gt('Database'); ?>:&#160;</td>
	<td><input class="text" type="text" name="dbname" value="" /></td>
</tr><tr>
	<td><?php echo gt('Username'); ?>:&#160;</td>
	<td><input class="text" type="text" name="username" value="" /></td>
</tr><tr>
	<td><?php echo gt('Password'); ?>:&#160;</td>
<!--	<td><input class="text" type="text" name="password" value="" /></td>-->
    <td><input class="password" type="text" name="password" value="" /></td>
</tr><tr>
	<td></td>
	<td><input class="text" type="button" value="<?php echo gt('For MySQL...'); ?>" onclick="buildHelp(this.form.dbname.value,this.form.username.value,this.form.password.value,'mysql'); return false" /></td>
</tr>
</form>
</table>
<br />
</div>
<br /><br />
<hr size="1" />
<img id="mysql" src="images/mysql.png" /><br />
<strong><?php echo gt('MySQL Database Creation'); ?></strong>
<br /><br />
<?php echo gt('If you have access to the database server, and have sufficient privileges to create databases, you can use the following SQL statements to setup the database for Exponent.  Note that you will have to fill in the form above before using these.'); ?>
<br /><br />
<strong><?php echo gt('Create the Database'); ?></strong><br />
<textarea id="mysql_create" rows="1" style="width: 100%">(<?php echo gt('fill in the above form and click "Go" to generate SQL'); ?>)</textarea>
<strong><?php echo gt('Grant Database Rights'); ?></strong><br />
<textarea id="mysql_perms" rows="3" style="width: 100%">(<?php echo gt('fill in the above form and click "Go" to generate SQL'); ?>)</textarea>
<script type="text/javascript">
ids.push("mysql_create");
tpls.push("CREATE DATABASE __DBNAME__;");

ids.push("mysql_perms");
tpls.push("GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP to __USERNAME__ identified by '__PASSWORD__';");
</script>

<br /><br />
<hr size="1" />
<img id="pgsql" src="images/pgsql.gif" /><br />
<strong><?php echo gt('PostGreSQL Database Creation'); ?></strong>
<br /><br />
<?php echo gt('Because PostGreSQL does not maintain its own set of users like MySQL (and instead relies on system users) you will have to refer to the <a href="http://www.postgresql.org/">online documentation</a> for information on creating new databases and assigning user permissions.'); ?>
</div>