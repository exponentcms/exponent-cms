{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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

{css unique="cal1" corecss="tables"}

{/css}

<div class="importer files-selectmodlist">
	<div class="form_header">
		<h2>{'Select Which Files to Import'|gettext}</h2>
	</div>
	<script type="text/javascript">
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
	</script>
	<form method="post" action="">
		<input type="hidden" name="module" value="importer" />
		<input type="hidden" name="action" value="page" />
		<input type="hidden" name="importer" value="files" />
		<input type="hidden" name="page" value="extract" />
		<input type="hidden" name="dest_dir" value="{$dest_dir}" />

		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="exp-skin-table">
			<thead>
				<th colspan="2">Files found in this Archive</th>
			</thead>
			<tbody>
				{foreach from=$file_data item=mod_data}
					{foreach from=$mod_data[1] item=file}
						<tr class="row {cycle values=even_row,odd_row}">
							<td class="header" width="16"><input type="checkbox" checked="checked" name="mods[{$file}]" /></td>
							<td>{$file}</td>
						</tr>
					{/foreach}
				{/foreach}
			<tr><td colspan="2">
				<a href="#" onclick="mods_selectUnselectAll(true); return false;">{'Select All'|gettext}</a>&nbsp;|&nbsp;<a href="#" onclick="mods_selectUnselectAll(false); return false;">{'Unselect All'|gettext}</a>
			</td></tr>
				<tr>
					<td colspan="2"><input class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" type="submit" value="{'Process'|gettext}" /></td>
				</tr>
			</tbody>
		</table>
	</form>
</div>