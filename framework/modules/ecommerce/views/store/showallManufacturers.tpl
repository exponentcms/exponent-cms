{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

<div class="module store showall-manufacturers">
	<h1>{$moduletitle|default:"Manufacturers"}</h1>
	<ul>
		{foreach from=$manufacturers item=manufacturer}
			<li><a href="{link action=showallByManufacturer id=$manufacturer->id}">{$manufacturer->title}</a></li>
		{/foreach}
	</ul>	
	
	{permissions level=$smarty.const.UILEVEL_PERMISSIONS}
        {if $permissions.create == 1 or $permissions.edit == 1}
        <div id="prod-admin">
            <a href="{link controller=company action=create}">Add a new company</a>
        </div>
    {/if}
    {/permissions}
</div>
