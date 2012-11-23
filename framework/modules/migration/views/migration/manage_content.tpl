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

{css unique="managecontent" corecss="tables"}

{/css}

<div class="module migration manage_content">
 	{br}<div class="admin"><strong>{'This is the Final Migration Step'|gettext}</strong></div>
    {br}<hr />
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Migrating Content"|gettext) module="migrate-content"}
        </div>
		<h1>{"Migrate Content"|gettext}</h1>	    
    </div>
    <blockquote>
        {'The following is a list of modules we found in the database'|gettext} {$config.database}.
    </blockquote>
    {form action="migrate_content"}
        <table class="exp-skin-table">
			<thead>
				<tr>
					<th><input type='checkbox' name='checkallm' title="{'Select All/None'|gettext}" onchange="selectAllm(this.checked)" checked=1> {'Migrate'|gettext}</th>
					<th><input type='checkbox' name='checkallr' title="{'Select All/None'|gettext}" onchange="selectAllr(this.checked)"> {'Replace'|gettext}</th>
					<th>{'Module'|gettext}</th>
					<th>{'Count'|gettext}</th>
					<th>{'Action'|gettext}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$modules item=module name=modules}
					{if !$module->notmigrating}
						<tr class="{cycle values="even,odd"}">            
							<td><input type=checkbox name="migrate[]" label=" " value='{$module->module}' checked=1></td>
							<td><input type=checkbox name="replace[]" label=" " value='{$module->module}'></td>
							<td>{$module->module}</td>
							<td>{$module->count}</td>
							<td>{$module->action}</td>
						</tr>
					{/if}
				{foreachelse}
					<tr><td colspan=2>{'No modules found in the database'|gettext} {$config.database}</td></tr>
				{/foreach}
			</tbody>
        </table>
		{control type="checkbox" name="copy_permissions" label="Migrate content permissions"|gettext|cat:"? ("|cat:("erases current content permissions"|gettext)|cat:"!)" value=1 checked=false}
        {control type="checkbox" name="wipe_content" label="Erase all current content before import"|gettext|cat:"?" value=1 checked=false}
        {control type="buttongroup" submit="Migrate Content"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="managecontent"}
    function selectAllm(val) {
        var checks = document.getElementsByName("migrate[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }

    function selectAllr(val) {
        var checks = document.getElementsByName("replace[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/script}
