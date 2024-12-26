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

<div class="importer import_csv">
	<div class="form_header">
		<h2>{'Import Message of the Day - CSV Options'|gettext}</h2>
		<blockquote>{'Please enter the row within the csv file to start at. The start row is for files that have column headers, or if you just want to skip records in the csv file.'|gettext}</blockquote>
	</div>
    <div>
        {form action=import_select}
            {control type=uploader name=upload label='CSV File to Upload'|gettext}
            {control type="dropdown" name="content" label="Type of Content"|gettext values='message,day_message,month_day_message' items="Message Only,Message and Day,Message including Day and Month"|gettxtlist default='month_day_message'}
            {control type="text" name="rowstart" label="Message Data begins in Row"|gettext value='1' size=6}
            <label>{'Module to import into'|gettext}</label>
            <table class="exp-skin-table aggregate">
                <thead>
                    <tr>
                        {$modules->header_columns}
                    </tr>
                </thead>
                <tbody>
                {foreach from=$modules->records item=mod}
                    <tr class="{cycle values="even,odd"}">
                        <td>
                            {control type="checkbox" name="import_aggregate[]" value=$mod->src label=$mod->title}
                        </td>
                        <td>
                            {$mod->section}
                        </td>
                    </tr>
                {foreachelse}
                    <tr><td colspan=3>{'There doesn\'t appear to be any modules of this type installed to import items'|gettext}</td></tr>
                {/foreach}
                </tbody>
            </table>
            {control type=buttongroup submit="Next"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>
