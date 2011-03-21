{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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

{if $config.title}<h3>{$config.title}</h3>{/if}
<ul class="filelist">
{foreach from=$files item=file}
	<li>
		<a href="{link action="downloadfile" id=$file->id}" title="{$file->title}">{if $file->title!=""}{$file->title}{else}{$file->filename}{/if}</a>
	</li>
{/foreach}
</ul>
