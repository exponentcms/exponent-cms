{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div class="module migration manage-files">
	<h1>{"Migrate Files"|gettext}</h1>
    <p> 
		This copies the list of files found in the database ({$config.database}). 
		Note: this will only properly copy over the records from the old database into the Exponent v2 database. 
		Make sure you manually copy the "files" directory over to this installation.
        <span class="warning">
            {br}WARNING: This process will wipe out all current file records in the database.
        </span>
    </p>
    
    {form action="migrate_files"}
        <table>
			<tbody>
				<tr><td>{if $count > 0}{$count}{else}No{/if} files found in the database '{$config.database}'</td>
			</tbody>
        </table>
        {control type="buttongroup" submit="Migrate Files" cancel="Cancel"}
    {/form}
</div>