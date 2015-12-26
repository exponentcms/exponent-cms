{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="exp-comment edit">
	{if $formtitle}<h3>{$formtitle}</h3>{/if}
    {$config.commentinfo}
    {if $ratings}
        {rating content_type=$content_type subtype="quality" label="Product Rating"|gettext content_id=$content_id user=$user->id}{br}
    {/if}
    {*{if ($smarty.const.COMMENTS_REQUIRE_LOGIN == 1 && $user->id != 0) || $smarty.const.COMMENTS_REQUIRE_LOGIN == 0}*}
    {if ($require_login == 1 && $user->id != 0) || $require_login == 0}
    	{form action=update}
    		{control type=hidden name=id value=$comment->id}
            {control type=hidden name=parent_id value=$comment->parent_id}
    		{control type=hidden name=content_id value=$content_id}
    		{control type=hidden name=content_type value=$content_type}
            <div id="commentinput"></div>
    		{if $user->id == 0 || $comment->id }
    	        {control type=text name=name label="Name"|gettext required=true value=$comment->name required=1}
    		    {*{control type=text name=email label="Email"|gettext required=true value=$comment->email required=1}*}
                {control type=email name=email label="Email"|gettext required=true value=$comment->email required=1}
    		{else}
                {control type=text name=name disabled=1 label="Name"|gettext value="`$user->firstname` `$user->lastname`"}
        	    {*{control type=text name=email disabled=1 label="Email"|gettext value=$user->email}*}
                {control type=email name=email disabled=1 label="Email"|gettext value=$user->email}
    		{/if}
    		{*control type=text name=website label="Website" value=$comment->website*}
    		{*{control type=textarea name=body label="Your Comment"|gettext rows=6 cols=35 value=$comment->body}*}
            {control type="editor" name=body label="Your"|gettext|cat:' '|cat:$type value=$comment->body toolbar='basic'}
    		{control type="antispam"}
            {permissions}
                {if $permissions.approve}
                    <div class="item-actions">
                        {control type="checkbox" name="approved" label="Approve"|gettext|cat:' '|cat:$type value=1 checked=$comment->approved}
                    </div>
                {/if}
            {/permissions}
    		{control type=buttongroup submit="Submit"|gettext|cat:' '|cat:$type}
    	{/form}
	{else}
		<p>
            {icon class="login" controller=login action=loginredirect text="Log In to leave a"|gettext|cat:' '|cat:$type}
		</p>
	{/if}
</div>
