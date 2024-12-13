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

{css unique="exporteql" corecss="tables"}

{/css}

<div class="importer usercsv-display">
	<div class="form_header">
        <div class="info-header">
            <h2>{'Import Messages - Available Messages to Import'|gettext}</h2>
            <blockquote>{'The following messages can be added to the module.'|gettext}</blockquote>
        </div>
	</div>
    {form action="import_add"}
        {control type="hidden" name="content" value=$params.content}
        {control type="hidden" name="rowstart" value=$params.rowstart}
        {control type="hidden" name="filename" value=$filename}
        {control type="hidden" name="source" value=$source}
        <table cellspacing="0" cellpadding="2" border="0" width="100%" class="exp-skin-table">
            <thead>
                <tr>
                    <th class="header importer_header"><input type='checkbox' name='checkall' title="{'Select All/None'|gettext}" onchange="selectAll(this.checked)" checked=1> {'Add'|gettext}</th>
                    <th class="header importer_header">{'Month'|gettext}</th>
                    <th class="header importer_header">{'Day'|gettext}</th>
                    <th class="header importer_header">{'Message'|gettext}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$msgarray item=msg}
                    <tr class="{cycle values='even,odd'}">
                        <td>
                            {control type="checkbox" name="importmessage[]" label=" " value=$msg.linenum checked=true}
                        </td>
                        <td>
                            {if empty($msg.month)}
                                {'Any Month'|gettext}
                            {else}
                                {date('M', strtotime('2017-'|cat:$msg.month|cat:'-01'))}
                            {/if}
                        </td>
                        <td>{$msg.day}</td>
                        <td>{$msg.message}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        {control type="buttongroup" submit="Add Selected Messages"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="importmessages"}
{literal}
    function selectAll(val) {
        var checks = document.getElementsByName("importmessage[]");
        for (var i = 0; i < checks.length; i++) {
          if (!checks[i].disabled) checks[i].checked = val;
        }
    }
{/literal}
{/script}
