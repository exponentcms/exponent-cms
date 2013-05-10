{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{css unique="exporteql" corecss="forms,tables"}

{/css}

{messagequeue}
<div class="importer usercsv-display">
	<div class="form_header">
		<h2>{'Import Form Data - Available Records to Import'|gettext}</h2>
		<blockquote>{'The following records can be added to the database.'|gettext}</blockquote>
	</div>
    {form action="import_csv_data_add"}
        {control type="hidden" name="filename" value=$params.filename}
        {control type="hidden" name="delimiter" value=$params.delimiter}
        {control type="hidden" name="rowstart" value=$params.rowstart}
        {control type="hidden" name="forms_id" value=$params.forms_id}
        {foreach from=$params.column key=k item=column}
            {control type="hidden" name="column[`$k`]" value=$column}
        {/foreach}
        <table cellspacing="0" cellpadding="2" border="0" width="100%" class="exp-skin-table">
            <thead>
                <th class="header importer_header"><input type='checkbox' name='checkall' title="{'Select All/None'|gettext}" onchange="selectAll(this.checked)" checked=1> {'Add'|gettext}</th>
                <th class="header importer_header">{'Status'|gettext}</th>
                {foreach from=$params.caption item=caption}
                    <th class="header importer_header">{$caption}</th>
                {/foreach}
            </thead>
            <tbody>
                {foreach from=$records item=record}
                    <tr class="{cycle values='even,odd'}">
                        <td>
                            {if $record.changed == "skipped"}
                                {control type="checkbox" name="importrecord[]" label=" " disabled=true}
                            {else}
                                {control type="checkbox" name="importrecord[]" label=" " value=$record.linenum checked=true}
                            {/if}
                        </td>
                        <td>
                            {if $record.changed == 1}<span style="color:green;">{'Update'|gettext}</span>
                            {elseif $record.changed == "skipped"}<span style="color:red;">{'Ignore&#160;(Line&#160;%s)'|sprintf:$record.linenum})</span>
                            {else}<span style="color:black;">{'Add'|gettext}</span>
                            {/if}
                        </td>
                        {foreach from=$record key=key item=field}
                            {if $key != 'linenum'}
                                <td>{$field}</td>
                            {/if}
                        {/foreach}
                    </tr>
                {/foreach}
            </tbody>
        </table>
        {control type="buttongroup" submit="Add Selected Records"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="importrecords"}
    function selectAll(val) {
        var checks = document.getElementsByName("importrecord[]");
        for (var i = 0; i < checks.length; i++) {
          if (!checks[i].disabled) checks[i].checked = val;
        }
    }
{/script}
