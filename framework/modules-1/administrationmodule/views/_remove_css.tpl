{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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

<div class="administrationmodule remove-css">
	<h1>Removing {$file_type}</h1>
	{if $files.removed|@count > 0}
		<h3>{$files.removed|@count} files were removed.  The files are listed below.</h3>
		<ul>
			{foreach from=$files.removed item=file}
				<li>{$file}</li>
			{/foreach}
		</ul>
	{/if}

	{if $files.not_removed|@count > 0}
                <h3>The following {$files.not_removed|@count} files could not be removed.</h3>
                <ul>
                        {foreach from=$files.not_removed item=file}
                                <li>{$file}</li>
                        {/foreach}
                </ul>
        {/if}

	<a href="{link module=administrationmodule action=index}">{$_TR.back}</a>
</div>
