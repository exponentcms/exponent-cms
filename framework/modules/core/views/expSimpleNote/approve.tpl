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

<div class="module simplenote approve">
	<h1>Edit & Approve a Note</h1>
    <p>
        To approve a note just check the "Approve Note" checkbox and click the approve button below.&nbsp;&nbsp;
        If you need to edit the note before you approve it and let it go live, you can do that here as well.
    </p>
	{form action=approve_submit}
		{control type=hidden name=id value=$simplenote->id}
        {control type=hidden name=tab value=$tab}
		
	    <strong>Poster's Name: {$user->firstname} {$user->lastname}</strong>{br}
	    <strong>Poster's Email: {$user->email}{br}
		{control type=textarea name=body label="Note Body" rows=6 cols=35 value=$simplenote->body}
		{control type="checkbox" name="approved" label="Approve Note" value=1 checked=$simplenote->approved}
		{control type=buttongroup submit="Approve"}
	{/form}
</div>

