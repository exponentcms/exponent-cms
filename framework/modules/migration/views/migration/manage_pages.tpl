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

<div class="module migration manage-pages">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help with Migrating Pages" module="migrate-pages"}
        </div>
		<h1>{"Migrate Pages"|gettext}</h1>	    
    </div>

    <p> 
        The following is a list of pages we found in the database {$config.database}.
        Select the pages you would like to pull over from {$config.database}.
    </p>
    {form action="migrate_pages"}
        <table class="exp-skin-table">
        <thead>
            <tr>
                <th>Migrate</th>
                <th>Replace</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$pages item=page name=pages}
        <tr class="{cycle values="even,odd"}">            
            <td>
				{if ($page->exists == true)}
					<em>(exists)</em>
				{else}
					{control type="checkbox" name="pages[]" label=" " value=$page->id checked=true}
				{/if}
            </td>
            <td>
				{if ($page->exists == true)}
					{control type="checkbox" name="rep_pages[]" label=" " value=$page->id checked=false}
				{else}
					<em>(new)</em>
				{/if}
            </td>
            <td>
                {$page->name} {if ($page->parent == -1)}(<b><em>Standalone</em></b>){/if}
            </td>
        </tr>
        {foreachelse}
			<tr><td colspan=>No new pages found in the database {$config.database}</td></tr>
        {/foreach}
        </tbody>
        </table>
        {control type="checkbox" name="wipe_pages" label="Erase all current pages and then try again?" value=1 checked=false}
        {control type="buttongroup" submit="Migrate Pages" cancel="Cancel"}
    {/form}
	<a class="admin" href="{link module=migration action=manage_files}">Next Step -> Migrate Files</a>
</div>