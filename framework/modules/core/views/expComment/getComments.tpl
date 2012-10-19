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
 
{css unique="blog-comments" corecss="comments"}

{/css}

{uniqueid assign="id"}

<div class="exp-comments">
	{if !$hidecomments && ($comments->records|@count > 0 || $config.usescomments!=1)}
	    <a id="exp-comments"></a>
	    {if $title}<h3>{$title}</h3>{/if}
        {if $comments->records|@count!=0}
            {permissions}
                {if $permissions.approve == 1}
                    <div {if $unapproved > 0}class="unapproved msg-queue notice"{/if}>
                        <div class="msg">
                            {icon action=manage content_id=$content_id content_type=$content_type text='Manage Comments'|gettext}
                            {if $unapproved > 0}
                            | {'There are'|gettext} {$unapproved} {'comments awaiting approval'|gettext}
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
                                        {img src=$cmt->avatar->image h=40 style="margin:5px; float:left;"}
                                    {else}
                                        {img src="`$smarty.const.PATH_RELATIVE`framework/modules/users/assets/images/avatar_not_found.jpg" h=40 style="margin:5px; float:left;"}
                                    {/if}
                                    {permissions}
                                        <div class="item-actions">
                                            {if $permissions.manage == 1}
                                                {icon action=edit record=$cmt content_id=$content_id content_type=$content_type title="Edit this comment"|gettext}
                                            {/if}
                                            {if $permissions.delete == 1}
                                                {icon action=delete record=$cmt title="Delete this comment"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this comment?"|gettext)|cat:"');"}
                                            {/if}
                                        </div>
                                    {/permissions}
                                    <div class="bodycopy">
                                        {$cmt->body}
                                        {if $config.usescomments!=1 && !$config.disable_nested_comments}
                                            <a class="comment-reply" title="{"Reply to this comment"|gettext}" onclick="EXPONENT.changeParent({$cmt->id},'{if $cmt->name != ''}{$cmt->name}{else}{$cmt->username}{/if}');" href="#commentinput">{'Reply'|gettext}</a>
                                        {/if}
                                    </div>
                                    {if isset($cmt->children)}
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
            <div class="hide-comments">
                {"Comments have been disabled"|gettext}
            </div>
        {elseif $config.usescomments!=1}
            <div class="no-comments">
                {"No comments yet"|gettext}
            </div>
        {/if}
    	{*$comments->links* <-- We'll need to fix pagination*}
	{/if}
	{if $config.usescomments!=1 && !$smarty.const.PRINTER_FRIENDLY && !$smarty.const.EXPORT_AS_PDF}
	    {include file="edit.tpl"}
	{/if}
</div>

{script unique="`$id`" yui3mods="1"}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
        EXPONENT.changeParent = function(e,n) {
            Y.one('#body').focus();
            var labels = document.getElementsByTagName("label");
            var lookup = {};
            for (var i = 0; i < labels.length; i++) {
                lookup[labels[i].htmlFor] = labels[i];
            }
            Y.one('#parent_id').set('value', e);
            lookup['body'].innerHTML = "{/literal}{'Reply to'|gettext}{literal} "+n+"'s {/literal}{'Comment'|gettext}{literal}";
        };
    });
{/literal}
{/script}
