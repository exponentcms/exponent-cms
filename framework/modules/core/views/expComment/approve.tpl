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

<div class="module comment approve">
	<h1>{'Edit & Approve Comment'|gettext}</h1>
    <p>
        {'To approve a comment just check the \'Approve Comment\' checkbox and click the approve button below.'|gettext}&#160;&#160;
        {'If you need to edit the comment before you approve it and let it go live, you can do that here as well.'|gettext}
    </p>
	{form action=approve_submit}
		{control type=hidden name=id value=$comment->id}
		
	    <strong>{'Poster\'s Name'|gettext}: {$user->firstname} {$user->lastname}</strong>{br}
	    <strong>{'Poster\'s Email'|gettext}: {$user->email}{br}
		{control type=textarea name=body label="Comment Body"|gettext rows=6 cols=35 value=$comment->body}
		{control type="checkbox" name="approved" label="Approve Comment"|gettext value=1 checked=$comment->approved}
		{control type=buttongroup submit="Approve"|gettext}
	{/form}
</div>

