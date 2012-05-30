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

<div class="exporter eql-tablelist">
	<div class="form_header">
		<h2>{'Backup Current Database'|gettext}</h2>
		<p>{'Listed below are all of the tables in your site\'s database.  Select which tables you wish to backup, and then click the \'Export Data\' button.  Doing so will generate an EQL file (which you must save) that contains the data in the selected tables.  This file can be used later to restore the database to the current state.'|gettext}</p>
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

	function isOneSelected() {
		var elems = document.getElementsByTagName("input");
		for (var key in elems) {
			if (elems[key].type == "checkbox" && elems[key].name.substr(0,7) == "tables[") {
				if (elems[key].checked) return true;
			}
		}
		alert("{/literal}{'You must select at least one table to export.'|gettext}{literal}");
		return false;
	}

	{/literal}
	{/script}

	<form method="post" action="{$smarty.const.PATH_RELATIVE}index.php">
		<input type="hidden" name="module" value="exporter" />
		<input type="hidden" name="action" value="page" />
		<input type="hidden" name="exporter" value="eql" />
		<input type="hidden" name="page" value="savefile" />
		<table cellspacing="0" cellpadding="2">
			{section name=tid loop=$tables step=2}
				<tr class="row {cycle values='even_row,odd_row'}">
					<td>
						<input type="checkbox" name="tables[{$tables[tid]}]" {if $tables[tid] != 'sessionticket' && $tables[tid] != 'search'}checked {/if}/>
					</td>

					<td>{$tables[tid]}</td>

					<td width="12">&nbsp;</td>

					{math equation="x+1" x=$smarty.section.tid.index assign=nextid}
					<td>
						{if $tables[$nextid] != ""}<input type="checkbox" name="tables[{$tables[$nextid]}]" {if $tables[$nextid] != 'sessionticket' && $tables[$nextid] != 'search'}checked {/if}/>{/if}
					</td>

					<td>{$tables[$nextid]}</td>
				</tr>
			{/section}
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
				<td colspan="1">
					<input type="checkbox" name="save_sample" value="1" class="checkbox">
				</td>
				<td colspan="4" valign="top">
					<strong><label class="label ">{'Save as Sample Content for the'|gettext} '{$smarty.const.DISPLAY_THEME}' {'Theme'|gettext}?</label></strong>
				</td>
			</tr>
			{/if}
			<tr>
				<td colspan="3" valign="top"><strong>{'File Name Template'|gettext}:</strong></td>
				<td colspan="2">
					<input type="text" name="filename" size="20" value="database" />
				</td>
			</tr>
			<tr>
				<td colspan="5">
					<div style="border-top: 1px solid #CCCC;">{'Use __DOMAIN__ for this website\'s domain name, __DB__ for the site\'s database name and any strftime options for time specification. The EQL extension will be added for you. Any other text will be preserved.'|gettext}<br /></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td colspan="3">
					<input class="downloadfile awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" type="submit" value="{'Export Data'|gettext}" onclick="return isOneSelected();" />
				</td>
			</tr>
		</table>
	</form>
</div>
