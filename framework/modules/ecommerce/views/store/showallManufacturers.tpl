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
	<h1>{$moduletitle|default:"Manufacturers"|gettext}</h1>
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
