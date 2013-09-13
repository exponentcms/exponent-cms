{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

<div class="module store showall showall-by-manufacturer">
    <h1>{'All Products for'|gettext} {$company->title}</h1>
    {permissions}
    <div class="module-actions">
        {if $permissions.create == true}
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

    {pagelinks paginate=$page top=1}
    <div class="products">
        {foreach from=$page->records item=listing name=listings}
            {include file=$listing->getForm('storeListing')}
        {/foreach}
    </div>
    {pagelinks paginate=$page bottom=1}
    {permissions}
		<div class="module-actions">
			{if $permissions.edit == 1}
				{icon class=add action=create text="Add a New Product"|gettext}
			{/if}
		</div>
    {/permissions}
</div>
