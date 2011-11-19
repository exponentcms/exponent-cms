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
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Migrating Files"|gettext) module="migrate-files"}
        </div>
		<h1>{"Migrate Files"|gettext}</h1>	    
    </div>

    <p> 
		{'This copies the list of files found in the database'|gettext} ({$config.database}).&nbsp;&nbsp;
        {'Note: this only properly copied over the records from the old database into the Exponent v2 database.'|gettext}&nbsp;&nbsp;
        {'Make sure you manually copy the \'files\' directory over to this installation.'|gettext}
        <span class="warning">
            {br}{'WARNING: This process will wipe out all current file records in the database'|gettext}.
        </span>
    </p>
    {form action="migrate_files"}
        <table>
			<tbody>
				<tr><td>{if $count > 0}{$count}{else}No{/if} {'files found in the database'|gettext} '{$config.database}'</td>
			</tbody>
        </table>
        {control type="buttongroup" submit="Migrate Files"|gettext cancel="Cancel"|gettext}
    {/form}
	{br}<hr>{br}
	<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link module=migration action=manage_content}"><b>{'Next Step -> Migrate Content'|gettext}</b></a>
</div>