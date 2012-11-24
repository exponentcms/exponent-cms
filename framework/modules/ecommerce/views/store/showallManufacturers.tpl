{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<div class="module store showall-manufacturers">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create == true || $permissions.edit == true}
            {icon class="add" action=create text="Add a Product"|gettext}
        {/if}
        {if $permissions.manage == 1}
            {icon action=manage text="Manage Products"|gettext}
            {icon controller=storeCategory action=manage text="Manage Store Categories"|gettext}
            {icon class="manage" controller="company" action="showall" text="Manage Manufacturers"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
	<ul>
		{foreach from=$manufacturers item=manufacturer}
			<li><a href="{link action=showallByManufacturer id=$manufacturer->id}">{$manufacturer->title}</a></li>
		{/foreach}
	</ul>	
	{permissions}
        {if $permissions.create == 1 or $permissions.edit == 1}
            <div id="prod-admin">
                {icon class=add controller=company action=create text="Add a New Company"|gettext}
            </div>
        {/if}
    {/permissions}
</div>
