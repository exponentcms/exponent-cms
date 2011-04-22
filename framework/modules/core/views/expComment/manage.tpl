{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div class="module expcomment manage">
    <h1>Manage Comments</h1>
    <p>The table below shows comments have not yet been approved.</p>
    
    <table class="exp-skin-table">
    <thead>
        <tr>
            {$page->header_columns}
            <th>&nbsp</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$page->records item=comment}
        <tr class="{cycle values="even, odd"}">
            <td>
                {if $comment->approved == 1}
                    <a href="{link action=approve_toggle id=$comment->id}" title="Disable this comment">
                        {img src=`$smarty.const.ICON_RELATIVE`toggle_on.gif}
                    </a>
                {else}
                    <a href="{link action=approve_toggle id=$comment->id}" title="Approve this comment">
                        {img src=`$smarty.const.ICON_RELATIVE`toggle_off.gif}
                    </a>   
                {/if}  
            </td>
            <td>{$comment->name}</td>
            <td>{$comment->body}</td>
            <td>
				<div class="item-actions">
					{icon class=edit action=approve record=$comment title="Edit Comment"}
					{icon action=delete record=$comment title="Delete Comment" onclick="return confirm('Are you sure you want to delete this comment?');"}
				</div>
            </td>
        </tr>
        {foreachelse}
        <tr><td>There are no comments awaiting approval</td></tr>
        {/foreach}
    </tbody>
    </table>        
</div>
