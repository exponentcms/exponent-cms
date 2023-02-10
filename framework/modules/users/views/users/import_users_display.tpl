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

{css unique="exporteql" corecss="tables"}

{/css}

<div class="importer usercsv-display">
	<div class="form_header">
        <div class="info-header">
            <h2>{'Import Users - Available Users to Import'|gettext}</h2>
            <blockquote>{'The following users can be added to the database.'|gettext}</blockquote>
        </div>
	</div>
    {form action="import_users_add"}
        {control type="hidden" name="filename" value=$params.filename}
        {control type="hidden" name="delimiter" value=$params.delimiter}
        {control type="hidden" name="rowstart" value=$params.rowstart}
        {foreach from=$params.column key=k item=column}
            {control type="hidden" name="column[`$k`]" value=$column}
        {/foreach}
        {control type="hidden" name="unameOptions" value=$params.unameOptions}
        {control type="hidden" name="pwordOptions" value=$params.pwordOptions}
        {control type="hidden" name="pwordText" value=$params.pwordText}
        {control type="hidden" name="update" value=$params.update}
        <table cellspacing="0" cellpadding="2" border="0" width="100%" class="exp-skin-table">
            <thead>
                <tr>
                    <th class="header importer_header"><input type='checkbox' name='checkall' title="{'Select All/None'|gettext}" onchange="selectAll(this.checked)" checked=1> {'Add'|gettext}</th>
                    <th class="header importer_header">{'Status'|gettext}</th>
                    <th class="header importer_header">{'Username'|gettext}</th>
                    <th class="header importer_header">{'First Name'|gettext}</th>
                    <th class="header importer_header">{'Last Name'|gettext}</th>
                    <th class="header importer_header">{'Email'|gettext}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$userarray item=user}
                    <tr class="{cycle values='even,odd'}">
                        <td>
                            {if $user.changed == "skipped"}
                                {control type="checkbox" name="importuser[]" label=" " disabled=true}
                            {else}
                                {control type="checkbox" name="importuser[]" label=" " value=$user.linenum checked=true}
                            {/if}
                        </td>
                        <td>
                            {if $user.changed == 1}<span style="color:green;">{'Updated'|gettext}</span>
                            {elseif $user.changed == "skipped"}<span style="color:red;">{'Ignored'|gettext} ({'Line'|gettext} {$user.linenum})</span>
                            {else}<span style="color:black;">{'Added'|gettext}</span>
                            {/if}
                        </td>
                        <td>{$user.username}</td>
                        <td>{$user.firstname}</td>
                        <td>{$user.lastname}</td>
                        <td>{$user.email}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        {if $smarty.const.USER_REGISTRATION_SEND_WELCOME}
            {control type="checkbox" name="sendemail" label="Send Welcome Email to Users?"|gettext}
        {/if}
        {control type="buttongroup" submit="Add Selected Users"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="importusers"}
{literal}
    function selectAll(val) {
        var checks = document.getElementsByName("importuser[]");
        for (var i = 0; i < checks.length; i++) {
          if (!checks[i].disabled) checks[i].checked = val;
        }
    }
{/literal}
{/script}
