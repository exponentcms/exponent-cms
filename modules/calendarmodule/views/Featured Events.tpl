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
<div class="module calendarmodule featured-events">
     {if $moduletitle != ""}<h1>{$moduletitle}</h1>{/if}
    {foreach from=$items item=item}
    {if $item->is_featured}
        <h2><a href="{link action=view id=$item->id date_id=$item->eventdate->id}">{$item->title}</a></h2>
        <div class="bodycopy">
                 {$item->body|summarize:html:para}
                {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
                    {if $permissions.administrate == 1 || $item->permissions.administrate == 1}
                        <a class="mngmntlink calendar_mngmntlink" href="{link action=userperms int=$item->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}userperms.png" title="{$_TR.alt_userperm_one}" alt="{$_TR.alt_userperm_one}" /></a>
                        <a class="mngmntlink calendar_mngmntlink" href="{link action=groupperms int=$item->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}groupperms.png" title="{$_TR.alt_groupperm_one}" alt="{$_TR.alt_groupperm_one}" /></a>
                    {/if}
                {/permissions}
                {permissions level=$smarty.const.UILEVEL_NORMAL}
                    {if $permissions.edit == 1 || $item->permissions.edit == 1}
                        {if $item->approved == 1}
                        <a class="mngmntlink calendar_mngmntlink" href="{link action=edit id=$item->id date_id=$item->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}" /></a>
                        {else}
                        <img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.disabled.png" title="{$_TR.alt_edit_disabled}" alt="{$_TR.alt_edit_disabled}" />
                        {/if}
                    {/if}
                    {if $permissions.delete == 1 || $item->permissions.delete == 1}
                        {if $item->approved == 1}
                        {if $item->is_recurring == 0}
                        <a class="mngmntlink calendar_mngmntlink" href="{link action=delete id=$item->id}" onclick="return confirm('{$_TR.delete_confirm}');"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
                        {else}
                        <a class="mngmntlink calendar_mngmntlink" href="{link action=delete_form id=$item->id date_id=$item->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
                        {/if}
                        {else}
                        <img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.disabled.png" title="{$_TR.alt_delete_disabled}" alt="{$_TR.alt_delete_disabled}" />
                        {/if}
                    {/if}
                    {if $permissions.manage_approval == 1}
                        <a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=revisions_view id=$item->id}" title="{$_TR.alt_revisions}" alt="{$_TR.alt_revisions}">{$_TR.alt_revisions}</a>
                    {/if}
                {/permissions}
            </div>

        {/if}
    {foreachelse}
        <div>{$_TR.no_event}</div>
    {/foreach}
    {permissions level=$smarty.const.UILEVEL_NORMAL}
    {if $permissions.post == 1}
    <a class="mngmntlink calendar_mngmntlink" href="{link action=edit id=0}" title="{$_TR.alt_create}" alt="{$_TR.alt_create}">{$_TR.create}</a>
    {/if}
    <br />
    {if $in_approval != 0 && $canview_approval_link == 1}
    <a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=summary}" title="{$_TR.alt_approval}" alt="{$_TR.alt_approval}">{$_TR.approval}</a>
    {/if}
    {if $modconfig->enable_categories == 1}
    {if $permissions.administrate == 1}
    <br />
    <a href="{link module=categories orig_module=calendarmodule action=manage}" class="mngmntlink calendar_mngmntlink">{$_TR.manage_categories}</a>
    {else}
    <br />
    <a class="mngmntlink calendar_mngmntlink" href="#" onclick="window.open('{$smarty.const.PATH_RELATIVE}popup.php?module=categories&m={$__loc->mod}&action=view&src={$__loc->src}','legend','width=200,height=200,title=no,status=no'); return false" title="{$_TR.alt_view_cat}" alt="{$_TR.alt_view_cat}">{$_TR.view_categories}</a>
    {/if}
    {/if}
    {/permissions}
    
</div>
