{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{css unique="managesimplenotes" corecss="tables"}

{/css}

<div class="module simplenote manage">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Managing Notes"|gettext) module="manage-simple-notes"}
        </div>
        <h1>{'Manage Notes'|gettext}</h1>
    </div>
    <p>{'The table below shows notes have not yet been approved'|gettext}.</p>
    
    {$page->links}
    <table class="exp-skin-table">
    <thead>
        <tr>
            {$page->header_columns}
            <th>{'Actions'|gettext}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$page->records item=simplenote}
        <tr class="{cycle values="even, odd"}">
            <td>
                {if $simplenote->approved == 1}
                    <a href="{link action=approve_toggle id=$simplenote->id tab=$tab}" title="Disable this note"|gettext>
                        {img src=$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}
                    </a>
                {else}
                    <a href="{link action=approve_toggle id=$simplenote->id tab=$tab}" title="Approve this note"|gettext>
                        {img src=$smarty.const.ICON_RELATIVE|cat:'toggle_off.png'}
                    </a>   
                {/if}  
            </td>
            <td>{$simplenote->name}</td>
            <td>{$simplenote->body}</td>
            <td>
				<div class="item-actions">
					{icon class=edit action=approve record=$simplenote tab=$tab title="Edit this note"|gettext}
					{icon action=delete record=$simplenote tab=$tab title="Delete this note"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this note?"|gettext)|cat:"');"}
				</div>
            </td>
        </tr>
        {foreachelse}
        <tr><td>{'There are no notes awaiting approval'|gettext}</td></tr>
        {/foreach}
    </tbody>
    </table>        
    {$page->links}
</div>
