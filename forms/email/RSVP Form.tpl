{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
 * Written and Designed by James Hunt
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
<form method="post" action="">
<input type="hidden" name="module" value="{$module}"/>
<input type="hidden" name="formname" value="{$formname}"/>
<input type="hidden" name="action" value="{$action}"/>
<input type="hidden" name="src" value="{$loc->src}" />
<input type="hidden" name="msg" value="_Default" />
<input type="hidden" name="subject" value="RSVP Event Submisson" />
<input type="hidden" name="id" value="{$id}" />
<input type="hidden" name="eventtitle" value="{$item->title}" />
<input type="hidden" name="eventdate" value="{$item->eventstart|format_date:"%B %e, %Y, %l:%M %P"} - {$item->eventend|format_date:"%l:%M %P"}" />

<table cellpadding="2" cellspacing="0" border="0">
<tr>
	<td width="10" style="width: 10px" valign="top" colspan="2"><b>RSVP to This Event</b></td>
</tr>
<tr>
	<td valign="top">Your Name:</td>
	<td>
		<input type="text" name="name" />
	</td>
</tr>
<tr>
	<td valign="top">Your Email Address:</td>
	<td>
		<input type="text" name="email" />
	</td>
</tr>
<tr>
	<td valign="top">Your Phone:</td>
	<td>
		<input type="text" name="phone" />
	</td>
</tr>
<tr>
	<td valign="top">Number of Attendees:</td>
	<td>
		<input type="text" name="attendees" />
	</td>
</tr>

<tr>
	<td valign="top">Comments:</td>
	<td>
		<textarea name="Comments"></textarea>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" value="Send" />
	</td>
</tr>
</table>
</form>
