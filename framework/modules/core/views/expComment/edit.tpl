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

<div class="comment edit">
    {if ($smarty.const.COMMENTS_REQUIRE_LOGIN == 1 && $user->id != 0) || $smarty.const.COMMENTS_REQUIRE_LOGIN == 0}
	<div class="form_header">
    	<h1>{$formtitle}</h1>
		<p>
			If you feel like commenting on the above item, use the form below. Your email address will be used 
			for personal contact reasons only, and will not be shown on this website.
		</p> 
	</div>
	{if $user->id != 0}
		<p>
			You are posting this comment as {$user->firstname} {$user->lastname} - {$user->email}.  If this is not correct or 
			you would like to change this information you can <a href="{link module=userprofilemodule action=edit id=$user->id}">edit your profile.</a>
		</p>
	{/if}
	{form action=update}
		{control type=hidden name=id value=$comment->id}
		{control type=hidden name=content_id value=$content_id}
		{control type=hidden name=content_type value=$content_type}
		{if $user->id == 0}
		    {control type=text name=name label="Name (required)" value=$comment->id}
		{else}
		    <strong>Name: {$user->firstname} {$user->lastname}</strong>{br}
		{/if}
		{if $user->id == 0}
		    {control type=text name=email label="Email (required)" value=$comment->email}
		{else}
		    <strong>Email: {$user->email}</strong>{br}
		{/if}
		{*control type=text name=website label="Website" value=$comment->website*}
		{control type=textarea name=body label="Your Comment" rows=6 cols=35 value=$comment->body}
		{control type="antispam"}
		{control type=buttongroup submit="Submit Comment"}
	{/form}
	{else}
	    <div class="form_header">
    	<h1>{$formtitle}</h1>
		<p>
			If would like to post a comment you must first be logged in.  <a href="{link module=loginmodule action=loginredirect}">
			To login, click here.</a>
		</p> 
	</div>
	{/if}
</div>

