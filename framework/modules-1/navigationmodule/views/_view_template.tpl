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
        <td><b>&lt;{'Name of Section'|gettext}&gt;</b></td>
        <td>
            [ <a class="mngmntlink sitetemplate_mngmntlink" href="{link action=edit_template parent=$template->id}">{'Add Subpage'|gettext}</a> ]
            [ <a class="mngmntlink sitetemplate_mngmntlink" href="{link action=edit_template id=$template->id}">{'Properties'|gettext}</a> ]
            [ <a class="mngmntlink sitetemplate_mngmntlink" href="#" onclick="window.open('{$smarty.const.URL_FULL}edit_page.php?sitetemplate_id={$template->id}'); return false">{'Page Content'|gettext}</a> ]
        </td>
    </tr>
        {foreach from=$subs item=sub}
            {math equation="x+1" x=$sub->rank assign=nextrank}
            {math equation="x-1" x=$sub->rank assign=prevrank}
            <tr>
                <td style="padding-left: {math equation="x*20" x=$sub->depth}">
                    <b>{$sub->name}</b>
                </td>
                <td>
                    [ <a class="mngmntlink sitetemplate_mngmntlink" href="{link action=edit_template parent=$sub->id}">{'Add Subpage'|gettext}</a> ]
                    [ <a class="mngmntlink sitetemplate_mngmntlink" href="{link action=edit_template id=$sub->id}">{'Properties'|gettext}</a> ]
                    [ <a class="mngmntlink sitetemplate_mngmntlink" href="#" onclick="window.open('{$smarty.const.URL_FULL}edit_page.php?sitetemplate_id={$sub->id}'); return false">{'Page Content'|gettext}</a> ]
                    [ <a class="mngmntlink sitetemplate_mngmntlink" href="{link action=delete_template id=$sub->id}">{'Delete'|gettext}</a> ]
                    {if $sub->last == 0}
                        <a href="{link action=order_templates parent=$sub->parent a=$sub->rank b=$nextrank}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'down.png'}" title="{'Move Item Down'|gettext}" alt="{'Move Item Down'|gettext}" /></a>
                    {else}
                        <img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'down.disabled.png'}" title="{''|gettext}" alt="{''|gettext}" />
                    {/if}
                    {if $sub->first == 0}
                        <a href="{link action=order_templates parent=$sub->parent a=$sub->rank b=$prevrank}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'up.png'}" title="{'Move Item Up'|gettext}" alt="{'Move Item Up'|gettext}" /></a>
                    {else}
                        <img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'up.disabled.png'}" title="{''|gettext}" alt="{''|gettext}" />
                    {/if}
                </td>
            </tr>
        {/foreach}
</table>
<br />
<br />
<a class="mngmntlink navigation_mngmntlink" href="{link action=manage}">{'Back to Manager'|gettext}</a>
