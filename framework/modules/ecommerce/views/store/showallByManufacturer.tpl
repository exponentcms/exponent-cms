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

<div class="store showall showall-by-manufacturer">
    
    <h1>All Products for {$company->title}</h1>
    
    {$page->links}
    
    <div class="products">
        {foreach from=$page->records item=listing name=listings}
        {include file=$listing->getForm('storeListing')}
        {/foreach}
    </div>

    {$page->links}
    
    {permissions}
		<div class="module-actions">
			{if $permissions.edit == 1}
				{icon class=add action=create title="Add a new product" text="Add a New Product"}
			{/if}
		</div>
    {/permissions}
</div>
