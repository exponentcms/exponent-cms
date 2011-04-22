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

<div class="scaffold showall">
	<h1>{$moduletitle|default:"Listings for `$modelname`"}</h1>
	{permissions}
        	{if $permissions.create == 1}
        		{icon controller=$model_name action=create text="Create a new `$modelname`"}{br}
        	{/if}
        {/permissions}
	<ul>
        {foreach from=$page->records item=listing}
		<li class="listing">
			<h3>
				<a href="{link controller=$controller action=show id=$listing->id}">{$listing->title}</a>
				{permissions}
					<div class="item-actions">
						{if $permissions.edit == 1}
							{icon controller=$controller action=edit record=$listing title="Edit this `$modelname`"}
						{/if}
						{if $permissions.delete == 1}
							{icon controller=$controller action=delete record=$listing title="Delete this `$modelname`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
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


	

