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

{css unique="managecomments" corecss="tables"}

{/css}

<div class="module expcomment manage">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Managing Comments"|gettext) module="manage-comments"}
        </div>
        <h1>{"Manage Comments"|gettext}</h1>
    </div>
    {form name="bulk_process" action=bulk_process}
        {control type=hidden name=mod value=$page->model}
        {$page->links}
        <table class="exp-skin-table">
            <thead>
                <tr>
                    <th>
                        <input type='checkbox' name='checkallp' title="{'Select All/None'|gettext}" onchange="selectAllp(this.checked)">
                    </th>
                    {$page->header_columns}
                    <th>{'Actions'|gettext}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$page->records item=comment}
                    <tr class="{cycle values="even, odd"}">
                        <td>
                            {control type="checkbox" name="bulk_select[]" label=" " value=$comment->id}
                        </td>
                        <td>
                            {if $comment->approved == 1}
                                <a href="{link action=approve_toggle id=$comment->id content_type=$comment->content_type content_id=$comment->content_id}" title="Disable this comment"|gettext>
                                    <img src="{$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}">
                                </a>
                            {else}
                                <a href="{link action=approve_toggle id=$comment->id content_type=$comment->content_type content_id=$comment->content_id}" title="Approve this comment"|gettext>
                                    <img src="{$smarty.const.ICON_RELATIVE|cat:'toggle_off.png'}">
                                </a>
                            {/if}
                        </td>
                        <td>{$comment->name}</td>
                        <td>{$comment->body}</td>
                        <td><a nohref title="{$refs[$comment->content_type][$comment->content_id]}">{$comment->content_type|capitalize}</a></td>
                        <td>
                            <div class="item-actions">
                                {icon action=edit record=$comment content_id=$comment->content_id content_type=$comment->content_type title="Edit this comment"|gettext}
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
        <p>{'Select the item(s) to bulk process, then select the action below'|gettext}</p>
        {control type="radiogroup" name="command" label="Bulk Action to take:"|gettext items="Approve,Disable (dis-approve),Delete"|gettxtlist values="1,2,3"}
        {control type=buttongroup submit="Process Selected Items"|gettext cancel="Cancel"|gettext returntype="viewable" onclick=" && confirmdelete(this.form)"}
    {/form}
</div>

{script unique="manage-comments" yui3mods="1"}
    function selectAllp(val) {
        var checks = document.getElementsByName("bulk_select[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }

    function confirmdelete(thisform) {
        if (document.getElementById("command3").checked==true)
            return confirm("{'Are you sure you want to delete all selected comments?'|gettext}");
        else return true;
    }
{/script}