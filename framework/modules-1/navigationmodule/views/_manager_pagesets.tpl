{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div class="navigationmodule manager-pagesets">
        <div class="form_header">
                <h1>{'Manage Pagesets'|gettext}</h1>
                <p>{'Pagesets are powerful tools to help you manage your site hierarchy.  A pageset is sort of like a sectional template layout - it allows you to define a commonly repeated structure as a miniature navigation hierarchy.  When you add a new section, you can set the page type to one of your Pagesets, and the sectional structure will be created for you, automatically.<br /><br />Another benefit of pagesets is default page content.  Any page in the page set can have modules on it, and the content of those modules is then copied to the newly created sections.'|gettext}</p>
		<a class="newpage" href="{link action=edit_template}">{'Create a New Pageset'|gettext}</a>
        </div>

	<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<th><strong>{'Pageset Title'|gettext}</strong></th>
		<th><strong>{'Actions'|gettext}</strong></th>
	</tr>
	{foreach from=$templates item=t}
	<tr class="row {cycle values='odd,even'}_row">
		<td style="padding-left: 10px">
			<b>{$t->name}</b>
		</td>
		<td>
			[ <a href="{link action=view_template id=$t->id}">{'View'|gettext}</a> ]
			[ <a href="{link action=edit_template id=$t->id}">{'Properties'|gettext}</a> ]
			[ <a href="{link action=delete_template id=$t->id}" onclick="return confirm('{'Are you sure you want to delete this template?'|gettext}');">{'Delete'|gettext}</a> ]
		</td>
	</tr>
	{foreachelse}
		<tr><td><i>{'No pagesets found'|gettext}</i></td></tr>
	{/foreach}
	</table>
</div>
