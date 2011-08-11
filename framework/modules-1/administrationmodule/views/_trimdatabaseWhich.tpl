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
<div class="form_title">{'Trim Database'|gettext}</div>
<div class="form_header">{'Exponent has examined the database and determined which tables are no longer being used. Please select which ones you want to remove from the database.'|gettext}</div>
<form method="post" action="">
<input type="hidden" name="module" value="administrationmodule" />
<input type="hidden" name="action" value="trimdatabase_final" />
<table cellpadding="2" cellspacing="0" width="100%" border="0">
{foreach from=$droppable_tables item=rowcount key=table}
<tr class="row {cycle values='odd,even'}_row">
<td><input type="checkbox" name="tables[{$table}]" {if $rowcount == 0}checked {/if}/></td>
<td>
{$table}
</td>
<td>
{$rowcount} {plural singular=Record plural=Records count=$rowcount}
</td></tr>
{foreachelse}
<tr><td colspan="3"><b>{'No unused tables were found.'|gettext}</b></td></tr>
{/foreach}
{if $droppable_count != 0}
<tr><td colspan="3">
<a href="#" onclick="selectAll('tables[',true); return false; ">{'Select All'|gettext}</a>
&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="#" onclick="selectAll('tables[',false); return false; ">{'Unselect All'|gettext}</a>
<br />
<input type="submit" value="Delete Selected" onclick="{literal}if (isOneSelected('tables[')) { return true; } else { alert('{'You must select at least one table.'|gettext}'); return false; }{/literal}" />
</td></tr>
{/if}
</table>