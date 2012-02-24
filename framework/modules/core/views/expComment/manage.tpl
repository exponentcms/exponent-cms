{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{css unique="managecomments" corecss="tables"}

{/css}

<div class="module expcomment manage">
    <h1>{"Manage Comments"|gettext}</h1>
    {$page->links}
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
                            <a href="{link action=approve_toggle id=$comment->id content_type=$content_type content_id=$content_id}" title="Disable this comment"|gettext>
                                <img src="{$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}">
                            </a>
                        {else}
                            <a href="{link action=approve_toggle id=$comment->id content_type=$content_type content_id=$content_id}" title="Approve this comment"|gettext>
                                <img src="{$smarty.const.ICON_RELATIVE|cat:'toggle_off.png'}">
                            </a>
                        {/if}
                    </td>
                    <td>{$comment->name}</td>
                    <td>{$comment->body}</td>
                    <td>
                        <div class="item-actions">
                            {icon action=edit record=$comment content_id=$content_id title="Edit this comment"|gettext}
                            {icon action=delete record=$comment title="Delete this comment"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this comment?"|gettext)|cat:"');"}
                        </div>
                    </td>
                </tr>
            {foreachelse}
                <tr><td>{'There are no comments awaiting approval'|gettext}</td></tr>
            {/foreach}
        </tbody>
    </table>        
    {$page->links}
</div>
