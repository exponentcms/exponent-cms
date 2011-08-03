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
<div class="form_title">{$_TR.form_title}</div>
<div class="form_header">{$_TR.form_header}</div>
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
<tr><td colspan="3"><b>{$_TR.no_unused_tables}</b></td></tr>
{/foreach}
{if $droppable_count != 0}
<tr><td colspan="3">
<a href="#" onclick="selectAll('tables[',true); return false; ">{$_TR.select_all}</a>
&nbsp;&nbsp;|&nbsp;&nbsp;
<a href="#" onclick="selectAll('tables[',false); return false; ">{$_TR.deselect_all}</a>
<br />
<input type="submit" value="Delete Selected" onclick="{literal}if (isOneSelected('tables[')) { return true; } else { alert('{$_TR.select_one_table}'); return false; }{/literal}" />
</td></tr>
{/if}
</table>