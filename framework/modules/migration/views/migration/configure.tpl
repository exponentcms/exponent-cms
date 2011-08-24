{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}

<div id="migrationconfig" class="module migration configure">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help with Migration Database Settings" module="configure-migration-settings"}
        </div>
		<h1>{"Database Settings to Migrate Your Old Site"|gettext}</h1>	    
    </div>

	<p>
		This is where you enter the database connection information for your
		old Exponent v1 site you want to migrate data from.
	</p>
    {form action=saveconfig}
		{control type=text name=server label="Server Name" value=$config.server|default:'localhost'}
		{control type="text" name="database" label="Database Name" value=$config.database}
		{control type="text" name="username" label="Username" value=$config.username}
		{control type="password" name="password" label="Password" value=$config.password}
		{control type="text" name="port" label="Port" value=$config.port|default:3306}
		{control type="text" name="prefix" label="Exponent Table Prefix" value=$config.prefix|default:'exponent'}
	    {control type="checkbox" name="fix_database" label="Attempt to fix tables in old database? (may cause a timeout on slow connection)" value=1 checked=false}
        {control type=buttongroup submit="Save Config" cancel="Cancel"}
    {/form}
	{br}<hr>{br}
	<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link module=migration action=manage_users}"><b>Next Step -> Migrate Users & Groups</b></a>
</div>