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
 
{css unique="permissions" corecss="tables"}

{/css}

<form method="post">
<input type="hidden" name="module" value="{$__loc->mod}" />
<input type="hidden" name="src" value="{$__loc->src}" />
<input type="hidden" name="int" value="{$__loc->int}" />
{if $user_form == 1}<input type="hidden" name="action" value="saveuserperms" />
{else}<input type="hidden" name="action" value="savegroupperms" />
{/if}
<input type="hidden" name="_common" value="1" />

{$page->links}
<div style="overflow : auto; overflow-y : hidden;">
<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
    <thead>
        <tr>
            {$page->header_columns}
        </tr>
    </thead>
    <tbody>
        {foreach from=$page->records item=user key=ukey name=user}
        <tr class="{cycle values="even,odd"}">    

            {if !$is_group}
            <td>
                {$user->username}
            </td>
            <td>
                {$user->firstname}
            </td>
            <td>
                {$user->lastname}
            </td>
            {else}
            <td>
                {$user->name}
            </td>
            {/if}

            {foreach from=$perms item=perm key=pkey name=perms}
                <td>
                    <input type="checkbox"{if $user->$pkey==1||$user->$pkey==2} checked{/if} name="permdata[{$user->id}][{$pkey}]" value="1"{if $user->$pkey==2} disabled=1{/if} id="permdata[{$user->id}][{$pkey}]">
                </td>
            {/foreach}
        </tr>
        {/foreach}
    </tbody>
</table>
</div>
{$page->links}

{control type="buttongroup" submit="Save Permissions"|gettext cancel="Cancel"|gettext}
</form>