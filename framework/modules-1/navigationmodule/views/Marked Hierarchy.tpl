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
 <table cellpadding="1" cellspacing="0" border="0" width="100%">
{foreach from=$sections item=section}
<tr><td style="padding-left: {math equation="x*20" x=$section->depth}px">
{if $section->id == $current->id}
<img src="{$smarty.const.ICON_RELATIVE}mark.gif" title="{'Mark'|gettext}" alt="{'Mark'|gettext}" />
{/if}
{if $section->active == 1}
<a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>&nbsp;
{else}
<span class="navlink">{$section->name}</span>&nbsp;
{/if}
</td></tr>
{/foreach}
</table>
{permissions}
{if $canManage == 1}
[ <a class="navlink" href="{link action=manage}">{'Manage'|gettext}</a> ]
{/if}
{/permissions}