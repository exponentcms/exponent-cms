{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

{css unique="importfiles" corecss="tables"}

{/css}

<div class="importer files-selectmodlist">
	<div class="form_header">
		<h2>{'Select Which Files to Import'|gettext}</h2>
	</div>
    {script unique="selectmod"}
	{literal}
	function mods_selectUnselectAll(setChecked) {
		var elems = document.getElementsByTagName("input");
		for (var key = 0; key < elems.length; key++) {
			if (elems[key].type == "checkbox" && elems[key].name.substr(0,5) == "mods[") {
				elems[key].checked = setChecked;
			}
		}
	}
	{/literal}
	{/script}
    {form action=import_files_extract}
        {control type="hidden" name="dest_dir" value=$dest_dir}
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="exp-skin-table">
			<thead>
                <tr>
                    <th colspan="2">{'Files found in this Archive'|gettext}</th>
                </tr>
			</thead>
			<tbody>
				{foreach from=$file_data item=mod_data}
					{foreach from=$mod_data[1] item=file}
						<tr class="{cycle values='even,odd'}">
							<td class="header" width="16">
                                <input type="checkbox" checked="checked" id="mods_{$file}" name="mods[{$file}]" />
                            </td>
							<td><label for="mods_{$file}}">{$file}</label></td>
						</tr>
					{/foreach}
				{/foreach}
                <tr><td colspan="2">
                    <a href="#" onclick="mods_selectUnselectAll(true); return false;">{'Select All'|gettext}</a>&#160;|&#160;<a href="#" onclick="mods_selectUnselectAll(false); return false;">{'Unselect All'|gettext}</a>
                </td></tr>
				<tr>
					<td colspan="2">
                        {*<input class="{button_style}" type="submit" value="{'Process'|gettext}" />*}
                        {control type=buttongroup submit='Process'|gettext}
                    </td>
				</tr>
			</tbody>
		</table>
    {/form}
</div>
