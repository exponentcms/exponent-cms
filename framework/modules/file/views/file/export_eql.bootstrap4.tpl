{*
 * Copyright (c) 2004-2020 OIC Group, Inc.
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

<div class="exporter eql-tablelist">
	<div class="form_header">
		<h2>{'Backup Current Database'|gettext}</h2>
		<blockquote>{'Listed below are all of the tables in your site\'s database.  Select which tables you wish to backup, and then click the \'Export Data\' button.  Doing so will generate an EQL file (which you must save) that contains the data in the selected tables.  This file can be used later to restore the database to this saved state.'|gettext}</blockquote>
	</div>
    {script unique="tablelist"}
	{literal}
	function selectAll(checked) {
		var elems = document.getElementsByTagName("input");
		for (var key in elems) {
			if (elems[key].type == "checkbox" && elems[key].name.substr(0,7) == "tables[") {
				elems[key].checked = checked;
			}
		}
	}

//	function isOneSelected() {
//		var elems = document.getElementsByTagName("input");
//		for (var key in elems) {
//			if (elems[key].type == "checkbox" && elems[key].name.substr(0,7) == "tables[") {
//				if (elems[key].checked) return true;
//			}
//		}
//		alert("{/literal}{'You must select at least one table to export.'|gettext}{literal}");
//		return false;
//	}

	{/literal}
	{/script}

	{form action=export_eql_process}
		<table cellspacing="0" cellpadding="2">
			{section name=tid loop=$tables step=2}
				<tr class="{cycle values='even,odd'}">
					<td>
						<input type="checkbox" id="tables_{$tables[tid]}" name="tables[{$tables[tid]}]" {if $tables[tid] != 'sessionticket' && $tables[tid] != 'search' && $tables[tid] != 'search_queries' && $tables[tid] != 'redirect'}checked {/if}/>
					</td>

					<td><label for="tables_{$tables[tid]}">{$tables[tid]}</label></td>

					<td style="width:12px">&#160;</td>

                    {$nextid=$smarty.section.tid.index+1}
					<td>
						{if $tables[$nextid] != ""}
                            <input type="checkbox" id="tables_{$tables[$nextid]}" name="tables[{$tables[$nextid]}]" {if $tables[$nextid] != 'sessionticket' && $tables[$nextid] != 'search' && $tables[$nextid] != 'search_queries' && $tables[$nextid] != 'redirect'}checked {/if}/>
                        {/if}
					</td>

					<td><label for="tables_{$tables[$nextid]}">{$tables[$nextid]}</label></td>
				</tr>
			{/section}
            <tr>
                <td colspan="5">&#160;</td>
            </tr>
			<tr>
				<td colspan="2">
					<a href="#" onclick="selectAll(true); return false">{'Select All'|gettext}</a>
				</td>
				<td></td>
				<td colspan="2">
					<a href="#" onclick="selectAll(false); return false">{'Unselect All'|gettext}</a>
				</td>
			</tr>
			<tr>
				<td colspan="5"><hr></td>
			</tr>
			{if $user->isAdmin()}
			<tr>
				<td>
					<input type="checkbox" name="save_sample" id="save_sample" value="1" class="checkbox">
				</td>
				<td colspan="3" valign="top">
					<strong><label class="label" for="save_sample">{'Save as Sample Content for the'|gettext} '{$smarty.const.DISPLAY_THEME}' {'Theme'|gettext}?</label></strong>
				</td>
			</tr>
			{/if}
			<tr>
				<td colspan="2" valign="top"><strong>{'File Name Template'|gettext}:</strong></td>
				<td colspan="3">
					{*<input type="text" name="filename" size="20" value="database" />*}
                    {control  type="text" name="filename" size="20" value="database"}
				</td>
			</tr>
			<tr>
				<td colspan="5">
					<div style="border-top: 1px solid #CCC;">{'Use __DOMAIN__ for this website\'s domain name, __DB__ for the site\'s database name and any strftime options for time specification. The EQL extension will be added for you. Any other text will be preserved.'|gettext}<br /></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">&#160;</td>
				<td colspan="3">
					{*<input type="submit" class="downloadfile {button_style}" value="{'Export Data'|gettext}" onclick="return isOneSelected();" />*}
                    {control type=buttongroup class="downloadfile" submit='Export Data'|gettext}
				</td>
			</tr>
		</table>
	{/form}
</div>
