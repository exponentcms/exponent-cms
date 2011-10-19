{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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
 
{css unique="standalone" corecss="tables"}

{/css}

<div class="navigationmodule manager-standalone">
	<div class="form_header">
		<p>{'Standalone pages do not appear in the site hierarchy, but still have their own content and act just like other pages.'|gettext}</p>
		<a class="add" href="{link action=edit_contentpage parent=-1}">{'Create a New Standalone Page'|gettext}</a>
	</div>

	<table cellpadding="2" cellspacing="0" border="0" width="100%" class="exp-skin-table">
    <thead>
        <tr>
    		<th><strong>{'Page Title'|gettext}</strong></th>
    		<th><strong>{'Actions'|gettext}</strong></th>
    		<th><strong>{'Permissions'|gettext}</strong></th>
    	</tr>
	</thead>
	<tbody>
	{foreach from=$sections item=section}

	<tr class="{cycle values='odd,even'}">
	<td>
		{if $section->active}
			<a href="{link section=$section->id}" class="navlink">{$section->name}</a>&nbsp;
		{else}
			{$section->name}&nbsp;
		{/if}
	</td><td>
		{icon class=edit action=edit_contentpage record=$section title='Edit'|gettext}
        {icon action=delete record=$section title='Delete'|gettext onclick="return confirm('Delete this page?');"}
	</td><td>
		{icon int=$section->id action=userperms _common=1 img='userperms.png' title='Assign user permissions for this page'|gettext text="User"}
		{icon int=$section->id action=groupperms _common=1 img='groupperms.png' title='Assign group permissions for this page'|gettext text="Group"}
	</td></tr>
	{foreachelse}
		<tr><td colspan=3><i>{'No standalone pages found'|gettext}</i></td></tr>
	{/foreach}
	</tbody>
	</table>
</div>
