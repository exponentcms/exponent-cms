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
<div class="moduletitle">{$template->name}</div>
<hr size="1" />
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td><b>&lt;{$_TR.name}&gt;</b></td>
<td>
[ <a class="mngmntlink sitetemplate_mngmntlink" href="{link action=edit_template parent=$template->id}">{$_TR.subpage}</a> ]
[ <a class="mngmntlink sitetemplate_mngmntlink" href="{link action=edit_template id=$template->id}">{$_TR.properties}</a> ]
[ <a class="mngmntlink sitetemplate_mngmntlink" href="#" onclick="window.open('{$smarty.const.URL_FULL}edit_page.php?sitetemplate_id={$template->id}'); return false">{$_TR.content}</a> ]
</td>
{foreach from=$subs item=sub}
{math equation="x+1" x=$sub->rank assign=nextrank}
{math equation="x-1" x=$sub->rank assign=prevrank}
<tr>
<td style="padding-left: {math equation="x*20" x=$sub->depth}">
<b>{$sub->name}</b>
</td>
<td>
[ <a class="mngmntlink sitetemplate_mngmntlink" href="{link action=edit_template parent=$sub->id}">{$_TR.subpage}</a> ]
[ <a class="mngmntlink sitetemplate_mngmntlink" href="{link action=edit_template id=$sub->id}">{$_TR.properties}</a> ]
[ <a class="mngmntlink sitetemplate_mngmntlink" href="#" onclick="window.open('{$smarty.const.URL_FULL}edit_page.php?sitetemplate_id={$sub->id}'); return false">{$_TR.content}</a> ]
[ <a class="mngmntlink sitetemplate_mngmntlink" href="{link action=delete_template id=$sub->id}">{$_TR.delete}</a> ]
{if $sub->last == 0}
	<a href="{link action=order_templates parent=$sub->parent a=$sub->rank b=$nextrank}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}down.png" title="{$_TR.alt_down}" alt="{$_TR.alt_down}" /></a>
{else}
	<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}down.disabled.png" title="{$_TR.alt_down_disabled}" alt="{$_TR.alt_down_disabled}" />
{/if}
{if $sub->first == 0}
	<a href="{link action=order_templates parent=$sub->parent a=$sub->rank b=$prevrank}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}up.png" title="{$_TR.alt_up}" alt="{$_TR.alt_up}" /></a>
{else}
	<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}up.disabled.png" title="{$_TR.alt_up_disabled}" alt="{$_TR.alt_up_disabled}" />
{/if}
</td>
</tr>
{/foreach}
</table>
<br />
<br />
<a class="mngmntlink navigation_mngmntlink" href="{link action=manage}">{$_TR.back}</a>
