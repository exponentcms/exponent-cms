{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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
{foreach from=$dates item=d}
<tr class="row {cycle values='even_row,odd_row'}">
	<td width="10">
		<input type="checkbox" name="dates[{$d->id}]" {if $d->id == $checked_date->id}checked="checked" {/if}/>
	</td>
	<td>
		{$d->date|format_date:$smarty.const.DISPLAY_DATE_FORMAT}
	</td>
</tr>
{/foreach}
<tr>
	<td colspan="2">
	{literal}
		<script type="text/javascript">
		function recur_selectUnselectAll(setChecked) {
			var elems = document.getElementsByTagName("input")
			for (key = 0; key < elems.length; key++) {
				if (elems[key].type == "checkbox" && elems[key].name.substr(0,6) == "dates[") {
					elems[key].checked = setChecked;
				}
			}
		}
		</script>
	{/literal}
		<a class="mngmntlink calendar_mngmntlink" href="#" onclick="recur_selectUnselectAll(true); return false;">{$_TR.select_all}</a>
		&nbsp;/&nbsp;
		<a class="mngmntlink calendar_mngmntlink" href="#" onclick="recur_selectUnselectAll(false); return false;">{$_TR.deselect_all}</a>
	</td>
</tr>