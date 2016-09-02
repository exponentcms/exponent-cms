{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="scaffold showall-by-tags">
    {if $smarty.const.DEVELOPMENT}
        <h4>{'This is the scaffold view'|gettext}</h4>
    {/if}
	<h1>{$moduletitle|default:"Tag Listings for"|gettext|cat:" `$model_name`"}</h1>

	{permissions}
		<div class="module-actions">
        	{if $permissions.create}
        		{icon controller=$model_name action=create text="Create a new"|gettext|cat:" `$model_name`"}{br}
        	{/if}
		</div>
    {/permissions}
	<ul>
        {*{foreach from=$items item=listing}*}
		{pagelinks paginate=$page top=1}
	    {foreach from=$page->records item=item}
		<li class="item listing">
			<h3><a href="{link controller=$controller action=show id=$item->id}">{$item->title}</a></h3>
            <div class="bodycopy">
                <p>{$item->body}</p>
            </div>
			{permissions}
				<div class="item-actions">
					{if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
						{icon controller=$controller action=edit record=$item}
					{/if}
					{if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
						{icon controller=$controller action=delete record=$item}
					{/if}
				</div>
			{/permissions}
			{clear}
		</li>
        {/foreach}
		{pagelinks paginate=$page bottom=1}
	</ul>
</div>
