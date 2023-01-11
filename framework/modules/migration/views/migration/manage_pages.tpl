{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{css unique="managespages" corecss="tables"}

{/css}

<div class="module migration manage-pages">
 	{*<a class="{button_style}" href="{link module=migration action=manage_files}"><strong>{'Skip to Next Step -> Migrate Files'|gettext}</strong></a>*}
    {icon button=true module=migration action=manage_files text='Skip to Next Step -> Migrate Files'|gettext}
    {br}{br}<hr />
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help with"|gettext|cat:" "|cat:("Migrating Pages"|gettext) module="migrate-pages"}
        </div>
		<h2>{"Migrate Pages"|gettext}</h2>
        <blockquote>
            {'The following is a list of pages we found in the database'|gettext} {$config.database}.
            {'Select the pages you would like to pull over from'|gettext} {$config.database}.
        </blockquote>
    </div>

    {form action="migrate_pages"}
        <table class="exp-skin-table">
        <thead>
            <tr>
                <th><input type='checkbox' name='checkallp' title="{'Select All/None'|gettext}" onchange="selectAllp(this.checked)" checked=1> {'Migrate'|gettext}</th>
                <th><input type='checkbox' name='checkallr' title="{'Select All/None'|gettext}" onchange="selectAllr(this.checked)"> {'Replace'|gettext}</th>
                <th>{'Name'|gettext}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$pages item=page name=pages}
        <tr class="{cycle values="even,odd"}">
            <td>
				{if ($page->exists == true)}
					<em>({'exists'|gettext})</em>
				{else}
					{control type="checkbox" name="pages[]" label=" " value=$page->id checked=true}
				{/if}
            </td>
            <td>
				{if ($page->exists == true)}
					{control type="checkbox" name="rep_pages[]" label=" " value=$page->id checked=false}
				{else}
					<em>({'new'|gettext})</em>
				{/if}
            </td>
            <td>
                {$page->name} {if ($page->parent == -1)}(<strong><em>{'Standalone'|gettext}</em></strong>){/if}
            </td>
        </tr>
        {foreachelse}
			<tr><td colspan=>{'No new pages found in the database'|gettext} {$config.database}</td></tr>
        {/foreach}
        </tbody>
        </table>
		{control type="checkbox" name="copy_permissions" label="Migrate page permissions"|gettext|cat:"? ("|cat:("erases current page permissions"|gettext)|cat:"!)" value=1 checked=false}
        {control type="checkbox" name="wipe_pages" label="Erase all current pages and then try again"|gettext|cat:"?" value=1 checked=false}
        {control type="buttongroup" submit="Migrate Pages"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="managepages"}
    function selectAllp(val) {
        var checks = document.getElementsByName("page[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }

    function selectAllr(val) {
        var checks = document.getElementsByName("rep_pages[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/script}
