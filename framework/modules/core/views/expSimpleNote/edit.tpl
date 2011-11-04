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

<div class="module simplenote edit">
    {if ($require_login == 1 && $user->id != 0) || $require_login == 0}
	<div class="form_header">
    	<h1>{$formtitle}</h1>
	</div>
	{form action=update}
		{control type=hidden name=id value=$simplenote->id}
		{control type=hidden name=content_id value=$content_id}
		{control type=hidden name=content_type value=$content_type}
        {control type=hidden name=tab value=$tab}
		{if $user->id == 0}
		    {control type=text name=name label="Name (required)"|gettext value=$simplenote->id}
		{else}
		    <strong>Name: {$user->firstname} {$user->lastname}</strong>{br}
		{/if}
		{if $user->id == 0}
		    {control type=text name=email label="Email (required)"|gettext value=$simplenote->email}
		{else}
		    <strong>Email: {$user->email}{br}
		{/if}
		{control type=textarea name=body label="New Note"|gettext|cat:":" rows=6 cols=35 value=$simplenote->body}
		{control type=buttongroup submit="Add Note"|gettext}
	{/form}
	{else}
	    <div class="form_header">
    	<h1>{$formtitle}</h1>
		<p>
			<a href="{link controller=login action=loginredirect}">{'You need to be logged in to add notes'|gettext}.</a>
		</p> 
	</div>
	{/if}
</div>

