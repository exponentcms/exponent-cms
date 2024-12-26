{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{css unique="standalone" corecss="tables"}

{/css}

<div class="module navigation manager-standalone">
	<div class="form_header">
		<blockquote>{'Standalone pages do not appear in the site hierarchy, but still have their own content and act just like other pages.'|gettext}</blockquote>
		{icon class="add" action=edit_contentpage parent=-1 text='Create a New Standalone Page'|gettext}
	</div>

    {form action=delete_standalones}
        <table cellpadding="2" cellspacing="0" border="0" width="100%" class="exp-skin-table">
            <thead>
                <tr>
                    <th><input type='checkbox' name='checkall' title="{'Select All/None'|gettext}" style="margin-left: 1px;" onchange="selectAll(this.checked)"></th>
                    <th><strong>{'Page Title'|gettext}</strong></th>
                    <th><strong>{'Actions'|gettext}</strong></th>
                    {if !$smarty.const.SIMPLE_PERMISSIONS}
                    <th><strong>{'Permissions'|gettext}</strong></th>
                    {/if}
                </tr>
            </thead>
            <tbody>
                {foreach from=$sasections item=section}
                    <tr class="{cycle values='odd,even'}">
                        <td width="20">
                            {control type="checkbox" name="deleteit[]" value=$section->id}
                        </td>
                    <td>
                        {if $section->active}
                            <a href="{link section=$section->id}" class="navlink" title="{'View this Page'|gettext}">{$section->name}</a>&#160;
                        {else}
                            {$section->name}&#160;
                        {/if}
                    </td><td>
                        {icon class=edit action=edit_contentpage record=$section title='Edit'|gettext}
                        {icon action=delete record=$section title='Delete'|gettext onclick="return confirm('"|cat:("Delete this page?"|gettext)|cat:"');"}
                    </td>
                    {if !$smarty.const.SIMPLE_PERMISSIONS}
                    <td>
                        {*{icon int=$section->id action=userperms _common=1 img='userperms.png' title='Assign user permissions for this Page'|gettext text="User"}*}
                        {*{icon int=$section->id action=groupperms _common=1 img='groupperms.png' title='Assign group permissions for this Page'|gettext text="Group"}*}
                        {icon controller=users action=userperms mod=navigation int=$section->id img='userperms.png' title='Assign user permissions for this Page'|gettext text="User"}
                        {icon controller=users action=groupperms mod=navigation int=$section->id img='groupperms.png' title='Assign group permissions for this Page'|gettext text="Group"}
                    </td>
                    {/if}
                    </tr>
                {foreachelse}
                    <tr><td colspan=4><em>{'No standalone pages found'|gettext}</em></td></tr>
                {/foreach}
            </tbody>
        </table>
        {control class=delete type=buttongroup submit="Delete Selected Pages"|gettext color=red onclick="return confirm('"|cat:("Are you sure you want to delete these pages?"|gettext)|cat:"');"}
    {/form}
</div>

{script unique="standalone"}
{literal}
    function selectAll(val) {
        var checks = document.getElementsByName("deleteit[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/literal}
{/script}
