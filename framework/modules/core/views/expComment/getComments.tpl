{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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
 
 {css unique="blog-comments" link="`$smarty.const.PATH_RELATIVE`framework/modules/core/assets/comments.css" corecss="pagination"}
    
 {/css}
 
<div class="exp-comments">
	{if !$hidecomments && $comments|@count > 0}
	    {if $title}<h3>{$title}</h3>{/if}
	    
	    {if $unapproved > 0}
			<div class="unapproved">
				There are {$unapproved} comments awaiting approval.
				<a href="{link action=manage content_id=$content_id content_type=$content_type}">Click here to manage approvals</a>
			</div>
	    {/if}
	    
	    <ol class="commentlist">		
        {foreach from=$comments->records item=cmt name=comments}
			<li class="comment">
				<cite>
					<span class="attribution">
						{*<a href="{link controller=users action=user_profile id=$cmt->poster}">{$cmt->name}</a> *}
						{$cmt->name} says
					</span>
					<span class="comment-date">{$cmt->created_at|format_date:$smarty.const.DISPLAY_DATE_FORMAT}</span>
				</cite>
				<div class="comment-text bodycopy">	
					{*avatar userid=$cmt->poster w=100  <-- we'll get back to you*}
					
					{permissions}
						{permissions}
								<div class="item-actions">
								{if $permissions.manage == 1}
									{icon action=edit record=$cmt title="Edit Comment"}
								{/if}
								{if $permissions.delete == 1}
									{icon action=delete record=$cmt title="Delete Comment" onclick="return confirm('Are you sure you want to delete this comment?');"}
								{/if}
							<div>
						{/permissions}
					{/permissions}

					{$cmt->body}
				</div>
			</li>
    	{/foreach}
    	</ol>
    	{*$comments->links* <-- We'll need to fix pagination*}
	{/if}
	{if !$hideform}{include file="edit.tpl"}{/if}
</div>
