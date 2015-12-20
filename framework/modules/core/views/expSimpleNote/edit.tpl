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

<div class="module simplenote edit">
    {if ($require_login == 1 && $user->id != 0) || $require_login == 0}
		<div class="form_header">
			<h2>{$formtitle}</h2>
		</div>
		{form action=update}
			{control type=hidden name=id value=$simplenote->id}
			{control type=hidden name=content_id value=$content_id}
			{control type=hidden name=content_type value=$content_type}
			{control type=hidden name=tab value=$tab}
			{if $user->id == 0}
				{control type=text name=name label="Name (required)"|gettext value=$simplenote->id focus=1}
			{else}
				<strong>{'Name'|gettext}: {$user->firstname} {$user->lastname}</strong>{br}
			{/if}
			{if $user->id == 0}
				{control type=text name=email label="Email (required)"|gettext value=$simplenote->email}
			{else}
				<strong>{'Email:'|gettext} {$user->email}{br}
			{/if}
			{*{control type=textarea name=body label="Note"|gettext|cat:":" rows=6 cols=35 value=$simplenote->body}*}
			{control type="editor" name=body label="Note"|gettext value=$simplenote->body toolbar='basic'}
			{control type=buttongroup submit="Add Note"|gettext}
		{/form}
	{else}
	    <div class="form_header">
			<h2>{$formtitle}</h2>
			<p>
				{icon class="login" controller=login action=loginredirect text="You need to be logged in to add notes"|gettext}
			</p>
		</div>
	{/if}
</div>

