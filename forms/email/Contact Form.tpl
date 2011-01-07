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
<input type="hidden" name="id" value="{$id}" />
<table cellpadding="2" cellspacing="0" border="0">
<tr>
	<td width="10" style="width: 10px" valign="top" colspan="2"><b>{$_TR.submit_feedback}</b></td>
</tr>
<tr>
	<td valign="top">{$_TR.email}</td>
	<td>
		<input type="text" name="email" />
	</td>
</tr>
<tr>
	<td valign="top">{$_TR.subject}</td>
	<td>
		<input type="text" name="subject" />
	</td>
</tr>
<tr>
	<td valign="top">{$_TR.message}</td>
	<td>
		<textarea name="message"></textarea>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" value="Send" />
	</td>
</tr>
</table>
</form>
