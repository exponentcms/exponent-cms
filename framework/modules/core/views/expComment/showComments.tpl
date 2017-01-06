{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
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

{uniqueid prepend="comments" assign="name"}

{css unique="blog-comments" corecss="comments"}

{/css}

<div class="exp-comments">
	{if !$hidecomments && ($comments->records|@count > 0 || $config.usescomments!=1)}
	    <a id="exp-comments"></a>
	    {if $title}<h3>{$title}</h3>{/if}
        {if $comments->records|@count!=0}
            {permissions}
                {if $permissions.approve}
                    <div {if $unapproved > 0}class="unapproved msg-queue notice"{/if}>
                        <div class="msg">
                            {icon action=manage content_id=$content_id content_type=$content_type text='Manage'|gettext|cat:' '|cat:$type|cat:'s'}
                            {if $unapproved > 0}
                            | {'There are'|gettext} {$unapproved} {$type|plural:$unapproved} {'awaiting approval'|gettext}
                            {/if}
                        </div>
                    </div>
                {/if}
            {/permissions}
            <div class="comment-block">
                {$cmts = $comments->records}
                {function nestcomments depth=0}
                    <ul class="commentlist">
                        {foreach from=$cmts item=cmt name=comments}
                            <li class="comment">
                                <cite>
                                    <span class="attribution">
                                        {if $cmt->name != ''}
                                            {$cmt->name}
                                        {else}
                                            {$cmt->username}
                                        {/if}
                                        {if $depth}
                                            {'said in response to'|gettext} {$parentuser}
                                        {else}
                                            {'said'|gettext}
                                        {/if}
                                    </span>
                                    <span class="comment-date">{$cmt->created_at|relative_date}</span>
                                </cite>
                                <div class="comment-text">
                                    {if $cmt->avatar->image}
                                        {img src=$cmt->avatar->image h=40 class="avatar"}
                                    {else}
                                        {img src="`$smarty.const.PATH_RELATIVE`framework/modules/users/assets/images/avatar_not_found.jpg" h=40 class="avatar"}
                                    {/if}
                                    {permissions}
                                        <div class="item-actions">
                                            {if $permissions.edit}
                                                {icon action=edit record=$cmt content_id=$content_id content_type=$content_type title="Edit this"|gettext|cat:' '|cat:$type|lower}
                                            {/if}
                                            {if $permissions.delete}
                                                {icon action=delete record=$cmt title="Delete this"|gettext|cat:' '|cat:$type onclick="return confirm('"|cat:("Are you sure you want to delete this"|gettext)|cat:$type|cat:"?');"}
                                            {/if}
                                        </div>
                                    {/permissions}
                                    <div class="bodycopy">
                                        {if $depth == 0 && $ratings}
                                            {rating content_type=$content_type subtype="quality" label="Product Rating"|gettext content_id=$content_id readonly=1 user=$cmt->poster}
                                        {/if}
                                        {$cmt->body}
                                        {if $config.usescomments!=1 && !$config.disable_nested_comments && !$ratings}
                                            <div class="item-actions">
                                                <a class="comment-reply" title="{"Reply to this"|gettext|cat:' '|cat:$type}" onclick="EXPONENT.changeParent({$cmt->id},'{if $cmt->name != ''}{$cmt->name}{else}{$cmt->username}{/if}');" href="#commentinput">{'Reply'|gettext}</a>
                                            </div>
                                        {/if}
                                    </div>
                                    {if isset($cmt->children) && !$ratings}
                                        {nestcomments cmts=$cmt->children parentuser=$cmt->name depth=$depth+1}
                                    {/if}
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                {/function}
                {nestcomments cmt=$cmts}
            </div>
        {elseif $config.hidecomments==1}
            {permissions}
                <div class="hide-comments">
                    {$type}s {"have been disabled"|gettext}
                </div>
            {/permissions}
        {elseif $config.usescomments!=1}
            <div class="no-comments">
                {"No"|gettext} {$type} {"yet"|gettext}
            </div>
        {/if}
    	{*$comments->links <-- We need to fix pagination*}
	{/if}
	{if !$hideform && !$smarty.const.PRINTER_FRIENDLY && !$smarty.const.EXPORT_AS_PDF}
	    {exp_include file="edit.tpl"}
    {else}
    <p></p>
	{/if}
</div>

{script unique=$name yui3mods="node"}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        EXPONENT.changeParent = function(e,n) {
            Y.one('#body').focus();
            var labels = document.getElementsByTagName("label");
            var lookup = {};
            for (var i = 0; i < labels.length; i++) {
                lookup[labels[i].htmlFor] = labels[i];
            }
            Y.one('#parent_id').set('value', e);
            lookup['body'].innerHTML = "{/literal}{'Reply to'|gettext}{literal} "+n+"'s {/literal}{$type}{literal}";
        };
    });
{/literal}
{/script}
