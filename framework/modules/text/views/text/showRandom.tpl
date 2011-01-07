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

<div class="module text show-random">
	<h1>{$moduletitle|default:"Listings for `$modelname`"}</h1>
	{permissions level=$smarty.const.UILEVEL_NORMAL}
        	{if $permissions.create == 1}
        		<a href="{link controller=$model_name action=create}">Create a new {$modelname}</a>
        	{/if}
        {/permissions}
	
        {foreach from=$items item=listing}
		<h3>
			<a href="{link controller=$controller action=show id=$listing->id}">{$listing->title}</a>
			{permissions level=$smarty.const.UILEVEL_NORMAL}
				{if $permissions.edit == 1}
					{icon controller=$controller action=edit id=$listing->id title="Edit this `$modelname`"}
				{/if}
				{if $permissions.delete == 1}
					{icon controller=$controller action=delete id=$listing->id title="Delete this `$modelname`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
				{/if}
			{/permissions}
		</h3>
		<p>{$listing->body}</p>
		
        {/foreach}
        {clear}
</div>
