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

<div class="module simplenote manage">
    <h1>Manage Notes</h1>
    <p>The table below shows notes have not yet been approved.</p>
    
    <table class="exp-skin-table">
    <thead>
        <tr>
            {$page->header_columns}
            <th>&nbsp</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$page->records item=simplenote}
        <tr class="{cycle values="even, odd"}">
            <td>
                {if $simplenote->approved == 1}
                    <a href="{link action=approve_toggle id=$simplenote->id tab=$tab}" title="Disable this note">
                        {img src=`$smarty.const.ICON_RELATIVE`toggle_on.gif}
                    </a>
                {else}
                    <a href="{link action=approve_toggle id=$simplenote->id tab=$tab}" title="Approve this note">
                        {img src=`$smarty.const.ICON_RELATIVE`toggle_off.gif}
                    </a>   
                {/if}  
            </td>
            <td>{$simplenote->name}</td>
            <td>{$simplenote->body}</td>
            <td>
                {icon img=edit.png action=approve id=$simplenote->id tab=$tab title="Edit Note"}
                {icon img=delete.png action=delete id=$simplenote->id tab=$tab title="Delete note" onclick="return confirm('Are you sure you want to delete this note?');"}
            </td>
        </tr>
        {foreachelse}
        <tr><td>There are no notes awaiting approval</td></tr>
        {/foreach}
    </tbody>
    </table>        
</div>
