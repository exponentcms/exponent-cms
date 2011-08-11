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

<div class="exporter files-modlist">
	<div class="form_header">
		<h2>{'Select Which Modules'|gettext}</h2>
		<p>{'Listed below are the module types that have uploaded files.  Select which type of modules you want to export the files for.'|gettext}</p>
	</div>
	<script type="text/javascript">
	{literal}
	function mods_selectUnselectAll(setChecked) {
		var elems = document.getElementsByTagName("input");
		for (key = 0; key < elems.length; key++) {
			if (elems[key].type == "checkbox" && elems[key].name.substr(0,5) == "mods[") {
				elems[key].checked = setChecked;
			}
		}
	}
	{/literal}
	</script>
	<form method="post" action="">
		<input type="hidden" name="module" value="exporter" />
		<input type="hidden" name="action" value="page" />
		<input type="hidden" name="exporter" value="files" />
		<input type="hidden" name="page" value="export" />
		<table cellspacing="0" cellpadding="2" border="0">
			<tr><td class="header">&nbsp;</td><td class="header">{'Module'|gettext}</td></tr>
			{foreach from=$mods key=modname item=modulename}
				<tr>
					<td>
						<input type="checkbox" name="mods[{$modname}]" />
					</td>
					<td>
						{$modulename}
					</td>
				</tr>
			{/foreach}
			<tr><td colspan="2">
				<a href="#" onclick="mods_selectUnselectAll(true); return false;">{'Select All'|gettext}</a>&nbsp;|&nbsp;<a href="#" onclick="mods_selectUnselectAll(false); return false;">{'Unselect All'|gettext}</a>
			</td></tr>
			<tr>
				<td colspan="2" valign="top"><b>{'File Name Template:'|gettext}</b>
					<input type="text" name="filename" size="20" value="files" />
				</td>
			</tr>
				<td colspan="2">
					<div style="border-top: 1px solid #CCCC;">{'Use __DOMAIN__ for this website\'s domain name and any strftime options for time specification. The extension will be added for you. Any other text will be preserved.'|gettext}<br /></div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" type="submit" value="{'Export Files'|gettext}" />
				</td>
			</tr>
		</table>
	</form>
</div>