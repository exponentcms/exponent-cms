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

<div class="scaffold show-random">
	<h1>{$moduletitle|default:"Listings for"|gettext|cat:" `$modelname`"}</h1>
	{permissions}
        	{if $permissions.create == 1}
        		{icon controller=$model_name action=create text="Create a new"|gettext|cat:" `$modelname`"}{br}
        	{/if}
        {/permissions}
	<ul>
        {foreach from=$items item=listing}
		<li class="listing">
			<h3>
				<a href="{link controller=$controller action=show id=$listing->id}">{$listing->title}</a>
				{permissions}
					<div class="item-actions">
						{if $permissions.edit == 1}
							{icon controller=$controller action=edit record=$listing}
						{/if}
						{if $permissions.delete == 1}
							{icon controller=$controller action=delete record=$listing}
						{/if}
					</div>
				{/permissions}
			</h3>
			<p>{$listing->body}</p>
			{clear}
		</li>
        {/foreach}
	</ul>
</div>
