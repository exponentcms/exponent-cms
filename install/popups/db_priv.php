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

?>
<strong><?php echo gt('Database User Privileges'); ?></strong>
<div class="bodytext">
<?php echo gt('When Exponent connects to the database, it needs to be able to run the following types of queries:'); ?>
<br /><br />

<tt>CREATE TABLE</tt><br />
&#160;&#160;-&#160;<?php echo gt('These queries create new table structures inside the database.  Exponent needs this when you install it for the first time.  CREATE TABLE queries are also run after new modules are uploaded to the site.'); ?>
<br /><br />

<tt>ALTER TABLE</tt><br />
&#160;&#160;-&#160;<?php echo gt('If you upgrade any module in Exponent, these queries will be run to change table structures in the database.'); ?>
<br /><br />

<tt>DROP TABLE</tt><br />
&#160;&#160;-&#160;<?php echo gt('These queries are executed on the database whenever an administrator trims it to remove tables that are no longer used.'); ?>
<br /><br />

<tt>SELECT</tt><br />
&#160;&#160;-&#160;<?php echo gt('Queries of this type are very important to the basic operation of Exponent.  All data stored in the database is read back through the use of SELECT queries.'); ?>
<br /><br />

<tt>INSERT</tt><br />
&#160;&#160;-&#160;<?php echo gt('Whenever new content is added to the site, new permissions are assigned, users and/or groups are created and configuration data is saved, Exponent runs INSERT queries.'); ?>
<br /><br />

<tt>UPDATE</tt><br />
&#160;&#160;-&#160;<?php echo gt('When content or configurations are updated, Exponent modifies the data in its tables by issuing UPDATE queries.'); ?>
<br /><br />

<tt>DELETE</tt><br />
&#160;&#160;-&#160;<?php echo gt('These queries remove content and configuration from the tables in the site database.  They are also executed whenever users and groups are removed, and permissions are revoked.'); ?>
</div>