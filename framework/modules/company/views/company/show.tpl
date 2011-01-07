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

<div class="module company show">
	<div id="details">
		{permissions level=$smarty.const.UILEVEL_NORMAL}
		        <div class="moduleactions">
                		{if $permissions.edit == 1}
					{icon controller=$model_name action=edit id=$record->id text="Edit `$record->title`"}
				{/if}
			</div>
		{/permissions}
		<h1>{$moduletitle|default:$record->title}</h1>
		<h3>{$record->website}</h3>
		<p class="description">
			{if $record->expFile.images[0] != ''}{img file_id=$record->expFile.images[0]->id constrain=1 width=150 height=100}{/if}
			{$record->body}
			{clear}
		</p>
		{filedisplayer files=$record->expFile.images title="ON THE JOB GALLERY"}
		<h3>Resource Downloads</h3>
	        <ul class="filelist">
        	{foreach from=$record->expFile.downloads item=file}
                	{if $file->name == ""}{assign var=name value=$file->filename}{else}{assign var=name value=$file->name}{/if}
	                <li>{getfileicon file=$file}
        	                <a href="{link action="downloadfile" id=$file->id}" title="{$name}">{$name}</a>
                	</li>
	        {/foreach}
        	</ul>
		{*filedisplayer files=$record->expFile.downloads title="RESOURCE DOWNLOADS" view=filelist*}
	</div>
	<div id="tag-related">
		<h2>Job Openings</h2>
		{chainByTags controller=job view="related" tags=$record->expTag}
		<h2>Careers</h2>
		{chainByTags controller=career view="related" tags=$record->expTag}
	</div>
	<div id="contactform">
		{chain module=contactmodule view=Default}
	</div>
</div>
