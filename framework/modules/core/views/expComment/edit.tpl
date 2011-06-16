{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

<div class="exp-comment edit bodycopy">
	{if $formtitle}<h3>{$formtitle}</h3>{/if}

    {$config.commentinfo}

    {if ($smarty.const.COMMENTS_REQUIRE_LOGIN == 1 && $user->id != 0) || $smarty.const.COMMENTS_REQUIRE_LOGIN == 0}    
    	{form action=update}
    		{control type=hidden name=id value=$comment->id}
    		{control type=hidden name=content_id value=$content_id}
    		{control type=hidden name=content_type value=$content_type}

    		{if $user->id == 0 || $comment->id }
    	        {control type=text name=name label="Name <span class=\"required\">*</span>" value=$comment->name required=1}
    		    {control type=text name=email label="Email <span class=\"required\">*</span>" value=$comment->email required=1}
    		{else}
                {control type=text name=name disabled=1 label="Name" value="`$user->firstname` `$user->lastname`"}
        	    {control type=text name=email disabled=1 label="Email" value=$user->email}
    		{/if}
            {permissions}
            {/permissions}
    		{*control type=text name=website label="Website" value=$comment->website*}
    		{control type=textarea name=body label="Your Comment" rows=6 cols=35 value=$comment->body}
    		{control type="antispam"}
            {if $permissions.approve}
    		    {control type="checkbox" name="approved" label="Approve Comment" value=1 checked=$comment->approved}
            {/if}
    		{control type=buttongroup submit="Submit Comment"}
    	{/form}
	{else}
		<p>
			<a href="{link module=loginmodule action=loginredirect}">{"Log In to leave a comment"|gettext}</a>
		</p> 
	{/if}
</div>

