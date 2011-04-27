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
<div class="exporter eql-tablelist">
<div class="form_header">
        <h1>{$_TR.form_title}</h1>
        <p>{$_TR.form_header}</p>
</div>
<script type="text/javascript">
{literal}
function selectAll(checked) {
	elems = document.getElementsByTagName("input");
	for (var key in elems) {
		if (elems[key].type == "checkbox" && elems[key].name.substr(0,7) == "tables[") {
			elems[key].checked = checked;
		}
	}
}

function isOneSelected() {
	elems = document.getElementsByTagName("input");
	for (var key in elems) {
		if (elems[key].type == "checkbox" && elems[key].name.substr(0,7) == "tables[") {
			if (elems[key].checked) return true;
		}
	}
	alert("{/literal}{$_TR.at_least_one}{literal}");
	return false;
}

{/literal}
</script>

<form method="post" action="{$smarty.const.URL_FULL}index.php">
<input type="hidden" name="module" value="exporter" />
<input type="hidden" name="action" value="page" />
<input type="hidden" name="exporter" value="eql" />
<input type="hidden" name="page" value="savefile" />

<table cellspacing="0" cellpadding="2">
{section name=tid loop=$tables step=2}
<tr class="row {cycle values=even_row,odd_row}">
	<td>
		<input type="checkbox" name="tables[{$tables[tid]}]" {if $tables[tid] != 'sessionticket'}checked {/if}/>
	</td>
	
	<td>{$tables[tid]}</td>
	
	<td width="12">&nbsp;</td>
	
	{math equation="x+1" x=$smarty.section.tid.index assign=nextid}
	<td>
		{if $tables[$nextid] != ""}<input type="checkbox" name="tables[{$tables[$nextid]}]" {if $tables[$nextid] != 'sessionticket'}checked {/if}/>{/if}
	</td>
	
	<td>{$tables[$nextid]}</td>
</tr>
{/section}
<tr>
	<td colspan="2">
		<a href="#" onclick="selectAll(true); return false">{$_TR.select_all}</a>
	</td>
	<td></td>
	<td colspan="2">
		<a href="#" onclick="selectAll(false); return false">{$_TR.deselect_all}</a>
	</td>
</tr>
<tr>
	<td colspan="5"><br /></td>
</td>
<tr>
	<td colspan="2" valign="top"><b>{$_TR.file_template}</b></td>
	<td colspan="3">
		<input type="text" name="filename" size="20" value="database" />
	</td>
</tr>
	<td colspan="5">
		<div style="border-top: 1px solid #CCCC;">{$_TR.template_description}<br /></div>
	</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
	<td colspan="3">
		<input type="submit" value="{$_TR.export_data}" onclick="return isOneSelected();" />
	</td>
</tr>
</table>
</form>
</div>
