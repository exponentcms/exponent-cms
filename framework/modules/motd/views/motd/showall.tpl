{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

<div class="module motd showall">
    <h1>{$moduletitle|default:"Messages by day"}</h1>
    <div class="bodycopy">
        {$record->body}
    </div>
    
    {$page->links}
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.edit == 1}
            {icon class="add" action=create text="Add a New Message"}
      {/if}
    {/permissions}
    <table id="prods" class="exp-skin-table">
    <thead>
        <tr>
            {$page->header_columns}
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$page->records item=listing name=listings}
        <tr class="{cycle values="odd,even"}">
            <td>{$listing->month}/{$listing->day}</td>
            <td>{$listing->body}</td>
            <td>
                {permissions level=$smarty.const.UILEVEL_NORMAL}
                    {if $permissions.edit == 1}
                        {icon img=edit.png action=edit id=$listing->id title="Edit this message"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon img=delete.png action=delete id=$listing->id title="Delete this message" onclick="return confirm('Are you sure you want to delete this message?');"}
                    {/if}
                {/permissions}  
            </td>                   
        </tr>
        {foreachelse}
            <tr class="{cycle values="odd,even"}">
            <td colspan="6">
                There are no products in the this store yet.
            </td>                   
        </tr>
        {/foreach}
    </tbody>
    </table>
    {$page->links}
</div>
