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
<div class="form_title">{'Profile Extensions'|gettext}</div>
<div class="form_header">{'Profile Extensions let users store more information in their profile.  Users will only be able to manage information governed by the active extensions listed below.'|gettext}</div>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
	<td class="header administration_header">{'Extension Name'|gettext}</td>
	<td class="header administration_header">&nbsp;</td>
</tr>
{foreach name=e from=$extensions item=extension}
{math equation="x+1" x=$extension->rank assign=nextrank}
{math equation="x-1" x=$extension->rank assign=prevrank}
	<tr class="row {cycle values='odd,even'}_row">
		<td>{$extension->name}</td>
		<td>
			<a class="mngmntlink administration_mngmntlink" href="{link action=profileext_delete id=$extension->id}" onclick="return confirm('{'Are you sure you want to deactivate this profile extension?'|gettext'Delete'|gettext}" alt="{'Delete'|gettext}" />
			</a>
			{if $smarty.foreach.e.first != 1}
			<a class="mngmntlink administration_mngmntlink" href="{link action=profileext_order a=$extension->rank b=$prevrank}">
				<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}up.png" title="{'Move Item Up'|gettext}" alt="{'Move Item Up'|gettext}" />
			</a>
			{else}
			<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}up.disabled.png" title="{''|gettext}" alt="{''|gettext}" />
			{/if}
			{if $smarty.foreach.e.last != 1}
			<a class="mngmntlink administration_mngmntlink" href="{link action=profileext_order a=$extension->rank b=$nextrank}">
				<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}down.png" title="{'Move Item Down'|gettext}" alt="{'Move Item Down'|gettext}" />
			</a>
			{else}
			<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}down.disabled.png" title="{''|gettext}" alt="{''|gettext}" />
			{/if}
		</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="2" align="center">
			<i>{'No Active Profile Extensions'|gettext}</i>
		</td>
	</tr>
{/foreach}
</table>
<br /><br />
<hr size="1" />
<div class="form_title">{'Inactive Profile Extensions'|gettext}</div>
<div class="form_header">{'The extensions listed below have been deactivated.  They will not show up as part of user profiles until they are activated.'|gettext}</div>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
	<td class="header administration_header">{'Extension Name'|gettext}</td>
	<td class="header administration_header">&nbsp;</td>
</tr>
{foreach from=$unused key=extclass item=extension}
<tr class="row {cycle values='odd,even'}_row">
	<td>{$extension->name}</td>
	<td>
		<a class="mngmntlink administration_mngmntlink" href="{link action=profileext_save ext=$extclass}">{'Activate'|gettext}</a>
		{if $extension->hasData == 1}
		<a class="mngmntlink administration_mngmntlink" href="{link action=profileext_clear ext=$extclass}">{'Clear Data'|gettext}</a>
		{else}
		{/if}
	</td>
</tr>
{foreachelse}
	<tr>
		<td colspan="2" align="center">
			<i>{'All Profile Extensions have been activated.'|gettext}</i>
		</td>
	</tr>
{/foreach}
</table>